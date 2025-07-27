<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only modify JSON responses
        if (!$response instanceof JsonResponse) {
            return $response;
        }

        $data = $response->getData(true);
        $statusCode = $response->getStatusCode();

        // If response already has success field, don't modify
        if (isset($data['success'])) {
            return $response;
        }

        // Handle different response types
        if ($statusCode >= 200 && $statusCode < 300) {
            // Success responses
            $formattedData = [
                'success' => true,
                'data' => $data,
                'message' => $this->getSuccessMessage($statusCode),
            ];

            // Add pagination meta if present
            if (isset($data['data']) && isset($data['meta'])) {
                $formattedData['data'] = $data['data'];
                $formattedData['meta'] = $data['meta'];
            }

            $response->setData($formattedData);
        } elseif ($statusCode >= 400) {
            // Error responses
            $errorCode = $this->getErrorCode($statusCode);
            $errorMessage = $this->getErrorMessage($statusCode, $data);

            $formattedData = [
                'success' => false,
                'error' => [
                    'code' => $errorCode,
                    'message' => $errorMessage,
                    'details' => $this->formatErrorDetails($data),
                ],
                'status_code' => $statusCode,
            ];

            $response->setData($formattedData);
        }

        return $response;
    }

    /**
     * Get success message based on status code
     */
    private function getSuccessMessage(int $statusCode): string
    {
        return match ($statusCode) {
            200 => 'Request successful',
            201 => 'Resource created successfully',
            202 => 'Request accepted',
            204 => 'Resource deleted successfully',
            default => 'Operation completed successfully',
        };
    }

    /**
     * Get error code based on status code
     */
    private function getErrorCode(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'BAD_REQUEST',
            401 => 'UNAUTHORIZED',
            403 => 'FORBIDDEN',
            404 => 'NOT_FOUND',
            405 => 'METHOD_NOT_ALLOWED',
            422 => 'VALIDATION_ERROR',
            429 => 'RATE_LIMIT_EXCEEDED',
            500 => 'INTERNAL_SERVER_ERROR',
            502 => 'BAD_GATEWAY',
            503 => 'SERVICE_UNAVAILABLE',
            default => 'UNKNOWN_ERROR',
        };
    }

    /**
     * Get error message based on status code and data
     */
    private function getErrorMessage(int $statusCode, $data): string
    {
        // If data already has a message, use it
        if (is_array($data) && isset($data['message'])) {
            return $data['message'];
        }

        return match ($statusCode) {
            400 => 'Bad request',
            401 => 'Unauthorized access',
            403 => 'Access forbidden',
            404 => 'Resource not found',
            405 => 'Method not allowed',
            422 => 'The given data was invalid',
            429 => 'Too many requests',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
            default => 'An error occurred',
        };
    }

    /**
     * Format error details
     */
    private function formatErrorDetails($data): array
    {
        if (!is_array($data)) {
            return [];
        }

        // Handle validation errors
        if (isset($data['errors'])) {
            return $data['errors'];
        }

        // Handle other error details
        $details = [];
        foreach ($data as $key => $value) {
            if ($key !== 'message' && $key !== 'status_code') {
                $details[$key] = $value;
            }
        }

        return $details;
    }
} 