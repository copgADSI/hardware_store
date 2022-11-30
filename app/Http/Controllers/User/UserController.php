<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\dashboards\ProductsDashboard;
use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    protected $productDashboard;

    public function __construct(ProductsDashboard $productDashboard)
    {
        $this->productDashboard = $productDashboard;
    }

    /**
     * EndPoint para iniciar sesión
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $request->email)->first();

            if (is_null($user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'user not found'
                ], 404);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'password not match'
                ], 302);
            }
            $user->last_login = now()->toDateTimeString();
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("access_token")->plainTextToken,
                'user' => $user,
                'role' => $user->role->role,
                'shopping_cart' => $this->productDashboard->getShoppingCartByUser($user->id)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * EndPoint para cerrar iniciar sesión
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
            ]);
            $user = User::findOrFail($request->id);
            DB::transaction(function () use ($user) {
                $user->tokens()->delete();
            });

            return response()->json([
                'status' => true,
                'message' => 'Cerró exitosamente sesión',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $user_fields = $request->except('password_confirmation');
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:10|confirmed',
        ]);

        $user_data = $user->create($user_fields)->toArray();
        return response()->json($user_data, 200);
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
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'id' => 'required'
        ]);
        if (!is_null($request->password)) {
            $request->validate([
                'password' => 'confirmed'
            ]);
        }
        $user_data = $user->where('id', $request->id)->first();
        if (is_null($user_data)) {
            return response()->json([
                'status' => false,
                'message' => 'Usuario no encontrado',
                'user' => $request->all()
            ], 404);
        }
        $user_data->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Usuario actualizado con éxito',
            'user' => $user_data
        ], 200);
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
