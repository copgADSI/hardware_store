<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param Address $address
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Address $address)
    {
        $request->validate([
            'user_id' => 'required'
        ]);

        $addresses =  $address->where('user_id', '=', $request->user_id)
            ->orderBy('created_at')->get();

        return response()->json([
            'status' => true,
            'message' => 'Direcciones obtenidas exitosamente!',
            'data' => $addresses
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Address $address
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Address $address)
    {
        $request->validate([
            'user_id' => 'required',
            'line_one' => 'required',
            'city_id' => 'required|numeric',
            'departament_id' => 'required|numeric'
        ]);

        $address_data = null;
        DB::transaction(function () use ($address, $request, $address_data) {
            $address->where('user_id', $request->user_id)->update([
                'active' => false
            ]);
            $address_data = $address->create($request->all());
        });
        return response()->json([
            'status' => true,
            'address' => $address_data,
            'message' => 'Se agregó una nueva dirección'
        ], 200);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Address $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Address $address)
    {
        $request->validate([
            'id' => 'required',
            'user_id' => 'required',
        ]);

        $address_data = $address->find($request->id);
        if (is_null($address_data)) {
            return response()->json([
                'status' => false,
                'message' => 'No se encontró la dirección'
            ], 404);
        }

        $address_data->update($request->all());
        return response()->json([
            'status' => true,
            'address' => $address_data,
            'message' => 'Se actualizó la dirección'
        ], 200);
    }

    /**
     * Eliminar dirección
     *
     * @param  Request $request
     * @param Address $address
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Address $address)
    {
        $request->validate([
            'id' => 'required|numeric',
            'user_id' => 'required|numeric'
        ]);
        DB::transaction(function () use ($request, $address) {
            $finded_address = $address->find($request->all())->first();
            $finded_address->delete();
            $count_addresses = $address->where('user_id', $request->user_id)->get();
            if ($count_addresses->count() === 1) {
                $count_addresses->first()->update([
                    'active' => true
                ]);
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Dirección eliminada'
        ], 200);
    }

    /**
     * actualizar dirección principal
     *
     * @param  Request $request
     * @param Address $address
     * @return \Illuminate\Http\Response
     */
    public function updatePrincipalAddress(Request $request, Address $address)
    {
        $request->validate([
            'user_id' => 'required|numeric',
            'id' => 'required',
        ]);
        DB::transaction(function () use ($request, $address) {
            $addresses_olds = $address->where('user_id', $request->user_id);
            $addresses_olds->update([
                'active' => false
            ]);
            $addresses_olds->where('id', $request->id)->update(['active' => true]);
        });
        return response()->json([
            'status' => true,
            'message' => 'Dirección principal cambiada'
        ], 200);
    }
}
