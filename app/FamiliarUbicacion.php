<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FamiliarUbicacion extends Model
{
    protected $table = "familiar_ubicacion";

    protected $fillable = [
        "familiar_id",
        "fecha",
        "latitude",
        "longitude",
        "descripcion"
    ];

    
    public function familiar()
    {
        return $this->belongsTo(Familiar::class, "familiar_id");
    }
}
