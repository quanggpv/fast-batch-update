<?php

namespace Quangpv\BatchUpdate;

use Illuminate\Database\Eloquent\Model;

interface BatchUpdateInterface
{
    public function execute(Model $model, array $values, array $indexes = ['id']);
}
