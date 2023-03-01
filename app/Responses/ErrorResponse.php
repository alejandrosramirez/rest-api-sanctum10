<?php

namespace App\Responses;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Throwable;

class ErrorResponse implements Responsable
{
    public function __construct(
        private string $error,
        private string|object|array $data,
        private ?Throwable $exc = null,
        private ?int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        private ?array $headers = []
    )
    {}

    public function toResponse($request): Response|ResponseFactory|JsonResponse
    {
        $response = [
            'error' => $this->error,
            'message' => $this->data,
            'code' => $this->code,
        ];

        if ($this->exc != null && config('app.debug')) {
            $response['debug'] = [
                'message' => $this->exc->getMessage(),
                'file' => $this->exc->getFile(),
                'line' => $this->exc->getLine(),
                'trace' => $this->exc->getTraceAsString(),
            ];
        }

        return response()->json($response, $this->code, $this->headers);
    }
}
