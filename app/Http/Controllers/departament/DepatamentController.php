<?php

namespace App\Http\Controllers\departament;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Departament;
use Illuminate\Http\Request;

class DepatamentController extends Controller
{
    /**
     * Obtenener los departamentos de colombia
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Departament::orderBy('departament')->get(), 200);
    }

    /**
     * Obtener las ciudades del departamento seleccionado.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function getCitiesByDepartament(Request $request)
    {
        $request->validate([
            'departament_id' => 'required|numeric'
        ]);
        $cities = City::where(
            'departament_id',
            $request->departament_id
        )->get();
        return response()->json($cities, 200);
    }
}
