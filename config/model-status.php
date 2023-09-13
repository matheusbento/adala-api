<?php

use App\Models\Cube;
use App\Models\SiloFile;

return [
    Cube::class => [
        'primary' => [
            Cube::CREATING_STATUS,
            Cube::READY_TO_ANALYSIS_STATUS,
            Cube::CREATING_ERROR_STATUS,
            Cube::INVALID_STATUS,
        ],
        'secondary' => [

        ],
    ],
    SiloFile::class => [
        'primary' => [
            SiloFile::CREATED_FILE_STATUS,
            SiloFile::PRE_PROCESSING_STATUS,
            SiloFile::READY_FOR_USE_STATUS,
            SiloFile::PROCESSING_STATUS,
            SiloFile::INVALID_STATUS,
            SiloFile::PROCESSING_ERROR_STATUS,
        ],
        'secondary' => [

        ],
    ],
];
