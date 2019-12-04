<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Main extends Model
{
    protected $table = 'main';

    public function station()
    {
        return $this->hasOne(Station::class, 'id', 'id');

    }

    public function project()
    {
        return $this->hasOne(Project::class, 'Project_id', 'Project_id');

    }

    public function species()
    {
        return $this->hasOne(Species::class, 'scientific_name', 'scientific_name');
    }
}
