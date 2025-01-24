<?php

namespace Novius\LaravelNovaOrderNestedsetField\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;
use Kalnoy\Nestedset\NodeTrait;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelNovaOrderNestedsetField\Traits\Orderable;

class PositionController extends Controller
{
    public function __invoke(NovaRequest $request)
    {
        $resourceId = $request->get('resourceId');
        /** @var Model&Orderable $model */
        $model = $request->findModelOrFail($resourceId);

        if (! in_array(Orderable::class, class_uses_recursive($model), true)) {
            abort(500, trans('nova-order-nestedset-field::errors.model_should_use_trait', [
                'class' => Orderable::class,
                'model' => get_class($model),
            ]));
        }

        if (! in_array(NodeTrait::class, class_uses_recursive($model), true)) {
            abort(500, trans('nova-order-nestedset-field::errors.model_should_use_trait', [
                'class' => NodeTrait::class,
                'model' => get_class($model),
            ]));
        }

        $direction = (string) $request->get('direction', '');
        if (! in_array($direction, ['up', 'down'])) {
            abort(500, trans('nova-order-nestedset-field::errors.bad_direction'));
        }

        if ($direction === 'up') {
            $model->moveOrderUp();
        } else {
            $model->moveOrderDown();
        }
    }
}
