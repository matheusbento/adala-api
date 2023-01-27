<?php

namespace App\Http\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class PreProcessingService
{
    public function get(string $path)
    {
        try {
            $url = env('PRE_PROCESSING_HANDLER_URL', null);
            if (!$url) {
                throw new Exception('PRE PROCESSING API URL NOT DEFINED');
            }

            $response = Http::get($url, [
                'object_path' => $path,
            ]);

            if($response->status() == 500){
                throw new Exception("File doesn't exists");
            }

            return (object) $response->json();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
