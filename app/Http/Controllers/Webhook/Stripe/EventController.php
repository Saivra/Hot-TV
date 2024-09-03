<?php

namespace App\Http\Controllers\Webhook\Stripe;

use App\Enums\User\StatusEnum;
use App\Http\Controllers\Controller;
use Botble\ACL\Models\User;
use Botble\SubscriptionOrder\Enums\OrderStatusEnum;
use Botble\SubscriptionPlan\Models\SubscriptionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Webhook;

class EventController extends Controller
{
    public function __invoke(Request $request)
    {
        Stripe::setApiKey(gs()->payment_stripe_secret);

        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $endpoint_secret = config('webhook-client.signing_secret'); //env('WEBHOOK_CLIENT_SECRET');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }

        switch($event->type) {
            case 'customer.subscription.created':
                $stripeSubscription = $event->data->object;
                $user = User::where('stripe_customer_id', $stripeSubscription['customer'])->first();
                $subscription = $user->subscription;
                $subscription->stripe_subscription_id = $stripeSubscription['id'];
                $subscription->save();
                Log::info(json_encode('New Subscription created: ' . $stripeSubscription['id']));
                break;
//            case 'checkout.session.completed':
//
//
//                $subscription = Subscription::retrieve($event->data->object['id']);
//
//                Log::info(json_encode($subscription));
//                break;

            case 'customer.subscription.deleted':

                $data = $event->data;
                $stripeSubscription = Subscription::retrieve($data->object['id']);

                $subscription = SubscriptionOrder::where('stripe_subscription_id', $stripeSubscription['id'])
                    ->where('current_subscription', true)
                    ->first();
                $user = $subscription->user;
                $user->status = StatusEnum::LOCKED->value;
                $user->save();
                Log::info(json_encode('New Subscription cancelled: ' . $stripeSubscription['id']));

                break;

            case 'invoice.payment_succeeded':

                $invoice = $event->data->object;
                if(!empty($invoice->subscription)) {
                    $subscriptionId = $invoice->subscription;
                    $amountPaid = $invoice->amount_paid;

                    $billingReason = $invoice->billing_reason;


                    $subscriptionOrder = SubscriptionOrder::where('stripe_subscription_id', $subscriptionId)
                        ->where('current_subscription', true)
                        ->first();

                    if($billingReason == 'subscription_create') {
                        if($subscriptionOrder && $subscriptionOrder->status != OrderStatusEnum::PAID->value) {
                            $subscriptionOrder->amount = $amountPaid / 100;
                            $subscriptionOrder->sub_total = $amountPaid / 100;
                            if($subscriptionOrder->status == OrderStatusEnum::PENDING->value) {
                                $subscriptionOrder->status = OrderStatusEnum::PAID->value;
                            }
                            $subscriptionOrder->save();
                            $user = $subscriptionOrder->user;
                            $user->status = StatusEnum::ACTIVATED->value;
                            $user->save();

                        }
                    } elseif($billingReason == 'subscription_cycle') {
                        $subscriptionOrder->current_subscription = 0;
                        if($subscriptionOrder->status == OrderStatusEnum::TRAIL->value) {
                            $subscriptionOrder->trail_ended_at = now();
                        }

                        $subscriptionOrder->save();
                        $user = $subscriptionOrder->user;

                        $newSubscriptionPayment = SubscriptionOrder::create([
                            'amount' => $amountPaid / 100,
                            'subscription_id' => $subscriptionId,
                            'user_id' => $user->id,
                            'payment_method_type' => 'stripe',
                            'session_id' => $subscriptionOrder->session_id,
                            'sub_total' => $amountPaid / 100,
                            'stripe_subscription_id' => $subscriptionOrder->stripe_subscription_id,
                            'status' => OrderStatusEnum::PAID->value,
                        ]);
                    }
                }
                break;

            case 'customer.subscription.trial_will_end':
                // Todo: send notification few days before trail ends

                break;
        }
    }
}
