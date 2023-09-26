<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCubeDashboardItemRequest;
use App\Http\Resources\CubeDashboardItemResource;
use App\Models\Cube;
use App\Models\CubeDashboardItem;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CubeDashboardItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Organization $organization, Request $request)
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

        $builder = CubeDashboardItem::with($this->getRelationshipsToLoad())
            ->orderBy($request->input('order_by', 'name'), $request->input('direction', 'asc'));

        if ($search = $request->input('q')) {
            $builder->where('name', 'LIKE', "%{$search}%");
        }

        $cubeDashboardItems = $request->input('all') ? $builder->get() : $builder->paginate($request->input('per_page', intval(config('general.pagination_size'))))
            ->appends($request->only(['per_page', 'order_by', 'direction', 'q']));

        return CubeDashboardItemResource::collection($cubeDashboardItems);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCubeDashboardItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCubeDashboardItemRequest $request, Organization $organization, Cube $cube)
    {
        $data = $request->validated();
        $data['cube_id'] = $cube->id;
        $cube = $this->updateCubeDashboardItem(new CubeDashboardItem(), $data);

        return new CubeDashboardItemResource($cube);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CubeDashboardItem  $cubeDashboardItem
     * @return \Illuminate\Http\Response
     */
    public function show(CubeDashboardItem $cubeDashboardItem, Organization $organization, Cube $cube)
    {
        return new CubeDashboardItemResource($cubeDashboardItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreCubeDashboardItemRequest  $request
     * @param  \App\Models\CubeDashboardItem  $cubeDashboardItem
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCubeDashboardItemRequest $request, Organization $organization, Cube $cube, CubeDashboardItem $cubeDashboardItem)
    {
        $data = $request->validated();
        $cubeDashboardItem = $this->updateCubeDashboardItem($cubeDashboardItem, $data);

        return new CubeDashboardItemResource($cubeDashboardItem);
    }

    private function updateCubeDashboardItem(CubeDashboardItem $cubeDashboardItem, array $data)
    {
        $cubeDashboardItem->fill($data);
        $cubeDashboardItem->save();

        return $cubeDashboardItem;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CubeDashboardItem  $cubeDashboardItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization, Cube $cube, CubeDashboardItem $cubeDashboardItem)
    {
        return [
            'success' => $cubeDashboardItem->delete(),
        ];
    }

    private function getRelationshipsToLoad()
    {
        return [];
    }
}
