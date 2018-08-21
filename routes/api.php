<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/** api */
Route::get("listarIncidencias", "IncidenteController@getIncidentes"); // listado de incidencias

Route::get("listarPersonas", "PersonaController@getPersonas"); // obtener personas

Route::get("listarPersona/{id}", "PersonaController@getPersonaById"); // obtener persona por id

Route::post("login", "UsuarioController@logueo"); // login de usuario

Route::post("registrarUsuario", "UsuarioController@nuevoRegistroCiudadano"); // registro de ciudadano

Route::get("listarUrbanizaciones", "UrbanizacionController@getUrbanizaciones"); // listado de urbanizaciones

Route::post("actualizarPersona", "PersonaController@udpPersonaById"); // Actualizacion de datos de persona

Route::post("actualizarContrasena", "UsuarioController@udpContrasenaById"); // Actualizacion de datos de persona

Route::get("listardirectorio","DirectorioController@getDirectorio"); // Listado de Directorios

Route::post("registrarfamiliar","FamiliarController@nuevoRegistroFamiliar"); // Registro de nuevo familiar

Route::post("listarfamiliarespersona","FamiliarController@getFamiliaresbyPersona"); // Listado de Familiares de una persona

Route::post("eliminarfamiliar","FamiliarController@delFamiliarbyId"); // Eliminar familiar por ID

Route::post("estadopersona","UsuarioController@getEstadoUserbyId"); // Obtener el estado de una persona por su id

Route::get("listarnivelagua","NivelAguaController@getNivelAgua"); // Listado de los niveles de inundación

Route::get("listartipoobstaculo","TipoObstaculoController@getTipoObstaculo"); // Listar Tipos de Obstaculo

Route::post("registrarIncidencia","IncidenteController@nuevoRegistroIncidencia"); // Registro de Incidencias

Route::post("registrarMediaIncidente","IncidenteController@nuevoRegistroMediaIncidente"); // Registro de material multimedia de un incidente

Route::get("listarIncidencias/{id}", "IncidenteController@getIncidentesById"); // listado de incidencias por ID

Route::get("listarIncidencias/ciudadano/{id}", "IncidenteController@getIncidentesByCiudadano"); // listado de incidencias por Ciudadano

Route::get("ListarUbicacionFamiliar/{id}", "FamiliarController@getUbicacionFamiliarbyId"); // Listado de ubicación de una familiar por ID

Route::post("registrarUbicacionFamiliar","FamiliarController@nuevoRegistroUbicacionFamiliar"); // Registro de ubicación de un familiar

Route::get("listarIncidencias/estado/{id}","IncidenteController@getIncidentesByEstado"); // Listar incidencias por estado

Route::get("listarIncidenciasSinConfirmar/alcalde/{id}","IncidenteController@getIncidentesSinConfirmarByAlcalde"); // Listar Incidencias sin confirmar por territorio vecinal de un Alcalde o jefe de accion vecinal


Route::post("actualizarAtencionIncidente","IncidenteController@updateIncidenteAtencion"); // Actualizar estado de Incidente

Route::get("listarIncidenciasValidadas/alcalde/{id}","IncidenteController@getIncidentesValidadasByAlcalde"); // Listado de Incidencias validadas por Alcalde  o jefe de un territorio vecinal

Route::get("listarConfiguracion","ConfiguracionController@getConfiguracion"); // Listado de valores de configuración

Route::post("recuperarcontrasena","UsuarioController@getContrasenabyemail"); // Recuperar contraseña por correo

Route::get("listarUrbanizaciones/alcalde", "UrbanizacionController@getUrbanizacionesbyalcaldecomite"); // listado de urbanizaciones


