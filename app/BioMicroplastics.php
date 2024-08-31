<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BioMicroplastics extends Model
{
    protected $table = 'microplastic_bio';

    public function station(): HasOne
    {
        return $this->hasOne(Station::class, 'id', 'sid');
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class, 'Project_id', 'project_id');

    }
}
