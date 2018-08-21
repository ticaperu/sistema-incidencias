<?php

namespace App\Http\Controllers;
use DB;
use Excel;
use App\PuntuacionPersona;
use App\Persona;
use App\ActividadPuntuacion;
use App\TerritorioVecinal;
use App\TipoIncidente;
use App\Incidente;

use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function listarRankingCiudadanosPorPuntuacion()
    {
        $result = DB::table("puntuacion_persona as pp")
            ->join("persona as p", "p.id", "=", "pp.persona_id")
            ->join("actividad_puntuacion as ap", "ap.id", "=", "pp.actividad_puntuacion_id")
            ->select("pp.id as puntuacion_persona_id", "pp.numero_incidente", "p.id as persona_id", "p.nombres", "p.ape_paterno", "p.ape_materno", "p.dni", "ap.puntaje")
            ->where("p.tipo_persona_id", 2)
            ->orderBy("ap.puntaje", "desc")
            ->get();

        return view("reportes.ciudadanos-por-puntuacion", compact("result"));   
    } 

    public function listarIncidentesAtendidos()
    {
        $incidentes = $this->listarIncidentesPorEstadoIncidente(4);
        return view("reportes.incidentes-atendidos", compact("incidentes"));
    }

    public function listarIncidentesPorAtender()
    {
        $incidentes = $this->listarIncidentesPorEstadoIncidente(2);
        return view("reportes.incidentes-por-atender", compact("incidentes"));
    }

    public function listarIncidentesPorTipoIncidente(Request $request)
    {
        $tipoIncidente = TipoIncidente::get();
        $tipo_incidente_id = $request->input("tipo_incidente_id");
        $fecha_inicio = $request->input("fecha_inicio");
        $fecha_final = $request->input("fecha_final");

        if(is_null($tipo_incidente_id) && is_null($fecha_inicio) && is_null($fecha_final)) {
            $incidentes = Incidente::get();
        }else if(!is_null($tipo_incidente_id) && is_null($fecha_inicio) && is_null($fecha_final)) {
            $incidentes = Incidente::where("tipo_incidente_id", $tipo_incidente_id)->get();
        }else if(!is_null($tipo_incidente_id) && $tipo_incidente_id == 0 && !is_null($fecha_inicio) && !is_null($fecha_final)) {
            $incidentes = Incidente::whereBetween("fecha", [$fecha_inicio, $fecha_final])->get();
        }else if(!is_null($tipo_incidente_id) && $tipo_incidente_id != 0 && !is_null($fecha_inicio) && !is_null($fecha_final)) {
            $incidentes = Incidente::where("tipo_incidente_id", $tipo_incidente_id)
                ->whereBetween("fecha", [$fecha_inicio, $fecha_final])->get();
        }else {
            $incidentes = [];
        }

        return view("reportes.incidentes-por-tipo-incidente", compact("tipoIncidente", "incidentes"));
    }

    public function totalUsuarioPorTipo(){
        $result = DB::table("users as u")
            ->select("tp.descripcion", DB::raw("COUNT(u.id) as total"))
            ->join("persona as p", "p.id", "=", "u.persona_id")
            ->join("tipo_persona as tp", "tp.id", "=", "p.tipo_persona_id")
            ->where("u.state", "Activo")
            ->groupBy("tp.id", "tp.descripcion")
            ->get();

        $labels = $result->pluck("descripcion");
        $data = $result->pluck("total");
        return ["labels" => $labels, "data" => $data];
    }

    public function totalIncidentesPorEstadoIncidente(){
        $result = DB::table("estado_incidente as ei")
            ->select("ei.descripcion", DB::raw("COUNT(ei.id) as total"))
            ->join("incidente as i", "ei.id", "=", "i.estado_incidente_id")
            ->groupBy("ei.id", "ei.descripcion")
            ->orderBy("ei.id", "desc")
            ->get();

        $labels = $result->pluck("descripcion");
        $data = $result->pluck("total");
        return ["labels" => $labels, "data" => $data];
    }

    public function metricas() {
        return ["labels" => $this->metricasLabels(), "data" => $this->metricasValores()];
    }

    private function numeroTotalPorTipoPersona($tipoPersona) {
        $result = DB::table("persona")
            ->where("estado_persona_id", 1)
            ->where("tipo_persona_id", $tipoPersona)
            ->count();

        return $result;
    }

    private function numeroTotalTerritoriosVecinales() {
        $result = DB::table("territorio_vecinal")->count();
        return $result;
    }

    private function numeroIncidentesValidos() {
        return $this->listarIncidentePorTipo(2);
    }

    private function numeroIncidentesNoValidos() {
        return $this->listarIncidentePorTipo(3);
    }

    private function numeroIncidentesAtentidos() {
        return $this->listarIncidentePorTipo(4);
    }

    private function metricasLabels()
    {
        $return = array();
        $return[] = "N° Ciudadanos";
        $return[] = "N° Alcaldes Vecinales";
        $return[] = "N° Territorios Vecinales";
        $return[] = "N° Incidentes válidos";
        $return[] = "N° Incidentes no válidos";
        $return[] = "N° Incidentes atendidos";
        return $return;
    }

    private function metricasValores()
    {
        $return = array();
        $return[] = $this->numeroTotalPorTipoPersona(2);
        $return[] = $this->numeroTotalPorTipoPersona(4);
        $return[] = $this->numeroTotalTerritoriosVecinales();
        $return[] = $this->numeroIncidentesValidos();
        $return[] = $this->numeroIncidentesNoValidos();
        $return[] = $this->numeroIncidentesAtentidos();
        return $return;
    }

    private function listarIncidentePorTipo($estadoIncidente)
    {
        $result = DB::table("incidente")->where("estado_incidente_id", $estadoIncidente)->count();
        return $result;
    }

    private function listarIncidentesPorEstadoIncidente($estado) {
        $incidentes = Incidente::where("estado_incidente_id", $estado)->get();
        return $incidentes;
    }

    public function exportRankingCiudadanosPorPuntuacion()
    {
        $filename = "ranking_ciudadanos_".now();
        $result = DB::table("puntuacion_persona as pp")
            ->join("persona as p", "p.id", "=", "pp.persona_id")
            ->join("actividad_puntuacion as ap", "ap.id", "=", "pp.actividad_puntuacion_id")
            ->select("pp.id as puntuacion_persona_id", "pp.numero_incidente", "p.id as persona_id", "p.nombres", "p.ape_paterno", "p.ape_materno", "p.dni", "ap.puntaje")
            ->where("p.tipo_persona_id", 2)
            ->orderBy("ap.puntaje", "desc")
        ->get();

        Excel::create($filename, function($excel) use($result){
            $excel->sheet("Datos", function($sheet) use($result){
                $sheet->mergeCells("A1:F1");
                $sheet->cell("A1", function($cell) {
                    $cell->setValue("Listado de Ciudadanos");
                    $cell->setAlignment("center");
                    $cell->setFont(array("family" => "Calibri", "size"=>14, "bold" => true));
                });
                $sheet->cell("A2", function($cell) {$cell->setValue("ID"); $cell->setAlignment("center"); });
                $sheet->cell("B2", function($cell) {$cell->setValue("Nombres"); $cell->setAlignment("center"); });
                $sheet->cell("C2", function($cell) {$cell->setValue("Apellido Paterno"); $cell->setAlignment("center"); });
                $sheet->cell("D2", function($cell) {$cell->setValue("Apellido Materno"); $cell->setAlignment("center"); });
                $sheet->cell("E2", function($cell) {$cell->setValue("DNI"); $cell->setAlignment("center"); });
                $sheet->cell("F2", function($cell) {$cell->setValue("Puntaje"); $cell->setAlignment("center"); });
                if(!empty($result)) {
                    foreach($result as $key => $value) {
                        $i = $key + 3;
                        $sheet->cell('A'.$i, $value->puntuacion_persona_id);
                        $sheet->cell('B'.$i, $value->nombres);
                        $sheet->cell('C'.$i, $value->ape_paterno);
                        $sheet->cell('D'.$i, $value->ape_materno);
                        $sheet->cell('E'.$i, $value->dni);
                        $sheet->cell("F".$i, $value->puntaje);
                    }
                }
            });
        })->export('xlsx');
    }
}
