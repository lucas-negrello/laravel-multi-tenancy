<?php

namespace App\Traits;

trait HasRegisters
{
    protected static function bootHasRegisters(): void
    {
        static::creating(function ($model) {
            if (request()->user()) {
                if (in_array('created_by', $model->getFillable()) && empty($model->created_by)) {
                    $model->created_by = request()->user()->id;
                }
                if (in_array('updated_by', $model->getFillable()) && empty($model->updated_by)) {
                    $model->updated_by = request()->user()->id;
                }
            }
        });

        static::updating(function ($model) {
            if (request()->user()) {
                if (in_array('updated_by', $model->getFillable())) {
                    $model->updated_by = request()->user()->id;
                }
            }
        });
    }
}
