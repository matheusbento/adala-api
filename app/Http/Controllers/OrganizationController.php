<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
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

        $builder = Organization::whereIAmOwner()->orWhereHas('users', function ($q) {
            $q->where('user_id', Auth::user()->id);
        })->with($this->getRelationshipsToLoad())
            ->withCount($this->getRelationshipsToLoad())
            ->orderBy($request->input('order_by', 'name'), $request->input('direction', 'asc'));

        if ($search = $request->input('q')) {
            $builder->where('name', 'LIKE', "%{$search}%");
        }

        $organizations = $request->input('all') ? $builder->get() : $builder->paginate($request->input('per_page', intval(config('general.pagination_size'))))
            ->appends($request->only(['per_page', 'order_by', 'direction', 'q']));

        return OrganizationResource::collection($organizations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrganizationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrganizationRequest $request)
    {
        $data = $request->validated();
        $data['owner_id'] = Auth::user()->id;
        $cube = $this->updateOrganization(new Organization(), $data);

        return new OrganizationResource($cube);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        return new OrganizationResource($organization);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrganizationRequest  $request
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function update(StoreOrganizationRequest $request, Organization $organization)
    {
        $data = $request->validated();
        $organization = $this->updateOrganization($organization, $data);

        return new OrganizationResource($organization);
    }

    private function updateOrganization(Organization $organization, array $data)
    {
        $organization->fill($data);
        $organization->save();

        return $organization;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization)
    {
        return [
            'success' => $organization->delete(),
        ];
    }

    private function getRelationshipsToLoad()
    {
        return [];
    }
}
