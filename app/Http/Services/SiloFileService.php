<?php

namespace App\Http\Services;

use App\Http\Facades\DataProcessingService;
use App\Http\Facades\PreProcessingService;
use App\Jobs\ProcessDataflowByIdJob;
use App\Models\SiloFile;
use App\Models\SiloFileAttributes;
use Exception;

class SiloFileService
{
    public function processDataflowFile(SiloFile $siloFile)
    {
        $siloFile->setStatus(SiloFile::PROCESSING_STATUS);
        $cubeIdentifier = 'SILO_' . $siloFile->folder->id;
        $objectPaths = [['id' => $siloFile->id, 'path' => $siloFile->file->path]];
        $objectColumns = $siloFile->attributes->all();

        try {
            DataProcessingService::get($cubeIdentifier, $objectPaths, $objectColumns, ['dataflow' => true]);
            $siloFile->setStatus(SiloFile::READY_FOR_USE_STATUS);
        } catch (Exception $e) {
            $siloFile->setStatus(SiloFile::PROCESSING_ERROR_STATUS, $e->getMessage());
        }

    }
    public function preProcessByIds(array $siloFileIds)
    {
        $siloFiles = SiloFile::whereIn('id', $siloFileIds)->get();

        foreach ($siloFiles as $key => $siloFile) {
            $siloFile->setStatus(SiloFile::PRE_PROCESSING_STATUS);
            $objectPath = $siloFile->file->path;

            try {
                $response = PreProcessingService::get($objectPath);

                $entities = $response->entities;
                $complexKeys = $response->complex_keys;

                foreach ($entities as $key => $entity) {
                    $data = $siloFile->attributes()->where('name', $key)->where('type', SiloFileAttributes::TYPE_TABLE)->first();
                    if ($data) {
                        $data->update([
                            'attributes' => $entity,
                        ]);
                    } else {
                        $siloFile->attributes()->create([
                            'type' => SiloFileAttributes::TYPE_TABLE,
                            'name' => $key,
                            'attributes' => $entity,
                        ]);
                    }
                }

                foreach ($complexKeys as $key => $complexKey) {
                    $data = $siloFile->attributes()->where('name', $key)->where('type', SiloFileAttributes::TYPE_COMPLEX_KEY)->first();
                    if ($data) {
                        $data->update([
                            'attributes' => $complexKey,
                        ]);
                    } else {
                        $siloFile->attributes()->create([
                            'type' => SiloFileAttributes::TYPE_COMPLEX_KEY,
                            'name' => $key,
                            'attributes' => $complexKey,
                        ]);
                    }
                }

                $siloFile->setStatus(SiloFile::READY_FOR_USE_STATUS);

                if($siloFile->folder->is_dataflow) {
                    dispatch(new ProcessDataflowByIdJob($siloFile));
                }
            } catch (Exception $e) {
                $siloFile->setStatus(SiloFile::INVALID_STATUS, $e->getMessage());
            }
        }
    }
}
