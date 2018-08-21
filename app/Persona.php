<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = "persona";

    protected $fillable = [
        'ape_paterno',
        'ape_materno',
        'nombres',
        'dni',
        'telefono',
        'mail',
        'direccion',
        'state',
        'tipo_persona_id',
        'nivel_ciudadano_id',
        'urbanizacion_id'
    ];

    public function tipoPersona()
    {
        return $this->belongsTo(TipoPersona::class, "tipo_persona_id");
    }

    public function nivelCiudadano()
    {
        return $this->belongsTo(NivelCiudadano::class, "nivel_ciudadano_id");
    }

    public function estadoPersona()
    {
        return $this->belongsTo(EstadoPersona::class, "estado_persona_id");
    }

    public function urbanizacion()
    {
        return $this->belongsTo(Urbanizacion::class, "urbanizacion_id");
    }

    public function incidentes()
    {
        return $this->hasMany(Incidente::class, "persona_id", "id");
    }

    public function atencion_incidentes()
    {
        return $this->hasMany(AtencionIncidente::class, "persona_id", "id");
    }

    public function puntuacionesPersona()
    {
        return $this->hasMany(PuntuacionPersona::class, "persona_id", "id");
    }

    public function user()
    {
        return $this->hasOne(User::class, "persona_id");
    }

    public function scopeFilter($query, $value)
    {
        if(!empty($value))
        {
            $query->where('tipo_persona_id', $value);
        }
    }


}
