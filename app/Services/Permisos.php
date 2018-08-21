<?php
/**
 * Created by PhpStorm.
 * User: icalvay
 * Date: 9/07/18
 * Time: 05:11
 */

namespace App\Services;


use App\Permiso;

class Permisos
{

    public function getMenu()
    {


        $menu = config('menu');

        $final_menu = [];

        foreach ($menu as $item)
        {

            $valid  = false;

            if (isset($item['routes']) && count($item['routes']) > 0){

                $routes = [];

                foreach ($item['routes'] as $route)
                {
                    $valid_route = $this->checkPermiso($route['model'], $route['action']);

                    if($valid_route){
                        $route['url'] = route($route['route']);
                        $routes[] = $route;
                    }
                }

                if(count($routes) > 0){
                    $valid = true;
                    $item['routes'] = $routes;
                }

            }else{
                $valid = $this->checkPermiso($item['model'], $item['action']);
                $item['url'] = route($item['route']);
            }

            if($valid){
                $final_menu[]  = $item;
            }

        }

        return $final_menu;

    }


    public function checkPermiso($model_name, $action)
    {
        $user = auth()->user();

        if($user->admin == 1)
        {
            return true;
        }

        $permission = Permiso::where('rol_id', $user->rol_id)->where('model', $model_name)->get()->toArray();

        $key = array_search($action, array_column($permission, 'action'));

        if(is_numeric($key))
        {
            return true;
        }

        return false;
    }

}