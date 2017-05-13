<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $fillable = [
        'type', 'class', 'message', 'trace', 'extra'
    ];

    /**
     * @return \App\Models\ErrorLog
     */
    public static function log(\Exception $e, $data = [])
    {
        $log = [
            'class' => get_class($e),
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ];
        $log = array_merge($data, $log);
        $model = new self($log);
        $model->save();
        return $model;
    }
}
