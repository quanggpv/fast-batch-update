<?php

namespace Quangpv\BatchUpdate\DatabaseConnectors;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Quangpv\BatchUpdate\BatchUpdateInterface;

class MariaDB implements BatchUpdateInterface
{
    /**
     * @param Model $model
     * @param array $values
     * @param array $indexes
     * @return int
     */
    public function execute(Model $model, array $values, array $indexes = ['id'], $updateFields = [])
    {
        if (count($values) <= 0) {
            return -1;
        }

        $fieldKeys = array_keys($values[0]);

        $valuesArray = array_map(function ($item) use ($fieldKeys) {
            return '(' . implode(', ', array_map(function ($field) use ($item) {
                    return is_numeric($item[$field]) ? $item[$field] : "'" . $item[$field] . "'";
                }, $fieldKeys)) . ')';
        }, $values);

        $valuesString = implode(',', $valuesArray);

        $fieldsString = implode(',', $fieldKeys);

        $tableName = $model->getTable();

        $onConditions = [];

        foreach ($indexes as $index) {
            $onConditions[] = "temp_data.{$index} = {$tableName}.{$index}";
        }

        $onConditionsString = implode(' AND ', $onConditions);

        $sql = "
            WITH temp_data ({$fieldsString}) AS (
                VALUES {$valuesString}
            )

            select temp_data.*
            from temp_data
            join {$tableName}
            on {$onConditionsString}
        ";

        $records = DB::select(DB::raw($sql));

        $records = array_map(function ($item) use ($indexes) {
            return implode(',', array_intersect_key((array) $item, array_flip($indexes)));
        }, $records);

        $values = array_filter($values, function ($val) use ($indexes, $records) {
            $valIndexes = array_intersect_key($val, array_flip($indexes));
            $valIndexesString = implode(',', $valIndexes);
            return in_array($valIndexesString, $records);
        });

        $model->query()->upsert($values, $indexes, $updateFields);
    }
}
