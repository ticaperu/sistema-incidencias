<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlcaldeVecinalRequest;
use Illuminate\Http\Request;
use App\AlcaldeVecinal;
use Illuminate\Support\Facades\DB;

class AlcaldeVecinalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AlcaldeVecinal $alcaldeVecinal)
    {
        $this->authorize('view', [$alcaldeVecinal, 'AlcaldeVecinal']);

        return view('alcalde-vecinal.index');
    }


    public function all(AlcaldeVecinal $alcaldeVecinal)
    {
        $this->authorize('view', [$alcaldeVecinal, 'AlcaldeVecinal']);

        $alcaldesVecinales = AlcaldeVecinal::with(['persona', 'territorioVecinal'])->get();


        return ['success'=>true , 'alcaldesVecinales'=>$alcaldesVecinales];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AlcaldeVecinal $alcaldeVecinal)
    {
        $this->authorize('create', [$alcaldeVecinal, 'AlcaldeVecinal']);

        return view('alcalde-vecinal.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(AlcaldeVecinal $alcaldeVecinal, AlcaldeVecinalRequest $request)
    {
        $this->authorize('create', [$alcaldeVecinal, 'AlcaldeVecinal']);

        DB::beginTransaction();

        try {
            $data = $request->all();

            AlcaldeVecinal::create($data);

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
    public function show(AlcaldeVecinal $alcaldeVecinal)
    {
        $this->authorize('view', [$alcaldeVecinal, 'AlcaldeVecinal']);

        $alcaldeVecinal->persona;
        return ['sucess'=>true, 'alcaldeVecinal'=>$alcaldeVecinal];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(AlcaldeVecinal $alcaldeVecinal)
    {
        $this->authorize('update', [$alcaldeVecinal, 'AlcaldeVecinal']);

        return view('alcalde-vecinal.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(AlcaldeVecinal $alcaldeVecinal, AlcaldeVecinalRequest $request)
    {
        $this->authorize('update', [$alcaldeVecinal, 'AlcaldeVecinal']);

        DB::beginTransaction();

        try {
            $data = $request->all();
            $alcaldeVecinal->update($data);

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
    public function destroy(AlcaldeVecinal $alcaldeVecinal)
    {
        $this->authorize('delete', [$alcaldeVecinal, 'AlcaldeVecinal']);

        DB::beginTransaction();

        try {

            $alcaldeVecinal->delete();

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.'];

        }
    }
    
    
}
