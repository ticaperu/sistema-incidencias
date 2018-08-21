<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComiteGestionRequest;
use Illuminate\Http\Request;
use App\ComiteGestion;
use Illuminate\Support\Facades\DB;

class ComiteGestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ComiteGestion $comiteGestion)
    {
        $this->authorize('view', [$comiteGestion, 'ComiteGestion']);

        return view('comite-gestion.index');
    }


    public function all(ComiteGestion $comiteGestion)
    {
        $this->authorize('view', [$comiteGestion, 'ComiteGestion']);

        $comitesGestion = ComiteGestion::with(['persona', 'territorioVecinal'])->get();


        return ['success'=>true , 'comitesGestion'=>$comitesGestion];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(ComiteGestion $comiteGestion)
    {
        $this->authorize('create', [$comiteGestion, 'ComiteGestion']);

        return view('comite-gestion.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ComiteGestion $comiteGestion,ComiteGestionRequest $request)
    {
        $this->authorize('create', [$comiteGestion, 'ComiteGestion']);

        DB::beginTransaction();

        try {
            $data = $request->all();

            ComiteGestion::create($data);

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
    public function show(ComiteGestion $comiteGestion)
    {
        $this->authorize('view', [$comiteGestion, 'ComiteGestion']);

        $comiteGestion->persona;
        return ['sucess'=>true, 'comiteGestion'=>$comiteGestion];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ComiteGestion $comiteGestion)
    {
        $this->authorize('update', [$comiteGestion, 'ComiteGestion']);

        return view('comite-gestion.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ComiteGestion $comiteGestion, ComiteGestionRequest $request)
    {
        $this->authorize('update', [$comiteGestion, 'ComiteGestion']);

        DB::beginTransaction();

        try {
            $data = $request->all();
            $comiteGestion->update($data);

            DB::commit();

            return ['success' => true];

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
    public function destroy(ComiteGestion $comiteGestion)
    {
        $this->authorize('delete', [$comiteGestion, 'ComiteGestion']);

        DB::beginTransaction();

        try {

            $comiteGestion->delete();

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.'];

        }
    }
    
    
}
