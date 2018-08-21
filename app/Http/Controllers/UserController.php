<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $this->authorize('view', [$user, 'User']);

        return view('user.index');
    }


    public function all(User $user, Request $request)
    {
        $dni = $request->get('dni');
        $this->authorize('view', [$user, 'User']);  
        $users = User::select('users.name','users.id',
            'users.email',
            'users.password',
            'users.last_name',
            'users.admin',
            'users.state',
            'users.persona_id',
            'users.rol_id',
            'users.token')->with(['roles'])
            ->filterDni($dni)
            ->get();
        return ['success'=>true , 'users'=>$users];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user)
    {
        $this->authorize('create', [$user, 'User']);

        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(User $user,UserRequest $request)
    {
        $this->authorize('create', [$user, 'User']);

        DB::beginTransaction();

        try {
            $data = $request->all();
            $password = $data['password'];
            $data['password'] = bcrypt($password);

            $user = User::create($data);

            foreach ($data['roles'] as $datum)
            {
                $user->roles()->attach($datum['id']);
            }

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.', 'exception'=>$exception->getMessage()];

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('view', [$user, 'User']);

        $user->persona;
        $user->roles;

        return ['sucess'=>true, 'user'=>$user];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $this->authorize('update', [$user, 'User']);

        return view('user.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, UserRequest $request)
    {
        $this->authorize('update', [$user, 'User']);
            
        DB::beginTransaction();

        try {

            $data = $request->all();
            $password = $request->input("password");
            $data['password'] = bcrypt($password);

<<<<<<< HEAD
            
            $user->update($data);
=======
            $user->roles()->detach();

            foreach ($data['roles'] as $datum)
            {
                $user->roles()->attach($datum['id']);
            }

>>>>>>> 81b17ccded470c0e3dcca579f84220c6f463848c
            DB::commit();
            return ['success' => true, 'message' => 'El usuario fue actualizado correctamente'];

        } catch (\Exception $exception) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.', 'exception'=>$exception->getMessage()];
        } 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', [$user, 'User']);

        DB::beginTransaction();

        try {

            $user->delete();

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.'];

        }
    }

    /** -- api -- */
    public function getUsers()
    {
        $users = User::get();
        return response()->json($users);
    }

    public function getUserById($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }
}
