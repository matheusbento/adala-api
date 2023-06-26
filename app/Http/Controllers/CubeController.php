<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCubeRequest;
use App\Http\Resources\CubeResource;
use App\Jobs\ProcessLoadCubeByIdJob;
use App\Models\Cube;
use App\Models\Organization;
use App\Models\SiloFile;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
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
            ->orderBy($request->input('order_by', 'id'), $request->input('direction', 'desc'));

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
        $cube = $this->updateCube(new Cube(), $data);

        $folder = App::make('fakeid')->decode($request->get('folder'));
        $columns = $request->get('columns', []);
        $filesEncoded = collect(array_keys($columns));

        $files = $filesEncoded->map(fn ($file) => SiloFile::find(App::make('fakeid')->decode($file)));
        $cube->files()->attach($files->pluck('id')->all());

        foreach ($columns as $key => $column) {
            $fileId = App::make('fakeid')->decode($key);
            $file = $files->where('id', $fileId)->first();
                $cube->attributes()->create([
                    'type' => get_class($file),
                    'name' => $file->id,
                    'attributes' => ["file_id" => $file->id, "attributes" => $column],
                ]);
        }

        $cube->folders()->attach([$folder]);

        $cube->setStatus(Cube::CREATING_STATUS);
        $historyType = Cube::CREATED_HISTORY_TYPE;
        $cube->addHistory($historyType, Cube::HISTORY_MESSAGES[$historyType]);

        dispatch(new ProcessLoadCubeByIdJob($cube));

        return new CubeResource($cube->load(['metadata']));
    }

    /**
     * get the specified resource in storage.
     *
     * @param  \App\Models\Cube  $cube
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization, Cube $cube)
    {
        return new CubeResource($cube->load(['metadata', 'history', 'files']));
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

        $historyType = Cube::EDITED_HISTORY_TYPE;
        $cube->addHistory($historyType, Cube::HISTORY_MESSAGES[$historyType]);

        return new CubeResource($cube->load(['metadata']));
    }

    private function updateCube(Cube $cube, array $data)
    {
        $cube->fill($data);

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
        $metadata = array_merge($metadata, Arr::get($data, 'metadata', []));
        $cube->metadata()->createMany($metadata);

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
