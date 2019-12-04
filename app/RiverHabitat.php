<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RiverHabitat extends Model
{
    protected $table = 'river_habitat';

    public function morphology()
    {
        return $this->hasOne('river_morphology', 'morphology', 'river_morphology.morphology_id');
    }
}
