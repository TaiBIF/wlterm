<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alage extends Model
{
    protected $table = 'alage';

    public function station()
    {
        return $this->hasOne(Station::class, 'id', 'id');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'Project_id', 'Project_id');

    }
}
