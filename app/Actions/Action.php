<?php

namespace App\Actions;

use Illuminate\Http\Resources\Json\JsonResource;
use Lorisleiva\Actions\Concerns\AsAction;

abstract class Action
{
    use AsAction;

    public function success(mixed $result, string $message = null): JsonResource
    {
        $response = [
            'success' => true,
            'data' => $result,
        ];

        if (! is_null($message)) {
            $response['message'] = $message;
        }

        return JsonResource::make($response);
    }

    public function error(string $message, array $data = [], $code = 404): JsonResource
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (! empty($data)) {
            $response['data'] = $data;
        }

        return JsonResource::make($response);
    }
}
