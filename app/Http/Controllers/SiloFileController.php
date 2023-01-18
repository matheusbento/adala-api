<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiloFileRequest;
use App\Http\Resources\SiloFileResource;
use App\Models\File;
use App\Models\Organization;
use App\Models\SiloFile;
use App\Models\SiloFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SiloFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Organization $organization, SiloFolder $folder)
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

        $builder = $folder->files()->with($this->getRelationshipsToLoad())
            ->withCount($this->getRelationshipsToLoad())
            ->orderBy($request->input('order_by', 'name'), $request->input('direction', 'asc'));

        if ($search = $request->input('q')) {
            $builder->where('name', 'LIKE', "%{$search}%");
        }

        $folders = $request->input('all') ? $builder->get() : $builder->paginate($request->input('per_page', intval(config('general.pagination_size'))))
            ->appends($request->only(['per_page', 'order_by', 'direction', 'q']));

        return SiloFileResource::collection($folders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSiloFileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSiloFileRequest $request, Organization $organization, SiloFolder $folder)
    {
        $data = $request->validated();
        $data['owner_id'] = Auth::user()->id;
        $data['folder_id'] = $folder->id;
        $cube = $this->updateSiloFile(new SiloFile(), $organization, $request, $data);

        return new SiloFileResource($cube);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SiloFile  $folder
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization, SiloFolder $folder, SiloFile $file)
    {
        return new SiloFileResource($file->load(['file']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreSiloFileRequest  $request
     * @param  \App\Models\SiloFile  $file
     * @return \Illuminate\Http\Response
     */
    public function update(StoreSiloFileRequest $request, Organization $organization, SiloFolder $folder, SiloFile $file)
    {
        $data = $request->validated();
        $file = $this->updateSiloFile($file, $organization, $request, $data);

        return new SiloFileResource($file);
    }

    private function updateSiloFile(SiloFile $siloFile, Organization $organization, Request $request, array $data)
    {
        $siloFile->fill($data);

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            /* Check file type */
            $file_mime = $file->getMimeType();
            abort_if(!in_array($file_mime, SiloFile::ACCEPTABLE_FILE_TYPES), 402, 'File type not allowed. Please try again.');

            /* Check file size */
            $file_size = $file->getSize();
            abort_if($file_size > config('filesystems.max_size.silo'), 402, 'File size is too large. Please try again.');

            $uploadedDocument = $siloFile->uploadFile($request->file('file'));

            $documentFile = File::create(
                array_merge($uploadedDocument, [
                    'owner_type' => Organization::class,
                    'owner_id' => $organization->id,
                    'created_by_user_id' => $request->user()->id,
                    'file_type' => SiloFile::FILE_TYPE,
                ])
            );

            $siloFile->file_id = $documentFile->id;
            $siloFile->file_uploaded_at = new Carbon();
        } else {
            $documentFile = $siloFile->file;
        }

        $documentFile->tags()->sync($request->input('tags'));

        $siloFile->save();

        return $siloFile;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SiloFile  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization, SiloFolder $folder, SiloFile $file)
    {
        return [
            'success' => $file->delete(),
        ];
    }

    public function download(Request $request, Organization $organization, SiloFolder $folder, SiloFile $file)
    {
        if ($file->file->owner_id !== $organization->id || $file->file->owner_type !== Organization::class) {
            return response()->json(['message' => 'file not found for this employer'], 404);
        }

        return SiloFile::download($file->file->path);
    }

    private function getRelationshipsToLoad()
    {
        return [];
    }
}
