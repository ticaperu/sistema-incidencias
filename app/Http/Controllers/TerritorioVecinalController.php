<?php

namespace App\Http\Controllers;

use App\Http\Requests\TerritorioVecinalRequest;
use App\TerritorioVecinal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TerritorioVecinalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TerritorioVecinal $territorioVecinal)
    {
        $this->authorize('view', [$territorioVecinal, 'TerritorioVecinal']);

        return view('territorio-vecinal.index');
    }

    public function viewMap()
    {
        return view('territorio-vecinal.view-map');
    }


    public function all(TerritorioVecinal $territorioVecinal)
    {
        $this->authorize('view', [$territorioVecinal, 'TerritorioVecinal']);

        $territoriosVecinales = TerritorioVecinal::all();


        return ['success'=>true , 'territoriosVecinales'=>$territoriosVecinales];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(TerritorioVecinal $territorioVecinal)
    {
        $this->authorize('create', [$territorioVecinal, 'TerritorioVecinal']);

        return view('territorio-vecinal.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(TerritorioVecinal $territorioVecinal,TerritorioVecinalRequest $request)
    {
        $this->authorize('create', [$territorioVecinal, 'TerritorioVecinal']);

        DB::beginTransaction();

        try {
            $data = $request->all();

            $latitudes = array();
            $longitudes = array();

            $coordinates = explode(';', $data['coordenadas']);

            foreach ($coordinates as $coordinate){
                $data_c = explode(',', $coordinate);
                $latitudes[] = $data_c[0];
                $longitudes[] = $data_c[1];
            }

            $data['max_latitude'] = max($latitudes);
            $data['min_latitude'] = min($latitudes);
            $data['max_longitude'] = max($longitudes);
            $data['min_longitude'] = min($longitudes);

            TerritorioVecinal::create($data);

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
    public function show(TerritorioVecinal $territorioVecinal)
    {
        $this->authorize('view', [$territorioVecinal, 'TerritorioVecinal']);

        return ['sucess'=>true, 'territorioVecinal'=>$territorioVecinal];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TerritorioVecinal $territorioVecinal)
    {
        $this->authorize('update', [$territorioVecinal, 'TerritorioVecinal']);

        return view('territorio-vecinal.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(TerritorioVecinal $territorioVecinal, TerritorioVecinalRequest $request)
    {
        $this->authorize('update', [$territorioVecinal, 'TerritorioVecinal']);

        DB::beginTransaction();

        try {
            $data = $request->all();

            $latitudes = array();
            $longitudes = array();

            $coordinates = explode(';', $data['coordenadas']);

            foreach ($coordinates as $coordinate){
                $data_c = explode(',', $coordinate);
                $latitudes[] = $data_c[0];
                $longitudes[] = $data_c[1];
            }

            $data['max_latitude'] = max($latitudes);
            $data['min_latitude'] = min($latitudes);
            $data['max_longitude'] = max($longitudes);
            $data['min_longitude'] = min($longitudes);

            $territorioVecinal->update($data);

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
    public function destroy(TerritorioVecinal $territorioVecinal)
    {
        $this->authorize('delete', [$territorioVecinal, 'TerritorioVecinal']);

        DB::beginTransaction();

        try {

            $territorioVecinal->delete();

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.'];

        }
    }
}
