<?php

namespace Novius\LaravelNovaOrderNestedsetField\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @mixin Model&NodeTrait
 */
trait Orderable
{
    /**
     * Move item to previous position
     */
    public function moveOrderUp(): void
    {
        $prevItem = $this->getPrevSibling();
        if ($prevItem !== null) {
            /** @var NodeTrait $prevItem */
            $this->insertBeforeNode($prevItem);
        }
    }

    /**
     * Move item to next position
     */
    public function moveOrderDown(): void
    {
        $nextItem = $this->getNextSibling();
        if ($nextItem !== null) {
            /** @var NodeTrait $nextItem */
            $this->insertAfterNode($nextItem);
        }
    }

    public function buildSortQuery(): Builder
    {
        $query = static::query()->where($this->getParentIdName(), $this->getParentId());
        if (! empty($this->getScopeAttributes())) {
            foreach ($this->getScopeAttributes() as $attributeName) {
                if (! empty($this->{$attributeName})) {
                    $query->where($attributeName, $this->{$attributeName});
                }
            }
        }

        return $query;
    }

    public function scopeOrdered(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy($this->getLftName(), $direction);
    }

    public function getOrderableCachePrefix(): string
    {
        return sprintf('nova-order-nestedset-field.%s', md5(get_class($this).'-'.(int) $this->{$this->getParentIdName()}));
    }

    /**
     * Clear the cache corresponding to the model
     */
    public function clearOrderableCache(): void
    {
        Cache::forget($this->getOrderableCachePrefix().'.first');
        Cache::forget($this->getOrderableCachePrefix().'.last');
    }
}
