<?php

namespace Botble\Tvshow\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Tvshow\Models\Tvshow;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Botble\Table\DataTables;

class TvshowTable extends TableAbstract
{
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, Tvshow $tvshow)
    {
        parent::__construct($table, $urlGenerator);

        $this->model = $tvshow;

        $this->hasActions = true;
        $this->hasFilter = true;

        if (!Auth::user()->hasAnyPermission(['tvshow.edit', 'tvshow.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function (Tvshow $item) {
                if (!Auth::user()->hasPermission('tvshow.edit')) {
                    return BaseHelper::clean($item->name);
                }
                return Html::link(route('tvshow.edit', $item->getKey()), BaseHelper::clean($item->name));
            })
            ->editColumn('checkbox', function (Tvshow $item) {
                return $this->getCheckbox($item->getKey());
            })
            ->editColumn('created_at', function (Tvshow $item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function (Tvshow $item) {
                return $item->status->toHtml();
            })
            ->addColumn('operations', function (Tvshow $item) {
                return $this->getOperations('tvshow.edit', 'tvshow.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
               'id',
               'name',
               'created_at',
               'status',
           ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'title' => trans('core/base::tables.name'),
                'class' => 'text-start',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('tvshow.create'), 'tvshow.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('tvshow.deletes'), 'tvshow.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'date',
            ],
        ];
    }

    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }
}
