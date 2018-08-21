<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoPersonaRequest;
use App\TipoPersona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoPersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TipoPersona $tipoPersona)
    {
        $this->authorize('view', [$tipoPersona, 'TipoPersona']);

        return view('tipo-persona.index');
    }


    public function all(TipoPersona $tipoPersona)
    {
        $this->authorize('view', [$tipoPersona, 'TipoPersona']);

        $tiposPersonas = TipoPersona::all();


        return ['success'=>true , 'tiposPersonas'=>$tiposPersonas];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(TipoPersona $tipoPersona)
    {
        $this->authorize('create', [$tipoPersona, 'TipoPersona']);

        return view('tipo-persona.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(TipoPersona $tipoPersona, TipoPersonaRequest $request)
    {
        $this->authorize('create', [$tipoPersona, 'TipoPersona']);

        DB::beginTransaction();

        try {
            $data = $request->all();

            TipoPersona::create($data);

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.'];

        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(TipoPersona $tipoPersona)
    {
        $this->authorize('view', [$tipoPersona, 'TipoPersona']);

        return ['sucess'=>true, 'tipoPersona'=>$tipoPersona];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TipoPersona $tipoPersona)
    {
        $this->authorize('update', [$tipoPersona, 'TipoPersona']);

        return view('tipo-persona.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoPersona $tipoPersona, TipoPersonaRequest $request)
    {
        $this->authorize('update', [$tipoPersona, 'TipoPersona']);

        DB::beginTransaction();

        try {
            $data = $request->all();

            $tipoPersona->update($data);

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.'];

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TipoPersona $tipoPersona)
    {
        $this->authorize('delete', [$tipoPersona, 'TipoPersona']);

        DB::beginTransaction();

        try {

            $tipoPersona->delete();

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.'];

        }
    }
}
