<?php

namespace App\Policies;

use App\Permiso;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ACLPolicy
{
    use HandlesAuthorization;

    public function view(User $user, $model, $model_name)
    {
        return $this->checkPermission($user, $model_name);
    }

    public function create(User $user, $model, $model_name)
    {
        return $this->checkPermission($user, $model_name);
    }


    public function update(User $user, $model, $model_name)
    {
        return $this->checkPermission($user, $model_name);
    }


    public function delete(User $user, $model, $model_name)
    {
        return $this->checkPermission($user, $model_name);
    }

    public function attention(User $user, $model, $model_name)
    {
        return $this->checkPermission($user, $model_name);
    }

    public function active(User $user, $model, $model_name)
    {
        return $this->checkPermission($user, $model_name);
    }


    public function checkPermission($user, $model_name)
    {
        if($user->admin == 1)
        {
            return true;
        }

        $roles = $user->roles()->pluck('id');

        $permission = Permiso::whereIn('rol_id', $roles)->where('model', $model_name)->get()->toArray();

        $key = array_search('view', array_column($permission, 'action'));

        if(is_numeric($key))
        {
            return true;
        }

        return false;

    }
}
