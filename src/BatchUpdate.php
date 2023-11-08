<?php

namespace Quangpv\BatchUpdate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BatchUpdate implements BatchUpdateInterface
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

        $rowValues = [];
        foreach ($values as $item) {
            $rowValues[] = "ROW(?, ?, ?)";
        }
        $rowValues = implode(',', $rowValues);

        $tableName = $model->getTable();
        $fields = array_map(function ($item) {
            return "'$item'";
        }, $values[0]);

        $valuesBinding = [];
        foreach ($values as $item) {
            $temp = [];
            foreach ($fields as $key => $field) {
                $temp[] = $item[$key];
            }
            $valuesBinding = array_merge($valuesBinding, $temp);
        }

        $tempTableFields = implode(",", array_keys($values[0]));

        $records = $model::query()
            ->select("temp_data.*")
            ->join(
                DB::raw("
                (SELECT *
                    FROM (
                        VALUES
                                {$rowValues}
                    ) AS temp_table({$tempTableFields})
                ) as temp_data"),
                function ($join) use ($indexes, $tableName) {
                    foreach ($indexes as $index) {
                        $join->on("temp_data.{$index}", '=', "{$tableName}.{$index}");
                    }
                }
            )
            ->setBindings($valuesBinding, 'join')
            ->getQuery()
            ->get()
            ->toArray();

        $records = array_map(function ($item) use ($indexes) {
            // return collect($item)->only($indexes)->toArray();
            return implode(',', array_intersect_key((array) $item, array_flip($indexes)));
        }, $records);

        $values = array_filter($values, function ($val) use ($indexes, $records) {
            // collect($val)->only($indexes)->toArray();
            $valIndexes = array_intersect_key($val, array_flip($indexes));
            $valIndexesString = implode(',', $valIndexes);
            return in_array($valIndexesString, $records);
        });

        return $model->query()->upsert($values);
    }
}
