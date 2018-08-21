<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_name',
        'admin',
        'state',
        'persona_id',
        'rol_id',
        'token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function persona()
    {
        return $this->belongsTo(Persona::class,'persona_id');
    }


    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'user_role','role_id','user_id');
    }

    public function getCreatedAtAttribute()
    {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at']);

        return $date->format('d/m/Y');
    }

    public function scopeFilterDni($query, $value)
    {
        if(!empty($value))
        {
            $query->join('persona', 'persona.id', '=', 'users.persona_id')
                    ->where('persona.dni',$value);
        }
    }
}
