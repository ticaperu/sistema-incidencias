<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth']], function () {

    Route::get("/dashboard", "DashBoardController@index")->name("dashboard.index");

    Route::get('/tipo-persona/all', 'TipoPersonaController@all')->name('tipo-persona.all');
    Route::resource('tipo-persona','TipoPersonaController');

    Route::get('/territorio-vecinal/all', 'TerritorioVecinalController@all')->name('territorio-vecinal.all');
    Route::get('/territorio-vecinal/view-map', 'TerritorioVecinalController@viewMap')->name('territorio-vecinal.view-all');
    Route::resource('territorio-vecinal','TerritorioVecinalController');

    Route::get('/urbanizacion/all', 'UrbanizacionController@all')->name('urbanizacion.all');
    Route::resource('urbanizacion','UrbanizacionController');

    Route::get('/nivel-ciudadano/all', 'NivelCiudadanoController@all')->name('ciudadano.all');
    Route::resource('nivel-ciudadano','NivelCiudadanoController');

    Route::get('/rol/permisos', 'RolController@getPermisos')->name('rol.permisos');
    Route::get('/rol/all', 'RolController@all')->name('rol.all');
    Route::resource('rol','RolController');

    Route::get('/persona/all', 'PersonaController@all')->name('persona.all');
    Route::get('/persona/inactives', 'PersonaController@inactivePersons')->name('persona.inactives');
    Route::get('/persona/inactive', 'PersonaController@inactivePerson')->name('persona.inactive');
    Route::put('/persona/activepersona/{persona}', 'PersonaController@active_persona')->name('persona.activepersona');
    Route::get('/persona/search', 'PersonaController@search')->name('persona.search');
    Route::resource('persona','PersonaController');

    Route::get('/user/all', 'UserController@all')->name('user.all');
    Route::resource('user','UserController');

    Route::get('/estado-incidente/all', 'EstadoIncidenteController@all')->name('estado-incidente.all');
    Route::resource('estado-incidente','EstadoIncidenteController');

    Route::get('/tipo-incidente/all', 'TipoIncidenteController@all')->name('tipo-incidente.all');
    Route::resource('tipo-incidente','TipoIncidenteController');

    Route::get('/nivel-agua/all', 'NivelAguaController@all')->name('nivel-agua.all');
    Route::resource('nivel-agua','NivelAguaController');

    Route::get('/tipo-obstaculo/all', 'TipoObstaculoController@all')->name('tipo-obstaculo.all');
    Route::resource('tipo-obstaculo','TipoObstaculoController');

    Route::get('/incidente/all', 'IncidenteController@all')->name('incidente.all');
    Route::get('/incidente/attentions', 'IncidenteController@attentions')->name('incidente.attentions');
    Route::get('/incidente/attention', 'IncidenteController@attention')->name('incidente.attention');    
    Route::get('/incidente/{incidente}/detalleatencion', 'IncidenteController@detalle')->name('incidente.detalleatencion');
    Route::get('/incidente/{incidente}/detalle', 'IncidenteController@edit')->name('incidente.detalle');
    Route::post('/incidente/{incidente}/registrar-coordinacion', 'IncidenteController@registrarCoordinacion')->name('incidente.registrar-coordinacion');
    Route::resource('incidente','IncidenteController');

    Route::get('/alcalde-vecinal/all', 'AlcaldeVecinalController@all')->name('alcalde-vecinal.all');
    Route::resource('alcalde-vecinal','AlcaldeVecinalController');

    Route::get('/comite-gestion/all', 'ComiteGestionController@all')->name('comite-gestion.all');
    Route::resource('comite-gestion','ComiteGestionController');

    Route::get('/directorio/all', 'DirectorioController@all')->name('directorio.all');
    Route::resource('directorio','DirectorioController');

    Route::get('/actividad-puntuacion/all', 'ActividadPuntuacionController@all')->name('actividad-puntuacion.all');
    Route::resource('actividad-puntuacion','ActividadPuntuacionController');

    Route::get('/coordinacion/all', 'CoordinacionController@all')->name('coordinacion.all');
    Route::resource('coordinacion','CoordinacionController');

    Route::resource('notificacion','NotificacionController');

    Route::get('/configuracion/all', 'ConfiguracionController@all')->name('configuracion.all');
    Route::resource('configuracion','ConfiguracionController');

    // Reportes
    Route::prefix("reportes")->group(function() {
        Route::get("ciudadanos-puntuaciones", "ReportesController@listarRankingCiudadanosPorPuntuacion")->name("reportes.ranking-ciudadanos-puntuacion");
        Route::get("incidentes-atentidos", "ReportesController@listarIncidentesAtendidos")->name("reportes.incidentes-atendidos");
        Route::get("incidentes-por-atender", "ReportesController@listarIncidentesPorAtender")->name("reportes.incidentes-por-atender");
        Route::get("incidentes-por-tipo-incidente/{tipo_incidente_id?}/{fechaInicio?}/{fechaFin?}", "ReportesController@listarIncidentesPorTipoIncidente")->name("reportes.incidentes-por-tipo-incidente");
        
        Route::get("ciudadanos-puntuaciones-excel", "ReportesController@exportRankingCiudadanosPorPuntuacion")->name("reportes.ciudadanos_puntuacion.excel");
        Route::get("incidentes-por-estado", "ReportesController@totalIncidentesPorEstadoIncidente")->name("reportes.incidentes-por-estado");
        Route::get("total-usuarios-por-tipo", "ReportesController@totalUsuarioPorTipo")->name("reportes.total-usuarios-por-tipo");
        Route::get("total-incidentes-por-estado-incidente", "ReportesController@totalIncidentesPorEstadoIncidente")->name("reportes.total-incidentes-por-estado-incidente");
        Route::get("total-metricas", "ReportesController@metricas")->name("reportes.metricas");
    });
});
