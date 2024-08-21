<?php

use App\Http\Controllers\PaymentVerificationController;
use App\Http\Controllers\PaypalPaymentVerificationController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\VideoStreamOld2Controller;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\Customer\Auth\Registration\StripeCheckoutController;

Route::get('jsdjsjjs', function(){
    return 444;
})->name('public.index');

Route::get('public/cart', function(){
    return 444;
})->name('public.cart');

Route::get('public/wishlist', function(){
    return 444;
})->name('public.wishlist');

Route::get('public/compare', function(){
    return 444;
})->name('public.compare');


Route::get('/video/{section}/{id}', [VideoStreamOld2Controller::class, 'videoContent'])->name('video-stream');
//Route::post('/video/save-played-time', [VideoStreamController::class, 'savePlayedTime'])->name('save-played-time');

Route::get('cart/payment-verification',PaymentVerificationController::class)->name('payment-verification');
Route::get('subscribe/stripe-checkout', function() {
    dd('testing');
})->name('stripe-checkout');


Route::group(['namespace' => "App\Http\Livewire"],function () {


    Route::get('logout', "Auth\Login@logout")->name('logout');
    Route::get('faqs',"Faqs")->name('faqs');
    Route::get('privacy-policy',"PrivacyPolicy")->name('privacy_policy');

    Route::group(['namespace' => 'Cart', 'as' => 'cart.', 'prefix' => 'cart'], function() {
        Route::get('/',"Home")->name('home');
        Route::get('checkout',"Checkout")->name('checkout');

    });
    Route::get('about',"About")->name('about');
    Route::get('contact',"Contact")->name('contact');

    Route::get('terms-and-condition',"Terms")->name('terms');
    Route::get('s',"Search")->name('search');

    Route::group(['middleware'=> []], function() {

        Route::get('/',"Home\Home")->name('home');

        Route::group(['namespace' => 'Auth', 'middleware' => ['UserAuth']], function() {
            Route::get('login',"Login")->name('login');
            Route::get('forgot-password',"ForgotPassword")->name('forgot_password');
            Route::get('reset-password',"ResetPassword")->name('reset_password');
            Route::get('signup',"Register")->name('register');
        });

        Route::group(['namespace' => 'LiveChannel'], function() {
            Route::get('live-channel',"Show")->name('live-channel.show');
        });

        Route::group(['namespace' => 'TvChannel'], function() {
            Route::get('tv-channel/{slug}',"Show")->name('tv-channel.show');
        });

        Route::group(['namespace' => 'Podcast'], function() {
            Route::get('podcast',"Home")->name('podcast.home');
            Route::get('podcast/{slug}',"Show")->name('podcast.show');
        });

        Route::group(['namespace' => 'TvShows', 'prefix' => 'tv-shows'], function() {
            Route::get('/',"Home")->name('tv-shows.home');
            Route::get('{slug}',"Show")->name('tv-shows.show');
        });

        Route::group(['namespace' => 'Product', 'prefix' => 'products', 'as' => 'merchandize.'], function() {
            Route::get('/',"Home")->name('home');
            Route::get('{slug}',"Show")->name('show');
        });

        Route::group(['namespace' => 'PedicabStream'], function() {
            Route::get('pedicab-streams',"Home")->name('pedicab-streams.home');
        });

        Route::group(['namespace' => 'Testimonials'], function() {
            Route::get('testimonials',"Home")->name('testimonials.home');
        });

        Route::group(['namespace' => 'ShoutOuts'], function() {
            Route::get('celebrity-shoutout',"Home")->name('celebrity-shoutout.home');
            Route::get('{slug}',"Show")->name('shoutout.show');
        });

        Route::group(['namespace' => 'Gallery'], function() {
            Route::get('gallery',"Home")->name('gallery.home');
        });

        Route::group(['namespace' => 'Blog', 'as' => 'blog.', 'prefix' => 'blog'], function() {
            Route::get('/',"Home")->name('home');
            Route::get('{slug}',"Show")->name('show');
        });

        Route::group(['namespace' => 'Pricing', 'prefix' => 'pricing', 'as' => 'pricing.'], function() {
            Route::get('/pricing',"Home")->name('home');
        });

        Route::group(['namespace' => 'Cart', 'prefix' => 'cart', 'as' => 'cart.'], function() {
            Route::get('order-completed', 'OrderCompleted')->name('order-completed');
        });


    });
});

Route::group(['prefix' => 'paypal', 'as' => 'paypal.'], function() {
    Route::get('payment-cancel', [PaypalPaymentVerificationController::class, 'cancel'])->name('payment.cancel');
    Route::get('payment-success', [PaypalPaymentVerificationController::class, 'paymentSuccess'])->name('payment.success');
});






