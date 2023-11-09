<?php

namespace Quangpv\BatchUpdate;

use Illuminate\Database\Eloquent\Model;

class BatchUpdate
{
    public $batch;

    public function __construct(BatchUpdateInterface $batch)
    {
        $this->batch = $batch;
    }


    /**
     * @param Model $model
     * @param array $values
     * @param array $indexes
     * @param array $updateFields
     * @return int
     */
    public function execute(Model $model, array $values, array $indexes = ['id'], array $updateFields = [])
    {
        return $this->batch->execute($model, $values, $indexes, $updateFields);
    }
}
