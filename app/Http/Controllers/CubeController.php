<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCubeRequest;
use App\Http\Requests\UpdateCubeRequest;
use App\Http\Resources\CubeResource;
use App\Models\Cube;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CubeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Organization $organization)
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

        $builder = $organization->cubes()->with($this->getRelationshipsToLoad())
            ->withCount($this->getRelationshipsToLoad())
            ->orderBy($request->input('order_by', 'name'), $request->input('direction', 'asc'));

        if ($search = $request->input('q')) {
            $builder->where('name', 'LIKE', "%$search%");
        }

        $cubes = $request->input('all') ? $builder->get() : $builder->paginate($request->input('per_page', intval(config('general.pagination_size'))))
            ->appends($request->only(['per_page', 'order_by', 'direction', 'q']));

        return CubeResource::collection($cubes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCubeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCubeRequest $request, Organization $organization)
    {
        $data = $request->validated();
        $data['organization_id'] = $organization->id;
        $cube = $this->updateCube(new Cube(), $data);

        return new CubeResource($cube);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCubeRequest  $request
     * @param  \App\Models\Cube  $cube
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCubeRequest $request, Cube $cube)
    {
        $data = $request->validated();
        $cube = $this->updateCube($cube, $data);

        return new CubeResource($cube);
    }

    private function updateCube(Cube $cube, array $data){
        $cube->fill($data);
        $cube->save();

        return $cube;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cube  $cube
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cube $cube, Organization $organization)
    {
        if ($cube->organization !== $organization->id) {
            return response()->json(['message' => 'Invalid cube for this organization.'], 400);
        }

        return [
          'success' => $cube->delete(),
      ];
    }

    private function getRelationshipsToLoad() {
        return [];
    }
}
