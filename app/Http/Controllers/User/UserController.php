<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\dashboards\ProductsDashboard;
use App\Models\User;
use Illuminate\Http\Request;
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
                'password' => 'required|pass'
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

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("access_token")->plainTextToken,
                'user' => $user,
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
            'name' => 'required|email',
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
