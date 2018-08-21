<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Familiar;
use App\FamiliarUbicacion;
use Illuminate\Support\Facades\DB;

class FamiliarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /** -- api -- */
    // Registro de familiares por persona
    // JJDCH 30062018
    public function nuevoRegistroFamiliar(Request $request){

        // Obtengo los datos de la persona
        $persona_id = $request->input("id_persona");
        $nombres = $request->input("nombres");
        $telefono = $request->input("telefono");  

       // Registrando un usuario
        Familiar::create([
            'nombres'     => $nombres,
            'telefono'    => $telefono,
            'persona_id'  => $persona_id
        ]);

        $data["status"] = true;
        $data["mensaje"] = "Familiar registrado con éxito";

        return response()->json($data);
    }

    /** -- api -- */
    // Listado de familiares por persona
    // JJDCH 29062018

    public function getFamiliaresbyPersona(Request $request){

        // Obtengo los datos de la persona
        $persona_id = $request->input("id_persona");  

        // Obtengo Datos de los familiares
        $familiar = Familiar::where("persona_id", $persona_id)->get();

        return response()->json($familiar); 

    }

    /** -- api -- */
    // Eliminar familiar por ID
    // JJDCH 29062018
    public function delFamiliarbyId(Request $request){

        // Obtengo los datos de la persona
        $familiar_id = $request->input("id_familiar"); 

        // Elimino Familiar
        $familiar = Familiar::find($familiar_id);
        $familiar->delete();

        $data["status"] = true;
        $data["mensaje"] = "Familiar eliminado correctamente";
        
        return response()->json($data);
    }

    /** -- api -- */
    // Listar Ubicacion familiar por ID
    // JJDCH 02072018
    public function getUbicacionFamiliarbyId($id)
    {
        // Obtengo Datos de ubicación de los familiares
        $familiarubicacion = FamiliarUbicacion::where("familiar_id", $id)->get();

        return response()->json($familiarubicacion); 
    }

    /** -- api -- */
    // Registrar Ubicación de un familiar
    // JJDCH 02072018
    public function nuevoRegistroUbicacionFamiliar(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $data = $request->all();
            $familiares = Familiar::where("telefono", $data['telefono'])->get();

            if (count($familiares)>0) {
                foreach ($familiares as $familiar) {

                    // Obtengo el id de la persona
                    $id_familiar = $familiar['id'];
                    $data['familiar_id'] = $id_familiar;

                    // Registro la ubicación del familiar                
                    FamiliarUbicacion::create($data);
                    DB::commit();
                }          

                return ['success' => true, 'data' => "Ubicación registrada correctamente"];
            }
            else
            {
                return ['success' => false, 'data' => "El teléfono no se encuentra asociado a un familiar"];
            }               

        } catch (\Exception $exception) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.', 'exception'=>$exception->getMessage()];
        }
    }

}
