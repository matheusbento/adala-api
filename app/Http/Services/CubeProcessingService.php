<?php

namespace App\Http\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class CubeProcessingService
{
    public function get(string $identifier, array $filesPaths, array $cubeAttributes)
    {
        try {
            $url = env('PROCESSING_HANDLER_URL', null);
            if (!$url) {
                throw new Exception('PROCESSING API URL NOT DEFINED');
            }

            // dd($url, [
            //     'identifier' => $identifier,
            //     'object_paths' => json_encode($filesPaths),
            //     'attributes' => json_encode($cubeAttributes),
            // ]);
            $response = Http::timeout(3600)->get($url, [
                'identifier' => $identifier,
                'object_paths' => json_encode($filesPaths),
                'attributes' => json_encode($cubeAttributes),
            ]);

            if($response->status() == 500) {
                throw new Exception("File doesn't exists");
            }

            return (object) $response->json();
        } catch (Exception $e) {
            dump($e);
            throw $e;
        }
    }
}
