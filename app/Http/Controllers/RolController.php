<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolRequest;
use App\Permiso;
use App\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Rol $rol)
    {
        $this->authorize('view', [$rol, 'Rol']);

        return view('rol.index');
    }


    public function all(Rol $rol)
    {
        $this->authorize('view', [$rol, 'Rol']);

        $roles = Rol::all();


        return ['success' => true, 'roles' => $roles];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Rol $rol)
    {
        $this->authorize('create', [$rol, 'Rol']);

        return view('rol.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Rol $rol,RolRequest $request)
    {
        $this->authorize('create', [$rol, 'Rol']);

        DB::beginTransaction();

        try {
            $data = $request->all();

            $rol = Rol::create($data);

            $this->setPermisos($rol, $data['permisos']);

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.', 'exception' => $exception->getMessage()];

        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Rol $rol)
    {
        $this->authorize('view', [$rol, 'Rol']);

        return ['sucess' => true, 'rol' => $rol];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Rol $rol)
    {
        $this->authorize('update', [$rol, 'Rol']);

        return view('rol.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Rol $rol, RolRequest $request)
    {
        $this->authorize('update', [$rol, 'Rol']);

        DB::beginTransaction();

        try {
            $data = $request->all();
            $rol->update($data);
            $rol->permisos()->delete();

            $this->setPermisos($rol, $data['permisos']);

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.', 'exception' => $exception->getMessage()];

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rol $rol)
    {
        $this->authorize('delete', [$rol, 'Rol']);

        DB::beginTransaction();

        try {
            $rol->permisos()->delete();
            $rol->delete();

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.', 'exception'=>$exception->getMessage()];

        }
    }

    public function getPermisos()
    {
        $permisos = config('permissions');

        return ['success' => true, 'permisos' => $permisos];
    }

    public function setPermisos($rol, $indexes)
    {
        $permissions = config('permissions');

        foreach ($indexes as $index) {
            foreach ($permissions[$index]['models'] as $model) {
                foreach ($model['actions'] as $action) {
                    Permiso::create(
                        [
                            'rol_id' => $rol->id,
                            'model' => $model['name'],
                            'action' => $action
                        ]
                    );
                }
            }
        }
    }


}
