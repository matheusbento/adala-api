<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiloFolderRequest;
use App\Http\Resources\SiloFolderResource;
use App\Models\Organization;
use App\Models\SiloFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SiloFolderController extends Controller
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

        $builder = $organization->folders()->with($this->getRelationshipsToLoad())
            ->withCount($this->getRelationshipsToLoad())
            ->orderBy($request->input('order_by', 'id'), $request->input('direction', 'desc'));

        if ($search = $request->input('q')) {
            $builder->where('name', 'LIKE', "%{$search}%");
        }

        $folders = $request->input('all') ? $builder->get() : $builder->paginate($request->input('per_page', intval(config('general.pagination_size'))))
            ->appends($request->only(['per_page', 'order_by', 'direction', 'q']));

        return SiloFolderResource::collection($folders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSiloFolderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSiloFolderRequest $request, Organization $organization)
    {
        $data = $request->validated();
        $data['owner_id'] = Auth::user()->id;
        $data['organization_id'] = $organization->id;
        $cube = $this->updateSiloFolder(new SiloFolder(), $data);

        return new SiloFolderResource($cube);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SiloFolder  $folder
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization, SiloFolder $folder)
    {
        return new SiloFolderResource($folder);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSiloFolderRequest  $request
     * @param  \App\Models\SiloFolder  $folder
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSiloFolderRequest $request, Organization $organization, SiloFolder $folder)
    {
        $data = $request->validated();
        $folder = $this->updateSiloFolder($folder, $data);

        return new SiloFolderResource($folder);
    }

    private function updateSiloFolder(SiloFolder $folder, array $data)
    {
        $folder->fill($data);
        $folder->save();

        return $folder;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SiloFolder  $folder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization, SiloFolder $folder)
    {
        return [
            'success' => $folder->delete(),
        ];
    }

    private function getRelationshipsToLoad()
    {
        return ['category'];
    }
}
