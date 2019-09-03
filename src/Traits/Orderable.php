<?php

namespace Novius\LaravelNovaOrderNestedsetField\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

trait Orderable
{
    /**
     * Move item to previous position
     */
    public function moveOrderUp()
    {
        $prevItem = $this->getPrevSibling();
        if (!empty($prevItem)) {
            $this->insertBeforeNode($prevItem);
        }
    }

    /**
     * Move item to next position
     */
    public function moveOrderDown()
    {
        $nextItem = $this->getNextSibling();
        if (!empty($nextItem)) {
            $this->insertAfterNode($nextItem);
        }
    }

    public function buildSortQuery()
    {
        $query = static::query()->where($this->getParentIdName(), $this->getParentId());
        if (!empty($this->getScopeAttributes())) {
            foreach ($this->getScopeAttributes() as $attributeName) {
                if (!empty($this->{$attributeName})) {
                    $query->where($attributeName, $this->{$attributeName});
                }
            }
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $direction
     * @return Builder
     */
    public function scopeOrdered(Builder $query, string $direction = 'asc')
    {
        return $query->orderBy($this->getLftName(), $direction);
    }

    /**
     * @return string
     */
    public function getOrderableCachePrefix(): string
    {
        return sprintf('nova-order-nestedset-field.%s', md5(get_class($this).'-'.(int) $this->{$this->getParentIdName()}));
    }

    /**
     * Clear the cache corresponding to the model
     */
    public function clearOrderableCache()
    {
        Cache::forget($this->getOrderableCachePrefix().'.first');
        Cache::forget($this->getOrderableCachePrefix().'.last');
    }
}
