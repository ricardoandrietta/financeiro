<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProvisionDate extends Model
{
    public function provision()
    {
        return $this->belongsTo(Provision::class);
    }

    public function getProvisionDateBRAttribute()
    {
        return date('d/m/Y', strtotime($this->target_date));
    }
}
