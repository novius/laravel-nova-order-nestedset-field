<?php

namespace Novius\LaravelNovaOrderNestedsetField;

use Illuminate\Database\Eloquent\Model;
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

    protected function resolveAttribute($resource, string $attribute): mixed
    {
        /** @var Model&Orderable&NodeTrait $resource */
        if (! in_array(Orderable::class, class_uses_recursive($resource), true)) {
            abort(500, trans('nova-order-nestedset-field::errors.model_should_use_trait', [
                'class' => Orderable::class,
                'model' => get_class($resource),
            ]));
        }

        if (! in_array(NodeTrait::class, class_uses_recursive($resource), true)) {
            abort(500, trans('nova-order-nestedset-field::errors.model_should_use_trait', [
                'class' => NodeTrait::class,
                'model' => get_class($resource),
            ]));
        }

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            $cachePrefix = $resource->getOrderableCachePrefix();
            $firstId = Cache::rememberForever($cachePrefix.'.first', static function () use ($resource) {
                return $resource->buildSortQuery()->ordered()->value($resource->getKeyName());
            });
            $lastId = Cache::rememberForever($cachePrefix.'.last', static function () use ($resource) {
                return $resource->buildSortQuery()->ordered('desc')->value($resource->getKeyName());
            });
        } else {
            $firstId = $resource->buildSortQuery()->ordered()->value($resource->getKeyName());
            $lastId = $resource->buildSortQuery()->ordered('desc')->value($resource->getKeyName());
        }

        $this->withMeta([
            'first' => $firstId instanceof $resource ? $firstId->id : $firstId,
            'last' => $lastId instanceof $resource ? $lastId->id : $lastId,
        ]);

        return data_get($resource, $attribute);
    }
}
