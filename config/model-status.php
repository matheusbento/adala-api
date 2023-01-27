<?php

use App\Models\SiloFile;

return [
    SiloFile::class => [
        'primary' => [
            SiloFile::CREATED_FILE_STATUS,
            SiloFile::PRE_PROCESSING_STATUS,
            SiloFile::READY_FOR_USE_STATUS,
            SiloFile::PROCESSING_STATUS,
            SiloFile::INVALID_STATUS,
        ],
        'secondary' => [

        ],
    ],
];
