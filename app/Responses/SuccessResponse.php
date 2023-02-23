<?php

namespace App\Responses;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SuccessResponse implements Responsable
{
    /**
     * @param  string  $message
     * @param  array  $data
     * @param  int  $code
     * @param  array  $headers
     */
    public function __construct(
        private string $message,
        private ?array $data = [],
        private ?int $code = Response::HTTP_OK,
        private ?array $headers = []
    )
    {}

    public function toResponse($request): Response|ResponseFactory|JsonResponse
    {
        $response = [
            'message' => $this->message,
        ];

        return response()->json($response, $this->code, $this->headers);
    }
}
