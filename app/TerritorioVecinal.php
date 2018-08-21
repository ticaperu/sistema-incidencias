<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TerritorioVecinal extends Model
{
    protected $table = "territorio_vecinal";

    protected $fillable = [
        'descripcion',
        'coordenadas',
        'min_latitude',
        'max_latitude',
        'max_longitude',
        'min_longitude',
        'latitude',
        'longitude'
    ];

    public function urbanizaciones()
    {
        return $this->hasMany(Urbanizacion::class, "territorio_vecinal_id", "id");
    }

    public function getCoordenadasAttribute()
    {
        $data = explode(';', $this->attributes['coordenadas']);
        $final_data = [];
        foreach ($data as $item)
        {
            $item_final = explode(',', $item);
            $final_data[] = $item_final;

        }

        return $final_data;
    }


}
