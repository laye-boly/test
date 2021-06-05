<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ImportationsDouane;
use Illuminate\Support\Facades\DB;

class ImportationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
        $importations = DB::table("importations_douanes");
        /*On filtre le tonnage des marchandisses importés dans un interavlles de temps précisé par l'utilisateur
            //Si l'utilisateur ne précise pas de dates, on considère l'intervalle de temps maximal (date la plus ancienne
            // à date la plus récente)
        */
        $dates = $request->input("dates");
        $dates = explode(";", $dates);
        if($dates[0] !== "null" && $dates[1] !== "null"){
            $importations = $importations->where("date_arrivee", ">=", $dates[0]);
            $importations = $importations->where("date_arrivee", "<=", $dates[1]);
        }
        else {
            return ["Précisez un intervalle de temps"];
        }
        $filtres = $request->all();
        // Si on a que le fitre dates (tjrs présent), on retourne le tonnage des marchandise importées durant
        // l'intervalle de temps indiqué
        if(count($filtres) == 1){
            return [
                ["poids des marchandise importes" => ""],
                ["poids" => $importations->sum("poids")]
            ];
        }

        $groupBy = []; // tableau de colonnnes sur lesquelles on va grouper la somme du tonnage des marchandises
        /*
            Filtre généraliste qui considère toutes les valeurs du filtre Level1
            Le filtre level1 concerne le regroupement du tonnage par certaines colonnes  : Exemple regroupe le tonnage
            par  consignataires, pays_origine, ville_destination,  etc
        */

        if(array_key_exists("level1", $filtres)){
            $level1Filters = $request->input("level1");
            $tabLevel1Filters = explode(";", $level1Filters);
            $groupBy = $tabLevel1Filters;
            $tabLevel1Filters[] = DB::raw("SUM(poids) AS poids");
            $importations = $importations->select($tabLevel1Filters);
        }

        // On ajoute des filtres plus spécifiques: Par exemple on ne veut que le tonnage du consignataire M.S.C SENEGAL
        // ou que des tonnages ou la ville de destination est dakar, etc
        foreach ($filtres as $key => $filtre) {
            if($key !== "dates" && $key !== "level1"){
                    $importations->where($key, "like", "%.$filtre.%");
            }

        }
        // On définit le groupe du tonnage des marchandises avec le filtre de niveau 1 (level1)
        if($groupBy){
            $importations->groupBy($groupBy);
        }

        // $importations = DB::table("importations_douanes")
        //     // ->select(["consignataire", "pays_destination", DB::raw("SUM(poids) AS poids")])
        //     ->select(["consignataire", "pays_destination", DB::raw("SUM(poids) AS poids")])
        //     ->where("pays_destination", "like", "%al%")
        //     ->groupBy("consignataire", "pays_destination")
        //      ->get()



        return $importations->get();
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
}
