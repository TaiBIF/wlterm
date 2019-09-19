<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WaterQuality extends Model
{
    protected $table = 'water';

    public function station()
    {
        return $this->hasOne(Station::class, 'id', 'id');

    }

    public function project()
    {
        return $this->hasOne(Project::class, 'Project_id', 'Project_id');

    }
}
