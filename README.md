# Nova Order Field nestedset
[![Travis](https://img.shields.io/travis/novius/laravel-nova-order-nestedset-field.svg?maxAge=1800&style=flat-square)](https://travis-ci.org/novius/laravel-nova-order-nestedset-field)
[![Packagist Release](https://img.shields.io/packagist/v/novius/laravel-nova-order-nestedset-field.svg?maxAge=1800&style=flat-square)](https://packagist.org/packages/novius/laravel-nova-order-nestedset-field)
[![Licence](https://img.shields.io/packagist/l/novius/laravel-nova-order-nestedset-field.svg?maxAge=1800&style=flat-square)](https://github.com/novius/laravel-nova-order-nestedset-field#licence)

A field that make your resources orderable using [the laravel nestedset package](https://github.com/lazychaser/laravel-nestedset).

## Requirements

* PHP >= 7.4
* Laravel Nova >= 3.0
* Laravel Framework >= 8.43

> **NOTE**: These instructions are for Laravel >= 8.43. If you are using prior version, please
> see the [previous version's docs](https://github.com/novius/laravel-nova-order-nestedset-field/tree/1.x).

## Installation

```sh
composer require novius/laravel-nova-order-nestedset-field
```

### Configuration

Some options that you can override are available.

```sh
php artisan vendor:publish --provider="Novius\LaravelNovaOrderNestedsetField\OrderNestedsetFieldServiceProvider" --tag="config"
```

## Usage

**Step 1**

Use Kalnoy\Nestedset `NodeTrait` and Novius\LaravelNovaOrderNestedsetField `Orderable` trait on your model. 

Example :

```php
use Kalnoy\Nestedset\NodeTrait;
use Novius\LaravelNovaOrderNestedsetField\Traits\Orderable;

class Foo extends Model {
    use NodeTrait;
    use Orderable;
    
    public function getLftName()
    {
        return 'left';
    }
    
    public function getRgtName()
    {
        return 'right';
    }
    
    public function getParentIdName()
    {
        return 'parent';
    }
}

```

**Step 2**
 
Add the field to your resource and specify order for your resources.


```php
use Novius\LaravelNovaOrderNestedsetField\OrderNestedsetField;

class FooResource extends Resource
{       
    public function fields(Request $request)
    {
        return [
            OrderNestedsetField::make('Order'),
        ];
    }
    
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $orderings
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyOrderings($query, array $orderings)
    {
        return $query->orderBy('left', 'asc');
    }
}

```

**Scoping**

Imagine you have `Menu` model and `MenuItems`. There is a one-to-many relationship
set up between these models. `MenuItem` has `menu_id` attribute for joining models
together. `MenuItem` incorporates nested sets. It is obvious that you would want to
process each tree separately based on `menu_id` attribute. In order to do so, you
need to specify this attribute as scope attribute:

```php
    protected function getScopeAttributes()
    {
        return ['menu_id'];
    }
```

[Retrieve more information about usage on official doc](https://github.com/lazychaser/laravel-nestedset#scoping).

## Performances

You can enable cache to avoid performance issues in case of large tree.

**By default cache is disabled.**

To use cache you have to enabled it in config file with :

```php
return [
    ...

    'cache_enabled' => true,
];
```

**You have to clear cache on every tree updates with an observer on your Model (or directly in boot method).**

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Novius\LaravelNovaOrderNestedsetField\Traits\Orderable;

class Foo extends Model 
{
    use NodeTrait;
    use Orderable;

    public static function boot()
    {
        parent::boot();

        if (config('nova-order-nestedset-field.cache_enabled', false)) {
            static::created(function (Theme $model) {
                $model->clearOrderableCache();
            });

            static::updated(function (Theme $model) {
                $model->clearOrderableCache();
            });

            static::deleted(function (Theme $model) {
                $model->clearOrderableCache();
            });
        }
    }
}
```

## Override default languages files

Run:

```sh
php artisan vendor:publish --provider="Novius\LaravelNovaOrderNestedsetField\OrderNestedsetFieldServiceProvider" --tag="lang"
```

## Lint

Run php-cs with:

```sh
composer run-script lint
```

## Contributing

Contributions are welcome!
Leave an issue on Github, or create a Pull Request.


## Licence

This package is under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.
