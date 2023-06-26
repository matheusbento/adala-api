<?php

namespace App\Http\Services;

use App\Http\Facades\CubeProcessingService;
use App\Models\Cube;
use Exception;

class CubeService
{
    public function processByIds(array $cubeIds)
    {
        $cubes = Cube::whereIn('id', $cubeIds)->get();

        foreach ($cubes as $key => $cube) {
            $cube->setStatus(Cube::CREATING_STATUS);
            $cubeIdentifier = $cube->identifier;
            $objectPaths = $cube->files->map(fn ($file) => ['id' => $file->id, 'path' => $file->file->path])->all();
            $objectColumns = $cube->attributes->pluck('attributes')->all();

            try {
                CubeProcessingService::get($cubeIdentifier, $objectPaths, $objectColumns);
                $cube->setStatus(Cube::READY_TO_ANALYSIS_STATUS);
            } catch (Exception $e) {
                $cube->setStatus(Cube::CREATING_ERROR_STATUS, $e->getMessage());
            }
        }
    }
}
