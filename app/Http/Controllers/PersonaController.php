<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonaRequest;
use Faker\Provider\Person;
use Illuminate\Http\Request;
use App\Persona;
use App\User;
use App\PuntuacionPersona;
use App\ActividadPuntuacion;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Persona $persona)
    {
        $this->authorize('view', [$persona, 'Persona']);

        return view('persona.index');
    }

    public function inactivePerson(Persona $persona)
    {
        $this->authorize('active', [$persona, 'Persona']);

        return view('persona.inactive');
    }


    public function all(Persona $persona)
    {

        $this->authorize('view', [$persona, 'Persona']);
        $personas = Persona::with(['tipopersona'])->get();      

        return ['success'=>true , 'personas'=>$personas];
    }

    public function inactivePersons(Persona $persona)
    {

        $this->authorize('active', [$persona, 'Persona']);
        $personas = Persona::with(['tipopersona'])->where('state','Inactivo')->get();


        return ['success'=>true , 'personas'=>$personas];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Persona $persona)
    {
        $this->authorize('create', [$persona, 'Persona']);

        return view('persona.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Persona $persona,PersonaRequest $request)
    {
        $this->authorize('create', [$persona, 'Persona']);

        DB::beginTransaction();

        try {
            $data = $request->all();

            $persona = Persona::create($data);

            // Registrando un usuario
            User::create([
                'name'          => $data['nombres'],
                'email'         => $data['mail'],
                'password'      => bcrypt($data['dni']),
                "last_name"     => $data['nombres'],
                "admin"         => 0,
                "state"         => "Inactivo",
                "persona_id"    => $persona->id,
                "rol_id"        => $persona->rol_id,
                "token"         => ''
            ]);

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
    public function show(Persona $persona)
    {
        $this->authorize('view', [$persona, 'Persona']);

        return ['sucess'=>true, 'persona'=>$persona];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Persona $persona)
    {
        $this->authorize('view', [$persona, 'Persona']);

        return view('persona.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Persona $persona, PersonaRequest $request)
    {
        $this->authorize('update', [$persona, 'Persona']);

        DB::beginTransaction();

        try {
            $data = $request->all();
            $persona->update($data);

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.', 'exception'=>$exception->getMessage()];

        }
    }

    public function active_persona(Persona $persona, Request $request)
    {
        $this->authorize('update', [$persona, 'Persona']);

        DB::beginTransaction();

        try {
            $data = $request->all();
            $persona->update($data);

            // Obtenemos el estado asignado a la persona
            $estado_persona = ($data['state']=='Activo')? 2 : 1;

            // Obtenemos valores de Actividad Puntuacion
            $actividad_puntuacion = ActividadPuntuacion::where('estado_incidente_id',$estado_persona)
                                                        ->where('descripcion','Registro de Usuario')->first();

            // Registro la puntuación de la persona
            PuntuacionPersona::create([
                "numero_incidente"          => 0,
                "actividad_puntuacion_id"   => $actividad_puntuacion->id,
                "persona_id"                => $data['id']
            ]);


            DB::commit();

            // Obtenemos la token del ciudadano para notificarle
            $ciudadano = User::where('persona_id',$data['id'])->first();
            $token[] = $ciudadano->token;

            // Title de la Notificacion
            $title = "Validación de Ciudadano";

            // Message de la Notificación
            $estado = ($data['state']=='Activo')? 'ya puede registrar incidencias' : 'verifique que sus datos estén correctos';
            $message = "Estimado ciudadano su cuenta ha sido validada como ".$data['state'].", ".$estado;
            
            // Valores adicionales de notificacion.
            $plataforma = "Otros";
            $id_incidente = 0; 

            // Enviamos notificacion
            $notify = $this->send_notificacion_movil($token, $title, $message, $plataforma, $id_incidente);

            return ['success' => true, 'message' => "Ciudadano validado correctamente"];

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
    public function destroy(Persona $persona)
    {
        $this->authorize('delete', [$persona, 'Persona']);

        DB::beginTransaction();

        try {

            $user = User::where('persona_id',$persona->id)->first();
            if($user)
            {
                $user->delete();
            }
            
            $persona->delete();

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.'];

        }
    }

    public function search(Request $request)
    {
        $q = $request->get('q');
        $filter = $request->get('filter');

        $personas = Persona::whereRaw('CONCAT(nombres," ", ape_paterno, " ", ape_materno) like "%'.$q.'%"')
                    ->orWhere('dni', 'like', '%'.$q.'%')
                    ->filter($filter)
                    ->get();

        return $personas;

    }

    /** -- api -- */
    public function getPersonas()
    {
        $personas = Persona::get();
        return response()->json($personas);
    }

    public function getPersonaById($id)
    {
        $persona = Persona::find($id);
        return response()->json($persona);
    }

    public function udpPersonaById(Request $request)
    {
        // Obtengo los valores que actualizare
        $id = $request->input("id_persona");
        $ape_paterno = $request->input("ape_paterno");
        $ape_materno = $request->input("ape_materno");
        $nombres = $request->input("nombres");
        $direccion = $request->input("direccion");
        $urbanizacion = $request->input("id_urbanizacion");

        // Obtengo Persona
        $persona = Persona::find($id);

        //Actualizo datos de la persona
        $persona->ape_paterno = $ape_paterno;
        $persona->ape_materno = $ape_materno;
        $persona->nombres = $nombres;
        $persona->direccion = $direccion;
        $persona->urbanizacion_id = $urbanizacion;
        
        // Registrando datos de persona
        $persona->save();

        $data["status"] = true;
        $data["mensaje"] = "Datos actualizados con éxito";
        $data["persona"] = $persona;

        return response()->json($data);       
    }

    // Envío de notificación aplicación
    // 21-07-2018 JJDCH

    public function send_notificacion_movil($tokens, $title, $message, $plataforma, $id_incidente)
    {
        //key de firebase
        define('FIREBASE_API_KEY', 'AAAAkof-lmo:APA91bFInCb08s4NTwdJ4N29bkozbiDmLIBjG-_95pTuctAfE082r8IPZfMSmNjWeZvXu0gUzapvBJCDRT3B9f4Z2bM0tXXEz1a5m_Waym3jCfIkn668n_XMGUcxtC7XxyaK_fsOKfQF3mQaXlTide8x2CK4ABv6mg');

        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
        'Authorization: key=' . FIREBASE_API_KEY,
        'Content-Type: application/json'
        );

        //Aqui van los token del alcalde o colaboradores a quien le va a allegar la notificacion
        $registrationIDs = $tokens;
        //array( "cqdA2yE-rEE:APA91bFg5neRdtPM-G-yxdpxoZgjbgR_1UlkPPP25Mhva6T8RCZx4NuIqc4Sj1FYWMs2YO76ek5xrCJu_b3db_X96Ij7Lzpt8vBxdHyH5A-Kyg5oyMcZY9dCUJxtWBgo-CinxmpE8b_3tJdnqz8CXvJ9punJPDPjgg");

        $priority = "normal";
        $res = array();
        //$res['data']['title'] = $incidente->TipoIncidente->descripcion;//"INUNDACION " ;//ESTE ES EL TITULO DE LA OTIFICACION
        $res['data']['title'] = $title;//"INUNDACION " ;//ESTE ES EL TITULO DE LA OTIFICACION
        //$res['data']['message'] =  $incidente->direccion; //DESCIPCION DE LA NOTIFICACION
        $res['data']['message'] = $message;
        $res['data']['timestamp'] = date('Y-m-d H:i a');
        $res['data']['plataforma'] = $plataforma;  //plataforma -> siempre sera eda
        //$res['data']['incidencia'] = $incidente->id;  //id del incidente registrado
        $res['data']['incidencia'] = $id_incidente;

        $fields = array(    'registration_ids'  => $registrationIDs,
        'priority'  => $priority,
        'data' => $res
        );

        //echo json_encode($fields);

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($fields) );

        $result = curl_exec($ch);
        if(curl_errno($ch)){ echo 'Curl error: ' . curl_error($ch); }
        curl_close($ch);
        return $result;
    }


}
