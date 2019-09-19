<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Temperature extends Model
{
    protected $table = 'temperature';

    public function station()
    {
        return $this->hasOne(Station::class, 'id', 'id');
    }
}
