<?php

namespace Novius\LaravelNovaOrderNestedsetField\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Orderable
{
    public function moveOrderUp()
    {
        $prevItem = $this->getPrevSibling();
        if (!empty($prevItem)) {
            $this->insertBeforeNode($prevItem);
        }
    }

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

    public function scopeOrdered(Builder $query, string $direction = 'asc')
    {
        return $query->orderBy($this->getLftName(), $direction);
    }
}
