<?php

namespace Novius\LaravelNovaOrderNestedsetField;

use Illuminate\Support\Facades\Cache;
use Kalnoy\Nestedset\NodeTrait;
use Laravel\Nova\Fields\Field;
use Novius\LaravelNovaOrderNestedsetField\Traits\Orderable;

class OrderNestedsetField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'order-nestedset-field';

    /**
     * @var bool
     */
    public $showOnDetail = false;

    /**
     * @var bool
     */
    public $showOnCreation = false;

    /**
     * @var bool
     */
    public $showOnUpdate = false;

    protected function resolveAttribute($resource, $attribute)
    {
        if (!in_array(Orderable::class, class_uses_recursive($resource))) {
            abort(500, trans('nova-order-nestedset-field::errors.model_should_use_trait', [
                'class' => Orderable::class,
                'model' => get_class($resource),
            ]));
        }

        if (!in_array(NodeTrait::class, class_uses_recursive($resource))) {
            abort(500, trans('nova-order-nestedset-field::errors.model_should_use_trait', [
                'class' => NodeTrait::class,
                'model' => get_class($resource),
            ]));
        }

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $cachePrefix = $resource->getOrderableCachePrefix();
            $first = Cache::rememberForever($cachePrefix.'.first', function () use ($resource) {
                return $resource->buildSortQuery()->ordered()->first();
            });
            $last = Cache::rememberForever($cachePrefix.'.last', function () use ($resource) {
                return $resource->buildSortQuery()->ordered('desc')->first();
            });
        } else {
            $first = $resource->buildSortQuery()->ordered()->first();
            $last = $resource->buildSortQuery()->ordered('desc')->first();
        }

        $this->withMeta([
            'first' => is_null($first) ? null : $first->id,
            'last' => is_null($last) ? null : $last->id,
        ]);

        return data_get($resource, $attribute);
    }
}
