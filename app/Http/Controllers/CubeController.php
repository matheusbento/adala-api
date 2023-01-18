<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCubeRequest;
use App\Http\Resources\CubeFullResource;
use App\Http\Resources\CubeResource;
use App\Models\Cube;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
            $builder->where('name', 'LIKE', "%{$search}%");
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
        $data['identifier'] = Str::random(50);
        $cube = $this->updateCube(new Cube(), $data);

        return new CubeFullResource($cube->load(['metadata']));
    }

    /**
     * get the specified resource in storage.
     *
     * @param  \App\Models\Cube  $cube
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization, Cube $cube)
    {
        return new CubeFullResource($cube->load(['metadata']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreCubeRequest  $request
     * @param  \App\Models\Cube  $cube
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCubeRequest $request, Organization $organization, Cube $cube)
    {
        abort_if($organization->id !== $cube->organization_id, 402, 'You don\'t belongs to this organization!');
        $data = $request->validated();
        $cube = $this->updateCube($cube, $data);

        return new CubeFullResource($cube->load(['metadata']));
    }

    private function updateCube(Cube $cube, array $data)
    {
        $cube->fill($data);
        // dd($cube);

        $model = Arr::get($data, 'model');

        $cube->save();

        $cube->metadata()->delete();

        // update metadata
        $metadata = [];
        $metadata[] = [
            'field' => 'start_date',
            'value' => Arr::get($data, 'start_date'),
        ];
        $metadata[] = [
            'field' => 'end_date',
            'value' => Arr::get($data, 'end_date'),
        ];
        $metadata = array_merge($metadata, Arr::get($data, 'metadata'));
        $cube->metadata()->createMany($metadata);

        // update model
        $model['cubes'][0]['id'] = $cube->id;
        $model['cubes'][0]['name'] = $cube->identifier;

        $model['cubes'][0]['infos'] = [];
        foreach ($metadata as $value) {
            $model['cubes'][0]['infos'][$value['field']] = $value['value'];
        }

        // dd($model);
        $cube->model = $model;

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

    private function getRelationshipsToLoad()
    {
        return [];
    }
}
