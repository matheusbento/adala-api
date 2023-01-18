<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Models\ImportMetadata;

/**
 * Trait UseMetadata
 * @package App\Models\Traits
 *
 */
trait UseImportMetadata
{
    public function importedMetadata(): MorphOne
    {
        return $this->morphOne(ImportMetadata::class, 'model');
    }

    /*
     * setImportMetadata
     *
     * @param array $data
     * @param string $fileName
     * @return Model
     */
    public function setImportMetadata(array $data, string $fileName, ?int $batchNumber = null): Model
    {
        $number = $batchNumber ?? ImportMetadata::getNextBatchNumberFor($this);
        if ($this->importedMetadata()->exists()) {
            $metadata = $this->importedMetadata;
            $metadata->fill([
                'raw_data' => $data,
                'batch' => $number,
                'file_name' => $fileName,
            ]);
            $metadata->save();
            return $metadata;
        }
        return $this->importedMetadata()->create(
            [
                'raw_data' => $data,
                'batch' => $number,
                'file_name' => $fileName,
            ]
        );
    }
}
