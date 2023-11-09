<?php

namespace Quangpv\BatchUpdate\DatabaseConnectors;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Quangpv\BatchUpdate\BatchUpdateInterface;

class PostgreSql implements BatchUpdateInterface
{
    /**
     * @param Model $model
     * @param array $values
     * @param array $indexes
     * @return int
     */
    public function execute(Model $model, array $values, array $indexes = ['id'])
    {
        if (count($values) <= 0) {
            return -1;
        }

        $tableName = $model->getTable();

        $tempTableFields = implode(",", array_keys($values[0]));

        $fieldKeys = array_keys($values[0]);

        $valuesArray = array_map(function ($item) use ($fieldKeys) {
            return '(' . implode(', ', array_map(function ($field) use ($item) {
                    return is_numeric($item[$field]) ? $item[$field] : "'" . $item[$field] . "'";
                }, $fieldKeys)) . ')';
        }, $values);

        $valuesString = implode(', ', $valuesArray);

        $records = $model::query()
            ->select("temp_data.*")
            ->join(
                DB::raw("(VALUES {$valuesString}) AS temp_data({$tempTableFields})"),
                function ($join) use ($indexes, $tableName) {
                    foreach ($indexes as $index) {
                        $join->on("temp_data.{$index}", '=', "{$tableName}.{$index}");
                    }
                }
            )
            ->getQuery()
            ->get()
            ->toArray();

        $records = array_map(function ($item) use ($indexes) {
            return implode(',', array_intersect_key((array) $item, array_flip($indexes)));
        }, $records);

        $values = array_filter($values, function ($val) use ($indexes, $records) {
            $valIndexes = array_intersect_key($val, array_flip($indexes));
            $valIndexesString = implode(',', $valIndexes);
            return in_array($valIndexesString, $records);
        });

        return $model->query()->upsert($values, $indexes);
    }
}
