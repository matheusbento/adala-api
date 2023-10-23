<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiloFolderRequest;
use App\Http\Resources\SiloFolderResource;
use App\Models\Organization;
use App\Models\SiloFolder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SiloFolderController extends Controller
{
    public function formatSizeUnits($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = 0;
        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }
        return round($bytes, 2) . ' ' . $units[$index];
    }

    /**
     * summary the specified resource.
     *
     * @param  \App\Models\CubeDashboardItem  $cubeDashboardItem
     * @return \Illuminate\Http\Response
     */
    public function summary(Request $request, Organization $organization)
    {
        $organization->load(['folders.files.file', 'folders.accesses']);
        $silos = $organization->folders;
        $groupedByDate = [];
        $groupedBySilo = (object) ['x' => [], 'y' => []];
        $viewBySilo = (object) ['x' => [], 'y' => []];
        $siloFiles = $silos->map(function ($silo) use ($groupedBySilo) {
            $groupedBySilo->y[] = $silo->files->map->file->pluck('size')->avg() ?? 0;
            $groupedBySilo->x[] = $silo->name;
            return $silo->files;
        })->flatten();

        $files = $siloFiles->unique('id')->map->file;
        $filesGroupedByDate = $siloFiles->groupBy(fn ($file) => $file->created_at->format('Y-m-d'));
        $totalInUse = $files->pluck('size')->sum();

        // Caminho para o diretório raiz do disco local (pode variar dependendo do seu sistema)
        $diskPath = storage_path('app');

        // Obtenha o tamanho total do disco local
        $totalSpace = disk_total_space($diskPath);

        $diskUsedBySilo = $totalInUse / $totalSpace * 100;

        // Health

        $currentDate = Carbon::now();

        // Subtract 1 month from the current date
        $oneMonthAgo = $currentDate->copy()->subMonth();

        // Create a date period using CarbonPeriod
        $datePeriod = CarbonPeriod::create($oneMonthAgo, '1 day', $currentDate);

        foreach ($silos as $key => $silo) {
            $filesGroupedByDate = $silo->files->groupBy(fn ($file) => $file->created_at->format('Y-m-d'));
            $x = [];
            $y = [];
            foreach ($datePeriod as $key => $date) {
                $filesByDate = $filesGroupedByDate[$date->format('Y-m-d')]->map->file ?? collect([]);
                $y[] = $filesByDate->pluck('size')->avg() ?? 0;
                $x[] = $date->format('d-m-Y');
            }

            $groupedByDate[] = ['x' => $x, 'y' => $y, 'name' => $silo->name];
            // dd($x, $y);
        }

        $usersUsing = collect([]);

        foreach ($silos as $key => $silo) {
            $accessesLastMonth = $silo->accesses()->where('created_at', '>=', $oneMonthAgo)->get();
            $usersUsing->add($accessesLastMonth->unique('user_id')->all());
            $viewBySilo->y[] = $accessesLastMonth->count();
            $viewBySilo->x[] = $silo->name;
        }

        $totalUsers = $organization->users->count();

        $users = $usersUsing->flatten()->pluck('user_id')->unique()->count();

        $usersUsingSilo = $users / $totalUsers * 100;

        return [
            'disk_percent_in_use' => $diskUsedBySilo,
            'users_percent_in_use' => $usersUsingSilo,
            'files_size_by_silo' => $groupedBySilo,
            'files_size_by_date' => $groupedByDate,
            'view_per_silo' => $viewBySilo,
        ];
    }


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
