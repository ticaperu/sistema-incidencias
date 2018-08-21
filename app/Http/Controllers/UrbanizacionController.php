<?php

namespace App\Http\Controllers;

use App\Http\Requests\UrbanizacionRequest;
use App\Urbanizacion;
use App\AlcaldeVecinal;
use App\ComiteGestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UrbanizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Urbanizacion $urbanizacion)
    {
        $this->authorize('view', [$urbanizacion, 'Urbanizacion']);

        return view('urbanizacion.index');
    }


    public function all(Urbanizacion $urbanizacion)
    {
        $this->authorize('view', [$urbanizacion, 'Urbanizacion']);

        $urbanizaciones = Urbanizacion::with(['TerritorioVecinal'])->get();


        return ['success'=>true , 'urbanizaciones'=>$urbanizaciones];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Urbanizacion $urbanizacion)
    {
        $this->authorize('create', [$urbanizacion, 'Urbanizacion']);

        return view('urbanizacion.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Urbanizacion $urbanizacion, UrbanizacionRequest $request)
    {
        $this->authorize('create', [$urbanizacion, 'Urbanizacion']);

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

            Urbanizacion::create($data);

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
    public function show(Urbanizacion $urbanizacion)
    {
        $this->authorize('view', [$urbanizacion, 'Urbanizacion']);

        $urbanizacion->territorioVecinal;

        return ['sucess'=>true, 'urbanizacion'=>$urbanizacion];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Urbanizacion $urbanizacion)
    {
        $this->authorize('update', [$urbanizacion, 'Urbanizacion']);

        return view('urbanizacion.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Urbanizacion $urbanizacion, UrbanizacionRequest $request)
    {
        $this->authorize('update', [$urbanizacion, 'Urbanizacion']);

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

            $urbanizacion->update($data);

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
    public function destroy(Urbanizacion $urbanizacion)
    {
        $this->authorize('delete', [$urbanizacion, 'Urbanizacion']);

        DB::beginTransaction();

        try {

            $urbanizacion->delete();

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.'];

        }
    }

    /** -- api -- */
    public function getUrbanizaciones()
    {
        $urbanizaciones = Urbanizacion::get();
        $result = array();
        foreach($urbanizaciones as $urbanizacion){
            $data = array();
            $data["urbanizacion_id"] = $urbanizacion->id;
            $data["descripcion"] = $urbanizacion->descripcion;
            $data["fecha_creacion"] = $urbanizacion->created_at;
            $data["territorio_vecinal_id"] = $urbanizacion->territorioVecinal->id;
            $data["territorio_vecinal_descripcion"] = $urbanizacion->territorioVecinal->descripcion;
            $data["territorio_vecinal_coordenadas"] = $urbanizacion->territorioVecinal->coordenadas;
            $data["territorio_vecinal_latitud"] = $urbanizacion->territorioVecinal->latitude;
            $data["territorio_vecinal_altitud"] = $urbanizacion->territorioVecinal->longitude;
            $data["territorio_vecinal_min_latitude"] = $urbanizacion->territorioVecinal->min_latitude;
            $data["territorio_vecinal_max_latitude"] = $urbanizacion->territorioVecinal->max_latitude;
            $data["territorio_vecinal_min_longitude"] = $urbanizacion->territorioVecinal->min_longitude;
            $data["territorio_vecinal_max_longitude"] = $urbanizacion->territorioVecinal->max_longitude;
            $result[] = $data;
        }
        
        return response()->json($result);
    }

    /** -- api -- */
    public function getUrbanizacionesbyalcaldecomite()
    {   

        // Obtenemos los alcaldes y el comite vecinal para poder filtrar las urbanizaciones
        // Que tienes un territorio vecinal con alcal o comite. 
        $alcalde = AlcaldeVecinal::with(['territorioVecinal']);
        $alcalde_comite = ComiteGestion::with(['territorioVecinal'])
                            ->union($alcalde)       
                            ->get();

        if(count($alcalde_comite)>0)
        {
            // Obtenemos el listado de territorios vecinales
            foreach ($alcalde_comite as $item) 
            {
                $tvecinal[] = $item->territorio_vecinal_id;
            }

            if (count($tvecinal)>0) 
            {
                // Listamos Urbanizaciones
                $urbanizaciones = Urbanizacion::whereIn('territorio_vecinal_id',$tvecinal)->get(); 
                $result = array();
                foreach($urbanizaciones as $urbanizacion)
                {           

                    $data = array();
                    $data["urbanizacion_id"] = $urbanizacion->id;
                    $data["descripcion"] = $urbanizacion->descripcion;
                    $data["fecha_creacion"] = $urbanizacion->created_at;
                    $data["territorio_vecinal_id"] = $urbanizacion->territorioVecinal->id;
                    $data["territorio_vecinal_descripcion"] = $urbanizacion->territorioVecinal->descripcion;
                    $data["territorio_vecinal_coordenadas"] = $urbanizacion->territorioVecinal->coordenadas;
                    $data["territorio_vecinal_latitud"] = $urbanizacion->territorioVecinal->latitude;
                    $data["territorio_vecinal_altitud"] = $urbanizacion->territorioVecinal->longitude;
                    $data["territorio_vecinal_min_latitude"] = $urbanizacion->territorioVecinal->min_latitude;
                    $data["territorio_vecinal_max_latitude"] = $urbanizacion->territorioVecinal->max_latitude;
                    $data["territorio_vecinal_min_longitude"] = $urbanizacion->territorioVecinal->min_longitude;
                    $data["territorio_vecinal_max_longitude"] = $urbanizacion->territorioVecinal->max_longitude;
                    $result[] = $data;
                }   
                
            } 
            else 
            {
                $result=array();
            }     
        }
        else
        {
            $result=array();
        }
        
        return response()->json($result);        
    }
}
