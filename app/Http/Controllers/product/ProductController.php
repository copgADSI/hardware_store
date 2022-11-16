<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\dashboards\ProductsDashboard;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    private $productDashboard;

    public function __construct(ProductsDashboard $productDashboard)
    {
        $this->productDashboard = $productDashboard;
    }

    /**
     * Display a listing of the resource.
     * @param User $user
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(User $user, Request $request)
    {
        try {
            $brads_ids['brands'] = json_decode($request->brands_ids);
            $categories_ids['categories'] = json_decode($request->categories_ids);
            $filters = array_merge($brads_ids, $categories_ids);
            $finded_user = $user->where('email', $request->email)->first(); //cambiar por token
            $products = $this->productDashboard->getProductsAndFavoritesByUser(
                $finded_user->id ?? null,
                $filters,
            );
            if ($products->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron productos...'
                ], 404);
            }
            return response()->json([
                'status' => true,
                'products' => $products,
                'productsRandom' => Product::all()->random(4),
                'priceRange' => [
                    'max' => Product::max('price'),
                    'min' => Product::min('price')
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * obtener laptops.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLaptops(Request $request)
    {
        try {
            $laptops = $this->productDashboard->handleLaptops($request->all());
            if ($laptops->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron productos...'
                ], 404);
            }
            return response()->json([
                'status' => true,
                'products' => $laptops,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'images_carousel' => 'required',
                'name' => 'required|min:4|unique:products',
                'price' => 'required|integer',
                'quantity' => 'required',
                'description' => 'required',
                'user_id' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors()
                ], 401);
            }
            $product = Product::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Producto creado con éxito!',
                'product' => $product
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    private function upload()
    {
        # code...
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'id' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors()
                ], 401);
            }
            $product = $this->productDashboard->getSingleProduct(
                $request->id
            );

            if (is_null($product)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Producto no encontrado...'
                ], 404);
            }
            return response()->json([
                'status' => true,
                'product' => $product[0]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'id' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors()
                ], 401);
            }
            $product = Product::where('id', '=', $request->id)
                ->first();

            if (is_null($product)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Producto no encontrado...'
                ], 404);
            }
            $product->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Producto actualizado con éxito!',
                'product' => $product
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'id' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors()
                ], 401);
            }
            $product = Product::where('id', '=', $request->id)
                ->first();

            if (is_null($product)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Producto no encontrado...'
                ], 404);
            }
            $product->forceDelete();
            return response()->json([
                'status' => true,
                'message' => 'Producto eliminado con éxito!'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Método usado para agregar un producto a favoritos.
     *
     * @param Request $request
     * @param User $user
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function addFavoritesProduct(Request $request, User $user, Product $product)
    {
        try {

            $validate = Validator::make($request->all(), [
                'user_id' => 'required',
                'product_id' => 'required'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors()
                ], 401);
            }

            $user->findOrFail($request->user_id);
            $product->findOrFail($request->product_id);
            $favorite = Favorite::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'producto agregado a mis favoritos',
                'favorites' => $favorite
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Método usado para listados los productos favoritos
     *
     * @param Request $request
     * @param User $user
     * @param Favorite $favorite
     * @return \Illuminate\Http\Response
     */
    public function getFavorites(Request $request, User $user, Favorite $favorite)
    {
        $request->validate([
            'user_id' => 'required'
        ]);
        $user->findOrFail(['id' => $request->user_id]);
        $favorites = $favorite->where('user_id', '=', $request->user_id)->get();
        return response()->json([
            'status' => true,
            'total' => $favorites->count(),
            'favorites' => $favorites
        ], 200);
    }

    /**
     * Método usado para listados los productos favoritos
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function searchMatches(Request $request)
    {
        $request->validate([
            'term' => 'required'
        ]);
        $response_data = $this->productDashboard->searchProducts(
            $request->term
        );
        if ($response_data->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No se encontraron coincidencias'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Registros encontrados',
            'matches' => $response_data
        ], 200);
    }
}
