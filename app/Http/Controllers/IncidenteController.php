<?php

namespace App\Http\Controllers;

use App\CalleObstaculo;
use App\Http\Requests\IncidenteRequest;
use App\Inundacion;
use App\Notificacion;
use App\Polyline;
use App\TipoIncidente;
use App\TipoObstaculo;
use App\NivelAgua;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Incidente;
use App\IncidenteMedia;
use App\AtencionIncidente;
use App\Persona;
use Illuminate\Support\Facades\DB;
use App\EstadoIncidente;
use App\Urbanizacion;
use App\TerritorioVecinal;
use App\AlcaldeVecinal;
use App\ComiteGestion;
use App\User;
use App\PuntuacionPersona;
use App\ActividadPuntuacion;

class IncidenteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Incidente $incidente)
    {
        $this->authorize('view', [$incidente, 'Incidente']);

        return view('incidente.index');
    }

    public function attention(Incidente $incidente)
    {
        $this->authorize('view', [$incidente, 'Incidente']);

        return view('incidente.attention');
    }


    public function all(Incidente $incidente, Request $request)
    {
        $this->authorize('view', [$incidente, 'Incidente']);

        $date = $request->get('date');
        $urbanizacion_id = $request->get('urbanizacion');
        $territorio_vecinal_id = $request->get('territorio');
        $estado_id = $request->get('estado');

        $incidentes = Incidente::select(
            'incidente.id',
            'incidente.fecha',
            'incidente.descripcion',
            'incidente.direccion',
            'incidente.persona_id_validador',
            'incidente.latitud',
            'incidente.longitud',
            'incidente.urbanizacion_id',
            'incidente.persona_id',
            'incidente.estado_incidente_id',
            'incidente.tipo_incidente_id',
            'incidente.imagen'
        )
            ->with(['estadoIncidente', 'polylines', 'persona','urbanizacion',
            'urbanizacion.territorioVecinal', 'tipoIncidente', 'inundacion', 'inundacion.nivelAgua',
            'calleObstaculo', 'calleObstaculo.tipoObstaculo'])
            ->filterFecha($date)
            ->filterEstado($estado_id)
            ->filterUrbanizacion($urbanizacion_id)
            ->filterTerritorioVecinal($territorio_vecinal_id)
            ->get();


        return ['success' => true, 'incidentes' => $incidentes];
    }

    public function attentions(Incidente $incidente)
    {
        $this->authorize('view', [$incidente, 'Incidente']);

        // Listamos incidentes confirmados y atendidos
        $incidentes = Incidente::with(['estadoIncidente', 'polylines', 'persona','urbanizacion',
            'urbanizacion.territorioVecinal', 'tipoIncidente', 'inundacion', 'inundacion.nivelAgua',
            'calleObstaculo', 'calleObstaculo.tipoObstaculo'])
            ->whereIn('estado_incidente_id', [2,4])
            ->get();

        return ['success' => true, 'incidentes' => $incidentes];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Incidente $incidente)
    {
        $this->authorize('create', [$incidente, 'Incidente']);

        return view('incidente.create');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Incidente $incidente)
    {
        $this->authorize('view', [$incidente, 'Incidente']);

        return view('incidente.edit');
    }

    public function detalle(Incidente $incidente)
    {
        $this->authorize('view', [$incidente, 'Incidente']);

        return view('incidente.detalle');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Incidente $incidente,IncidenteRequest $request)
    {
        $this->authorize('create', [$incidente, 'Incidente']);

        DB::beginTransaction();

        try {
            $data = $request->all();

            $data['fecha'] = Carbon::createFromTimeString($data['fecha'])->format('Y-m-d');

            $incidente = Incidente::create($data);

            if (isset($data['imagen_file']) && isset($data['imagen_file']['value'])) {
                $this->setImageFile($incidente, $data);
            }

            if ($data['tipo_incidente_id'] == 1) {
                Inundacion::create([
                    'tipo_inundacion'=>$data['inundacion']['nivel_agua_id'],
                    'incidente_id'=>$incidente->id,
                    'nivel_agua_id'=>$data['inundacion']['nivel_agua_id']
                ]);

            } elseif ($data['tipo_incidente_id'] == 2) {
                CalleObstaculo::create([
                    "incidente_id"=>$incidente->id,
                    "tipo_obstaculo_id"=>$data['calle_obstaculo']['tipo_obstaculo_id']
                ]);
            }

            foreach ($data['polylines'] as $polyline)
            {
                Polyline::create([
                    'incidente_id'=> $incidente->id,
                    'coordinates'=>$polyline['coordinates'],
                    'descripcion'=>$polyline['descripcion']

                ]);
            }

            Notificacion::create([
                'incidente_id'=>$incidente->id,
                'state'=>1,
                'descripcion'=>'Nueva Incidencia'
            ]);

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
    public function show(Incidente $incidente)
    {
        $this->authorize('view', [$incidente, 'Incidente']);

        $incidente = Incidente::with(['estadoIncidente', 'polylines', 'persona','urbanizacion',
            'urbanizacion.territorioVecinal', 'tipoIncidente', 'inundacion', 'inundacion.nivelAgua',
            'calleObstaculo', 'calleObstaculo.tipoObstaculo', 'atencionIncidente', 'atencionIncidente.persona', 'coordinaciones', 'incidentesmedia'])->where('id', $incidente->id)->first();

        return ['sucess' => true, 'incidente' => $incidente];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Incidente $incidente, IncidenteRequest $request)
    {
        $this->authorize('update', [$incidente, 'Incidente']);

        DB::beginTransaction();

        try {
            $data = $request->all();

            $incidente->update($data);

            $incidente->polylines()->delete();

            foreach ($data['polylines'] as $polyline)
            {
                Polyline::create([
                    'incidente_id'=> $incidente->id,
                    'coordinates'=>$polyline['coordinates'],
                    'descripcion'=>$polyline['descripcion']
                ]);
            }

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.', 'exception'=>$exception->getMessage()];

        }
    }


    public function setImageFile($incidente, $data)
    {

        $file = base64_decode($data['imagen_file']['value']);
        $extension = explode('.', $data['imagen_file']['filename']);
        $index = count($extension);
        $fileName = seoUrl($incidente->descripcion . '_imagen_' . substr(md5(uniqid(rand(), true)), 0, 6)) . '.' . $extension[$index - 1];

        \Storage::disk('public')->put('/images/incidentes/' . $fileName, $file);

        $exists = \Storage::disk('public')->exists('images/incidentes/' . $incidente->imagen);

        if ($exists && !empty($incidente->imagen)) {
            \Storage::disk('public')->delete('images/incidentes/' . $incidente->imagen);
        }

        $incidente->imagen = $fileName;

        $incidente->save();

    }

    public function registrarCoordinacion(Incidente $incidente, Request $request)
    {
        $this->authorize('update', [$incidente, 'Incidente']);

        DB::beginTransaction();

        try {
            $data = $request->all();

            $incidente->coordinaciones()->attach($data['coordinacion_id'], ['user_id'=>auth()->user()->id]);

            AtencionIncidente::create([
                "persona_id"=>auth()->user()->persona_id,
                "incidente_id"=>$incidente->id,
                "fecha"=>date('Y-m-d'),
                "descripcion"=>$data['descripcion']
            ]);

            DB::commit();

            return ['success' => true];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.', 'exception'=>$exception->getMessage()];

        }

    }


    // Registrar Media para incidentes
    // JJDCH 30062018
    public function setMediaFile($incidente, $data)
    {

        $file = base64_decode($data['media_file']);
        $extension = explode('.', $data['incidente_media_name']);
        $index = count($extension);
        $fileName = seoUrl('imagen_incidente_' . substr(md5(uniqid(rand(), true)), 0, 10)) . '.' . $extension[$index - 1];

        \Storage::disk('public')->put('/images/incidentes/' . $fileName, $file);

        /*$exists = \Storage::disk('public')->exists('images/incidentes/' . $incidente->imagen);

        if ($exists && !empty($incidente->imagen)) {
            \Storage::disk('public')->delete('images/incidentes/' . $incidente->imagen);
        }*/

        // Obteniendo ruta de imagen
        $file_path = \Storage::url('/images/incidentes/'.$fileName);
        $url = asset($file_path);

        // Instanciando Incidente Media
        $incidente_media = new IncidenteMedia();

        // Asignando valores a los campos de Incidente Media
        $incidente_media->incidente_id = $incidente->id;
        $incidente_media->tipo_media = $data['tipo_media'];
        $incidente_media->incidente_media_name = $fileName; 
        $incidente_media->incidente_media_url = $url; 
        
        // Registrando un Incidente Media
        $incidente_media->save();       

    }

    /** -- api -- */
    // Registrar Incidencia
    // JJDCH 29062018
    public function nuevoRegistroIncidencia(Request $request)
    {
        DB::beginTransaction();

        try {
            // Obtengo todos los valores
            $data = $request->all();  

            // Valido el tipo de persona que realiza el registro, ya que en función a ello se consideran algunos registros
            $notify = 1;
            if(!empty($data['tipo_usuario']))
            {
                $tipo_persona = $data['tipo_usuario'];

                if($tipo_persona == 4 || $tipo_persona == 5)
                {
                    $data['estado_incidente_id'] = 2;
                    $data['persona_id_validador'] = $data['persona_id'];
                    $notify = 0;
                }
            }          

            $incidente = Incidente::create($data);

            foreach ($data['media'] as $media) {
                if (isset($media['incidente_media_name']) && isset($media['media_file'])) {
                    $this->setMediaFile($incidente, $media);    
                }
            }

            if ($data['tipo_incidente_id'] == 1) {
                Inundacion::create([
                    'tipo_inundacion'=>$data['inundacion']['nivel_agua_id'],
                    'incidente_id'=>$incidente->id,
                    'nivel_agua_id'=>$data['inundacion']['nivel_agua_id']
                ]);

            } elseif ($data['tipo_incidente_id'] == 2) {
                CalleObstaculo::create([
                    "incidente_id"=>$incidente->id,
                    "tipo_obstaculo_id"=>$data['calle_obstaculo']['tipo_obstaculo_id']
                ]);
            }

            DB::commit();


            // Aca debe de enviarse la notificación
            // Obtenemos el territorio vecinal en función a la urbanización 
            $territoriovecinal = urbanizacion::where('id',$incidente->urbanizacion_id)->first();

            // Obtenemos los alcaldes y el comite vecinal para enviarle la 
            $alcalde = AlcaldeVecinal::with(['Persona','Persona.User'])
                        ->where('territorio_vecinal_id',$territoriovecinal->territorio_vecinal_id);

            $alcalde_comite = ComiteGestion::with(['Persona','Persona.User'])
                                ->where('territorio_vecinal_id',$territoriovecinal->territorio_vecinal_id)
                                ->union($alcalde)       
                                ->get();

            // Paso a los usuarios de los alcaldes y comites a un array
            foreach ($alcalde_comite as $item)
            {
                if (!empty($item['Persona']['User']->token)) {
                    $token_alcalde_comite[] = $item['Persona']['User']->token;
                }               
            }

            // Obtenemos la data del incidente
            $incidentenotify = Incidente::with(['TipoIncidente'])
                            ->where('id',$incidente->id)
                            ->first();

            // Enviamos notificacion
            // Se envía notificación siempre y cuando el que haya registrado sea un ciudadano
            if ($notify == 1) {
                $notify = $this->send_notificacion_movil($token_alcalde_comite,$incidentenotify->TipoIncidente->descripcion,$incidentenotify->direccion,'Validar Incidente',$incidentenotify->id); 
            }                      


            $message["success"] = true;
            $message["mensaje"] = "El incidente se registro correctamente";
            $message["notificacion"] = $notify;

            return response()->json($message);

        } catch (\Exception $exception) {
            DB::rollBack();

            $message["success"] = false;
            $message["mensaje"] = 'Hubo un error, intente nuevamente.';
            $message["exception"] = $exception->getMessage();

            return response()->json($message);

        }
        
    }

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

    /** -- api -- */
    // Registrar Material multimedia de un incidente
    // JJDCH 29062018
    public function nuevoRegistroMediaIncidente(Request $request)
    {
        DB::beginTransaction();

        try {
            // Obtengo todos los valores
            $data = $request->all();            

            $incidente = Incidente::find($data["id_incidente"]);

            foreach ($data['media'] as $media) {
                if (isset($media['incidente_media_name']) && isset($media['media_file'])) {
                    $this->setMediaFile($incidente, $media);    
                }
            }

            DB::commit();

            $message["success"] = true;
            $message["mensaje"] = "Material multimedia del incidente registrado correctamente"; 

            return response()->json($message);

        } catch (\Exception $exception) {
            DB::rollBack();

            $message["success"] = false;
            $message["mensaje"] = 'Hubo un error, intente nuevamente.';
            $message["exception"] = $exception->getMessage();

            return response()->json($message);

        }
    }

    /** -- api -- */
    // Listar Incidencias
    // JJDCH 30062018
    public function getIncidentes(Request $request)
    {
        // Objetos
        $incidente = Incidente::get();
        $persona = Persona::get();

        if($incidente != null)
        {
            // Devolución de campos
            foreach ($incidente as $data) {

                $persona = Persona::find($data["persona_id"]);
                $tipo_incidente = TipoIncidente::find($data["tipo_incidente_id"]);
                $media_incidente = IncidenteMedia::where("incidente_id", $data["id"])->get();
                $atencion_incidente = AtencionIncidente::where("incidente_id", $data["id"])->get();              
                $estado_incidente = EstadoIncidente::find($data["estado_incidente_id"]);
                $Urbanizacion = Urbanizacion::find($data["urbanizacion_id"]);
                $territoriovecinal = TerritorioVecinal::find($Urbanizacion["territorio_vecinal_id"]);
                $polyline = Polyline::where("incidente_id", $data["id"])->get();

                if ($data["tipo_incidente_id"]==1)
                {
                    $detalle_incidencia = Inundacion::where("incidente_id", $data["id"])->first();
                    $nivel_agua = NivelAgua::find($detalle_incidencia['nivel_agua_id']);
                    $detalle_incidencia['nivel_agua'] = $nivel_agua->descripcion;  
                }
                elseif ($data["tipo_incidente_id"]==2)
                {
                    $detalle_incidencia = CalleObstaculo::where("incidente_id", $data["id"])->first();
                    $tipo_obstaculo = TipoObstaculo::find($detalle_incidencia['tipo_obstaculo_id']);
                    $detalle_incidencia['tipo_obstaculo'] = $tipo_obstaculo->descripcion;  
                }


                $incidencia["data"] = $data;
                $incidencia["data"]["estado_incidente_descripcion"] = $estado_incidente->descripcion;
                                
                if($tipo_incidente != null){
                    $incidencia["data"]["tipo_incidente"] = $tipo_incidente->descripcion;
                }                

                $incidencia["data"]["urbanizacion_nombre"] = $Urbanizacion->descripcion;
                $incidencia["data"]["territorio_vecinal_nombre"] = $territoriovecinal->descripcion;
                $incidencia["ciudadano"] = $persona;
                $incidencia["detalle_incidente"] = $detalle_incidencia;
                $incidencia["media"] = $media_incidente;  
                $incidencia["atencion"] = $atencion_incidente;

                for ($i=0; $i < count($incidencia["atencion"]) ; $i++) 
                { 
                    $persona_atencion = Persona::find($incidencia["atencion"][$i]["persona_id"]);
                    $persona_atencion_incidente = $persona_atencion->ape_paterno." ".$persona_atencion->ape_materno." ".$persona_atencion->nombres;

                    $incidencia["atencion"][$i]["persona_atencion"] = $persona_atencion_incidente;
                }
                $incidencia["polilyne"] = $polyline;
                
                $incidencias["incidencia"][] = $incidencia;
            }        

            return response()->json($incidencias);
        }
        else
        {
            $incidencia = $incidente;
            return response()->json($incidencia);
        }
            
    }

    /** -- api -- */
    // Listar Incidencias por ID
    // JJDCH 01072018
    public function getIncidentesById($id)
    {
        // Objetos
        $incidente = Incidente::find($id);
        $persona = Persona::get();

        // Devolución de campos
        $data = $incidente;

        if ($data != null){

            $persona = Persona::find($data["persona_id"]);
            $tipo_incidente = TipoIncidente::find($data["tipo_incidente_id"]);
            $media_incidente = IncidenteMedia::where("incidente_id", $data["id"])->get();;
            $atencion_incidente = AtencionIncidente::where("incidente_id", $data["id"])->get();
            $estado_incidente = EstadoIncidente::find($data["estado_incidente_id"]);
            $Urbanizacion = Urbanizacion::find($data["urbanizacion_id"]);
            $territoriovecinal = TerritorioVecinal::find($Urbanizacion["territorio_vecinal_id"]);
            $polyline = Polyline::where("incidente_id", $data["id"])->get();

            if ($data["tipo_incidente_id"]==1)
            {
                $detalle_incidencia = Inundacion::where("incidente_id", $data["id"])->first();
                $nivel_agua = NivelAgua::find($detalle_incidencia['nivel_agua_id']);
                $detalle_incidencia['nivel_agua'] = $nivel_agua->descripcion;  
            }
            elseif ($data["tipo_incidente_id"]==2)
            {
                $detalle_incidencia = CalleObstaculo::where("incidente_id", $data["id"])->first();
                $tipo_obstaculo = TipoObstaculo::find($detalle_incidencia['tipo_obstaculo_id']);
                $detalle_incidencia['tipo_obstaculo'] = $tipo_obstaculo->descripcion;  
            }

            
            $incidencia["incidencia"]["data"] = $data;
            $incidencia["incidencia"]["data"]["estado_incidente_descripcion"] = $estado_incidente->descripcion;
            
            if($tipo_incidente != null){
                $incidencia["incidencia"]["data"]["tipo_incidente"] = $tipo_incidente->descripcion;
            } 
            
            $incidencia["incidencia"]["data"]["urbanizacion_nombre"] = $Urbanizacion->descripcion;
            $incidencia["incidencia"]["data"]["territorio_vecinal_nombre"] = $territoriovecinal->descripcion;
            $incidencia["incidencia"]["ciudadano"] = $persona;
            $incidencia["incidencia"]["detalle_incidente"] = $detalle_incidencia;
            $incidencia["incidencia"]["media"] = $media_incidente;
            $incidencia["incidencia"]["atencion"] = $atencion_incidente;

            for ($i=0; $i < count($incidencia["incidencia"]["atencion"]) ; $i++) 
            { 
                $persona_atencion = Persona::find($incidencia["incidencia"]["atencion"][$i]["persona_id"]);
                $persona_atencion_incidente = $persona_atencion->ape_paterno." ".$persona_atencion->ape_materno." ".$persona_atencion->nombres;

                $incidencia["incidencia"]["atencion"][$i]["persona_atencion"] = $persona_atencion_incidente;
            } 
            $incidencia["incidencia"]["polilyne"] = $polyline;

        }
        else
        {
            $incidencia = $data;
        }

        return response()->json($incidencia);

    }

    /** -- api -- */
    // Listar Incidencias por Ciudadano
    // JJDCH 01072018
    public function getIncidentesByCiudadano($id)
    {

        // Objetos
        $incidente = Incidente::where("persona_id", $id)->get();
        $persona = Persona::get();

        if (count($incidente)>0)
        {
            // Devolución de campos
            foreach ($incidente as $data) {

                $persona = Persona::find($data["persona_id"]);
                $tipo_incidente = TipoIncidente::find($data["tipo_incidente_id"]);
                $media_incidente = IncidenteMedia::where("incidente_id", $data["id"])->get();
                $atencion_incidente = AtencionIncidente::where("incidente_id", $data["id"])->get();
                $estado_incidente = EstadoIncidente::find($data["estado_incidente_id"]);
                $Urbanizacion = Urbanizacion::find($data["urbanizacion_id"]);
                $territoriovecinal = TerritorioVecinal::find($Urbanizacion["territorio_vecinal_id"]);
                $polyline = Polyline::where("incidente_id", $data["id"])->get();

                if ($data["tipo_incidente_id"]==1)
                {
                    $detalle_incidencia = Inundacion::where("incidente_id", $data["id"])->first();
                    $nivel_agua = NivelAgua::find($detalle_incidencia['nivel_agua_id']);
                    $detalle_incidencia['nivel_agua'] = $nivel_agua->descripcion;  
                }
                elseif ($data["tipo_incidente_id"]==2)
                {
                    $detalle_incidencia = CalleObstaculo::where("incidente_id", $data["id"])->first();
                    $tipo_obstaculo = TipoObstaculo::find($detalle_incidencia['tipo_obstaculo_id']);
                    $detalle_incidencia['tipo_obstaculo'] = $tipo_obstaculo->descripcion;  
                }


                $incidencia["data"] = $data;
                $incidencia["data"]["estado_incidente_descripcion"] = $estado_incidente->descripcion;
                
                if($tipo_incidente != null){
                    $incidencia["data"]["tipo_incidente"] = $tipo_incidente->descripcion;
                }             
                
                $incidencia["data"]["urbanizacion_nombre"] = $Urbanizacion->descripcion;
                $incidencia["data"]["territorio_vecinal_nombre"] = $territoriovecinal->descripcion;
                $incidencia["ciudadano"] = $persona;
                $incidencia["detalle_incidente"] = $detalle_incidencia;
                $incidencia["media"] = $media_incidente;
                $incidencia["atencion"] = $atencion_incidente;

                for ($i=0; $i < count($incidencia["atencion"]) ; $i++) 
                { 
                    $persona_atencion = Persona::find($incidencia["atencion"][$i]["persona_id"]);
                    $persona_atencion_incidente = $persona_atencion->ape_paterno." ".$persona_atencion->ape_materno." ".$persona_atencion->nombres;

                    $incidencia["atencion"][$i]["persona_atencion"] = $persona_atencion_incidente;
                }
                $incidencia["polilyne"] = $polyline;

                $incidencias["incidencia"][] = $incidencia;
            }        

            return response()->json($incidencias);
        }
        else
        {
            return response()->json($incidente);
        }        

    }

    /** -- api -- */
    // Listar Incidencias por estado
    // JJDCH 03072018
    public function getIncidentesByEstado($id)
    {
        // Objetos        
        $estados = explode("-", $id);
        $incidente = Incidente::whereIn('estado_incidente_id', $estados)->get();
        $persona = Persona::get();

        if (count($incidente)>0)
        {
            // Devolución de campos
            foreach ($incidente as $data) {

                $persona = Persona::find($data["persona_id"]);
                $tipo_incidente = TipoIncidente::find($data["tipo_incidente_id"]);
                $media_incidente = IncidenteMedia::where("incidente_id", $data["id"])->get();
                $atencion_incidente = AtencionIncidente::where("incidente_id", $data["id"])->get();
                $estado_incidente = EstadoIncidente::find($data["estado_incidente_id"]);
                $Urbanizacion = Urbanizacion::find($data["urbanizacion_id"]);
                $territoriovecinal = TerritorioVecinal::find($Urbanizacion["territorio_vecinal_id"]);
                $polyline = Polyline::where("incidente_id", $data["id"])->get();

                if ($data["tipo_incidente_id"]==1)
                {
                    $detalle_incidencia = Inundacion::where("incidente_id", $data["id"])->first();
                    $nivel_agua = NivelAgua::find($detalle_incidencia['nivel_agua_id']);
                    $detalle_incidencia['nivel_agua'] = $nivel_agua->descripcion;  
                }
                elseif ($data["tipo_incidente_id"]==2)
                {
                    $detalle_incidencia = CalleObstaculo::where("incidente_id", $data["id"])->first();
                    $tipo_obstaculo = TipoObstaculo::find($detalle_incidencia['tipo_obstaculo_id']);
                    $detalle_incidencia['tipo_obstaculo'] = $tipo_obstaculo->descripcion;  
                }


                $incidencia["data"] = $data;
                $incidencia["data"]["estado_incidente_descripcion"] = $estado_incidente->descripcion;

                if($tipo_incidente != null){
                    $incidencia["data"]["tipo_incidente"] = $tipo_incidente->descripcion;
                }             
                
                $incidencia["data"]["urbanizacion_nombre"] = $Urbanizacion->descripcion;
                $incidencia["data"]["territorio_vecinal_nombre"] = $territoriovecinal->descripcion;
                $incidencia["ciudadano"] = $persona;
                $incidencia["detalle_incidente"] = $detalle_incidencia;
                $incidencia["media"] = $media_incidente;
                $incidencia["atencion"] = $atencion_incidente;

                for ($i=0; $i < count($incidencia["atencion"]) ; $i++) 
                { 
                    $persona_atencion = Persona::find($incidencia["atencion"][$i]["persona_id"]);
                    $persona_atencion_incidente = $persona_atencion->ape_paterno." ".$persona_atencion->ape_materno." ".$persona_atencion->nombres;

                    $incidencia["atencion"][$i]["persona_atencion"] = $persona_atencion_incidente;
                }
                $incidencia["polilyne"] = $polyline;

                $incidencias["incidencia"][] = $incidencia;
            }        

            return response()->json($incidencias);
        }
        else
        {
            return response()->json($incidente);
        } 
    }

    /** -- api -- */
    // Listar Incidencias sin confirmar por Alcalde o comite
    // JJDCH 04072018
    public function getIncidentesSinConfirmarByAlcalde($id)
    {
        // Objeto Personas 4 = Alacalde // 5 = Comite Vecinal
        $alcaldes = DB::table('persona')
                        ->where('id', $id)
                        ->whereIn('tipo_persona_id', [4,5])->get();


        if(count($alcaldes)>0)
        {
            foreach ($alcaldes as $alcalde) {

                // En función al tipo de persona Alcalde o Comite buscamos el territorio vencinal
                // Al que pertenece

                if ($alcalde->tipo_persona_id == 4) {
                    $alcaldecomite = AlcaldeVecinal::where("persona_id", $alcalde->id)->first();
                }
                elseif ($alcalde->tipo_persona_id == 5) {
                    $alcaldecomite = ComiteGestion::where("persona_id", $alcalde->id)->first();
                }

                // Validamos que el alcalde o comite tenga asignado un territorio vecinal
                if (count($alcaldecomite)>0) 
                {
                    // Obtenemos las urbanizaciones del territorio vecinal obtenido
                    $Urbanizaciones = Urbanizacion::where("territorio_vecinal_id",$alcaldecomite->territorio_vecinal_id)->get();

                    // Ordenamos en un arregle las urbanizaciones
                    $urbs = [];
                    foreach ($Urbanizaciones as $urb) 
                    {   
                        array_push($urbs,$urb->id);
                    }

                    // Listamos los incidentes sin confirman de las urbanizacines de un territorio vecinaL
                    $incidente = Incidente::with('urbanizacion')
                                    ->Where('estado_incidente_id',1)
                                    ->whereIn('urbanizacion_id',$urbs)
                                    ->get();

                           

                    if (count($incidente)>0)
                    {
                        // Devolución de campos
                        foreach ($incidente as $data) {

                            $persona = Persona::find($data->persona_id);
                            $tipo_incidente = TipoIncidente::find($data->tipo_incidente_id);
                            $media_incidente = IncidenteMedia::where("incidente_id", $data->id)->get();
                            $atencion_incidente = AtencionIncidente::where("incidente_id", $data->id)->get();
                            $estado_incidente = EstadoIncidente::find($data->estado_incidente_id);
                            $Urbanizacion = Urbanizacion::find($data->urbanizacion_id);
                            $territoriovecinal = TerritorioVecinal::find($Urbanizacion["territorio_vecinal_id"]);
                            
                            $polyline = Polyline::where("incidente_id", $data->id)->get();

                            if ($data->tipo_incidente_id==1)
                            {
                                $detalle_incidencia = Inundacion::where("incidente_id", $data->id)->first();
                                $nivel_agua = NivelAgua::find($detalle_incidencia['nivel_agua_id']);
                                $detalle_incidencia['nivel_agua'] = $nivel_agua->descripcion;  
                            }
                            elseif ($data->tipo_incidente_id==2)
                            {
                                $detalle_incidencia = CalleObstaculo::where("incidente_id", $data->id)->first();
                                $tipo_obstaculo = TipoObstaculo::find($detalle_incidencia['tipo_obstaculo_id']);
                                $detalle_incidencia['tipo_obstaculo'] = $tipo_obstaculo->descripcion;  
                            }


                            $incidencia["data"] = $data;
                            $incidencia["data"]->estado_incidente_descripcion = $estado_incidente->descripcion;

                            if($tipo_incidente != null){
                                $incidencia["data"]->tipo_incidente = $tipo_incidente->descripcion;
                            }             
                            
                            $incidencia["data"]->urbanizacion_nombre = $Urbanizacion->descripcion;
                            $incidencia["data"]->territorio_vecinal_nombre = $territoriovecinal->descripcion;
                            $incidencia["ciudadano"] = $persona;
                            $incidencia["detalle_incidente"] = $detalle_incidencia;
                            $incidencia["media"] = $media_incidente;
                            $incidencia["atencion"] = $atencion_incidente;

                            for ($i=0; $i < count($incidencia["atencion"]) ; $i++) 
                            { 
                                $persona_atencion = Persona::find($incidencia["atencion"][$i]["persona_id"]);
                                $persona_atencion_incidente = $persona_atencion->ape_paterno." ".$persona_atencion->ape_materno." ".$persona_atencion->nombres;

                                $incidencia["atencion"][$i]["persona_atencion"] = $persona_atencion_incidente;
                            }
                            $incidencia["polilyne"] = $polyline;

                            $incidencias["incidencia"][] = $incidencia;
                        }
                        return response()->json($incidencias);
                    }
                    else
                    {
                        return ['success' => false, 'message' => 'No existen incidentes'];
                    } 
                }
                else
                {
                    return ['success' => false, 'message' => 'El usuario no tiene asignado un Territorio Vecinal']; 
                }                                                  
            }       
        }
        else
        {
            return ['success' => false, 'message' => 'El usuario no es Alcalde ni pertenece al Comité de Gestión de Territorio Vecinal'];    
        }            
    }


    /** -- api -- */
    // Actualizar estado de Incidente 
    // JJDCH 04072018
    public function updateIncidenteAtencion(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $data = $request->all();
            $incidente = Incidente::find($data["id"]);

            // Asigno nuevos valores
            $incidente->persona_id_validador = $data["persona_id_validator"];
            $incidente->estado_incidente_id = $data["estado_incidente_id"];

            // Guardo cambios en el incidente
            $incidente->save();

            // Obtenemos valores de Actividad Puntuacion
            $actividad_puntuacion = ActividadPuntuacion::where('estado_incidente_id',$data["estado_incidente_id"])
                                                        ->where('descripcion','Incidente Reportado')->first();

            // Registro la puntuación de la persona
            PuntuacionPersona::create([
                "numero_incidente"          => $incidente->id,
                "actividad_puntuacion_id"   => $actividad_puntuacion->id,
                "persona_id"                => $incidente->persona_id
            ]);

            DB::commit();

            // Obtenemos la token del ciudadano para notificarle
            $ciudadano = User::where('persona_id',$incidente->persona_id)->first();
            $token[] = $ciudadano->token;

            // Obtenemos la data del incidente
            $incidentenotify = Incidente::with(['TipoIncidente','EstadoIncidente'])
                            ->where('id',$incidente->id)
                            ->first();

            // Title de la Notificacion
            $title = "Validación de incidente ".$incidentenotify->TipoIncidente->descripcion;

            // Message de la Notificación
            $message = "El incidente reportado en ".$incidentenotify->direccion." ha sido validado como ".$incidentenotify->EstadoIncidente->descripcion;
            
            // Valores adicionales de notificacion.
            $plataforma = "Otros";
            $id_incidente = 0; 

            // Enviamos notificacion
            $notify = $this->send_notificacion_movil($token, $title, $message, $plataforma, $id_incidente);  

            return ['success' => true, 'message' => 'Incidencia actualizada corrrectamente', 'notificacion' => $notify];

        } catch (\Exception $exception) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Hubo un error, intente nuevamente.', 'exception'=>$exception->getMessage()];

        }
    }


    /** -- api -- */
    // Listar Incidencias verificadas por Alcalde o comite
    // JJDCH 04072018
    public function getIncidentesValidadasByAlcalde($id)
    {
        // Objeto Personas 4 = Alacalde // 5 = Comite Vecinal
        $alcaldes = DB::table('persona')
                        ->where('id', $id)
                        ->whereIn('tipo_persona_id', [4,5])->get();

        if(count($alcaldes)>0)
        {
            foreach ($alcaldes as $alcalde) {

                // En función al tipo de persona Alcalde o Comite buscamos el territorio vencinal
                // Al que pertenece

                if ($alcalde->tipo_persona_id == 4) {
                    $alcaldecomite = AlcaldeVecinal::where("persona_id", $alcalde->id)->first();
                }
                elseif ($alcalde->tipo_persona_id == 5) {
                    $alcaldecomite = ComiteGestion::where("persona_id", $alcalde->id)->first();
                }

                // Validamos que el alcalde o comite tenga asignado un territorio vecinal
                if (count($alcaldecomite)>0) 
                {
                    // Obtenemos las urbanizaciones del territorio vecinal obtenido
                    $Urbanizaciones = Urbanizacion::where("territorio_vecinal_id",$alcaldecomite->territorio_vecinal_id)->get();

                    // Ordenamos en un arregle las urbanizaciones
                    $urbs = [];
                    foreach ($Urbanizaciones as $urb) 
                    {   
                        array_push($urbs,$urb->id);
                    }

                    // Listamos los incidentes sin confirman de las urbanizacines de un territorio vecinaL
                    $incidente = Incidente::with('urbanizacion')
                                    ->WhereIn('estado_incidente_id',[2,4])
                                    ->whereIn('urbanizacion_id',$urbs)
                                    ->get();
                        

                    if (count($incidente)>0)
                    {
                        // Devolución de campos
                        foreach ($incidente as $data) {

                            $persona = Persona::find($data->persona_id);
                            $tipo_incidente = TipoIncidente::find($data->tipo_incidente_id);
                            $media_incidente = IncidenteMedia::where("incidente_id", $data->id)->get();
                            $atencion_incidente = AtencionIncidente::where("incidente_id", $data->id)->get();
                            $estado_incidente = EstadoIncidente::find($data->estado_incidente_id);
                            $Urbanizacion = Urbanizacion::find($data->urbanizacion_id);
                            $territoriovecinal = TerritorioVecinal::find($Urbanizacion["territorio_vecinal_id"]);
                            $polyline = Polyline::where("incidente_id", $data->id)->get();

                            if ($data->tipo_incidente_id==1)
                            {
                                $detalle_incidencia = Inundacion::where("incidente_id", $data->id)->first();
                                $nivel_agua = NivelAgua::find($detalle_incidencia['nivel_agua_id']);
                                $detalle_incidencia['nivel_agua'] = $nivel_agua->descripcion;  
                            }
                            elseif ($data->tipo_incidente_id==2)
                            {
                                $detalle_incidencia = CalleObstaculo::where("incidente_id", $data->id)->first();
                                $tipo_obstaculo = TipoObstaculo::find($detalle_incidencia['tipo_obstaculo_id']);
                                $detalle_incidencia['tipo_obstaculo'] = $tipo_obstaculo->descripcion;  
                            }


                            $incidencia["data"] = $data;
                            $incidencia["data"]->estado_incidente_descripcion = $estado_incidente->descripcion;

                            if($tipo_incidente != null){
                                $incidencia["data"]->tipo_incidente = $tipo_incidente->descripcion;
                            }             
                            
                            $incidencia["data"]->urbanizacion_nombre = $Urbanizacion->descripcion;
                            $incidencia["data"]->territorio_vecinal_nombre = $territoriovecinal->descripcion;
                            $incidencia["ciudadano"] = $persona;
                            $incidencia["detalle_incidente"] = $detalle_incidencia;
                            $incidencia["media"] = $media_incidente;
                            $incidencia["atencion"] = $atencion_incidente;

                            for ($i=0; $i < count($incidencia["atencion"]) ; $i++) 
                            { 
                                $persona_atencion = Persona::find($incidencia["atencion"][$i]["persona_id"]);
                                $persona_atencion_incidente = $persona_atencion->ape_paterno." ".$persona_atencion->ape_materno." ".$persona_atencion->nombres;

                                $incidencia["atencion"][$i]["persona_atencion"] = $persona_atencion_incidente;
                            }
                            $incidencia["polilyne"] = $polyline;
                            
                            $incidencias["incidencia"][] = $incidencia;
                        }        

                        return response()->json($incidencias);
                    }
                    else
                    {
                        return response()->json($incidente);
                    }   
                }       
                else
                {
                    return ['success' => false, 'message' => 'El usuario no tiene asignado un Territorio Vecinal']; 
                }  
            }       
        }
        else
        {
            return ['success' => false, 'message' => 'El usuario no es Alcalde ni pertenece al comite de gestión vecinal'];    
        }        
    }


}
