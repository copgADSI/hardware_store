<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dashboards\CategoriesDashboard;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    protected $categoriesDashboard;

    public function __construct(CategoriesDashboard $categoriesDashboard)
    {
        $this->categoriesDashboard = $categoriesDashboard;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'categories' => $this->categoriesDashboard->getCountByCategory()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Category $category
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Category $category)
    {
        $request->validate([
            'category' => 'required|unique:categories'
        ]);
        DB::transaction(function () use ($request, $category) {
            $category->category = $request->category;
            $category->save();
        });

        return response()->json([
            'status' => true,
            'message' => "Categoría {$request->category} creada con éxito",
        ]);
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
