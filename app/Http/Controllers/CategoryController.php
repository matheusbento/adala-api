<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => [
                'integer',
                'gt:0',
                'lte:1000',
            ],
            'order_by' => [
                'string',
                Rule::in(['id', 'name']),
            ],
            'direction' => [
                'string',
                Rule::in(['asc', 'desc']),
            ],
            'q' => [
                'nullable',
                'string',
            ],
        ]);

        $builder = Category::with($this->getRelationshipsToLoad())
            ->withCount($this->getRelationshipsToLoad())
            ->orderBy($request->input('order_by', 'name'), $request->input('direction', 'asc'));

        if ($search = $request->input('q')) {
            $builder->where('name', 'LIKE', "%{$search}%");
        }

        $categorys = $request->input('all') ? $builder->get() : $builder->paginate($request->input('per_page', intval(config('general.pagination_size'))))
            ->appends($request->only(['per_page', 'order_by', 'direction', 'q']));

        return CategoryResource::collection($categorys);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['owner_id'] = Auth::user()->id;
        $cube = $this->updateCategory(new Category(), $data);

        return new CategoryResource($cube);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $category = $this->updateCategory($category, $data);

        return new CategoryResource($category);
    }

    private function updateCategory(Category $category, array $data)
    {
        $category->fill($data);
        $category->save();

        return $category;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        return [
            'success' => $category->delete(),
        ];
    }

    private function getRelationshipsToLoad()
    {
        return [];
    }
}
