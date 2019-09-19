<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{
    protected $table = 'flow';

    public function rain()
    {
        return $this->hasOne(Rain::class, 'date', 'date');
    }

    public function station()
    {
        return $this->hasOne(FlowStation::class , 'id', 'station_id');
    }
}
