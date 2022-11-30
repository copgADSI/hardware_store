<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\dashboards\ProductsDashboard;
use App\Http\Controllers\dashboards\ReviewsDashboard;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    protected $productDashboard;
    protected $reviewsDashboard;

    public function __construct(ProductsDashboard $productDashboard, ReviewsDashboard $reviewsDashboard)
    {
        $this->productDashboard = $productDashboard;
        $this->reviewsDashboard = $reviewsDashboard;
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
            $products = $this->productDashboard->getInfoProducts(
                $finded_user->id ?? null,
                $filters,
            );

            return response()->json([
                'status' => true,
                'products' => $products,
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
     * obtener precio máximo y mínimo
     * @return \Illuminate\Http\Response
     */
    public function getPriceRange()
    {
        return  response()->json([
            'priceRange' => [
                'max' => Product::max('price'),
                'min' => Product::min('price')
            ]
        ], 200);
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
                'carousel' => 'required',
                'name' => 'required|min:4|unique:products',
                'price' => 'required|integer',
                'quantity' => 'required',
                'description' => 'required',
                /* 'user_id' => 'required' */
            ]);;
            if ($validate->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors()
                ], 401);
            }

            $fields = $request->all();
            $fields['carousel'] = $this->uploadImageToS3(
                $request
            );

            DB::transaction(function () use ($fields) {
                Product::create($fields);
            });

            return response()->json([
                'status' => true,
                'message' => 'Producto creado con éxito!',
                'product' => $fields
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Proceso para subir las imágenes de cada producto a amazon s3
     * @return string
     */
    private function uploadImageToS3($request): string
    {
        if ($request->hasFile('carousel')) {
            $urls = [];
            foreach ($request->carousel as $image) {
                $path_name = str_replace(' ', '_',  $request->name);
                /** Proceso para guardar gif en disco s3 */
                $full_path = Storage::disk('s3')->put($path_name, $image, 'public');
                /** Url de acceso a imgs y valor para la prop images de BD*/
                $full_url = Storage::disk('s3')->url($full_path);
                $urls[] = $full_url;
            }
            return implode(',', $urls);
            /* $urls = [];
            foreach ($request->carousel as $image) {
                $current_time = time();
                $url = $image->store("images/{$current_time}");
                $urls[] = $url;
            }
            return implode(',', $urls); */
        }
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
            $product = Product::findOrFail($request->id);
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
            $product = Product::findOrFail($request->id);
            //agregar delete a bucket
            Storage::disk('s3')->delete($product->carousel);
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
            DB::transaction(function () use ($request) {
                $favorite = Favorite::updateOrCreate($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'producto agregado a mis favoritos',
                    'favorites' => $favorite
                ]);
            });
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
        $favorites = $favorite->join('products', 'favorites.product_id', '=', 'products.id')
            ->where('favorites.user_id', '=', $request->user_id);
        return response()->json([
            'status' => true,
            'total' => $favorites->get()->count(),
            'favorites' => $favorites->paginate(10)
        ], 200);
    }

    /**
     * Método usado para eliminar favoritos del usuario
     *
     * @param Request $request
     * @param User $user
     * @param Favorite $favorite
     * @return \Illuminate\Http\Response
     */
    public function destroyFavorite(Request $request, User $user, Favorite $favorite)
    {
        $request->validate([
            'user_id' => 'required',
            'product_id' => 'required'
        ]);
        $user->findOrFail(['id' => $request->user_id]);
        $favorite
            ->where('user_id', $request->user_id)
            ->where('product_id', $request->product_id)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Producto eliminado de favoritos'
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
