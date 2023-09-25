<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrganizationUserController extends Controller
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

        $builder = $organization->users()->with($this->getRelationshipsToLoad())
            ->withCount($this->getRelationshipsToLoad())
            ->orderBy($request->input('order_by', 'id'), $request->input('direction', 'desc'));

        if ($search = $request->input('q')) {
            $builder->where('name', 'LIKE', "%{$search}%");
        }

        $users = $request->input('all') ? $builder->get() : $builder->paginate($request->input('per_page', intval(config('general.pagination_size'))))
            ->appends($request->only(['per_page', 'order_by', 'direction', 'q']));

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request, Organization $organization)
    {
        $data = $request->validated();

        $user = $this->updateOrganizationUser($organization, new User(), $data);

        return new UserResource($user);
    }

    /**
     * get the specified resource in storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Organization $organization, User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUserRequest $request, Organization $organization, User $user)
    {
        // abort_if($organization->id !== $user->organization_id, 402, 'You don\'t belongs to this organization!');
        $data = $request->validated();
        $user = $this->updateOrganizationUser($organization, $user, $data);
        return new UserResource($user);
    }

    private function updateOrganizationUser(Organization $organization, User $user, array $data)
    {
        $user->fill($data);
        $user->save();

        if (isset($data['direct_permissions'])) {
            $user->syncPermissions($data['direct_permissions'], $organization);
        }

        $user->organizations()->syncWithoutDetaching($organization);

        $user = $user->refresh();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization, User $user)
    {
        // if ($user->organization->id !== $organization->id) {
        //     return response()->json(['message' => 'Invalid cube for this organization.'], 400);
        // }

        return [
            'success' => $user->delete(),
        ];
    }

    private function getRelationshipsToLoad()
    {
        return [];
    }
}
