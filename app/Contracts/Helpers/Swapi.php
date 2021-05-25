<?php

namespace App\Contracts\Helpers;

use Illuminate\Support\Facades\Http;

class Swapi
{
    public function getResponse(string $swapi)
    {
        try {
            $response = Http::get($swapi);

            return $this->getDecodedResponse($response);
        } catch (\Exception $exception){
            return response()->json([
                'message' => 'Service Unavailable.'
            ], 503);
        }
    }

    private function getDecodedResponse($response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}