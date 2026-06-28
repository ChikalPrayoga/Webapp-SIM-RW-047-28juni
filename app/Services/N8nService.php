<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class N8nService
{
    protected bool $enabled;
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->enabled = config('ai.enabled', false);
        $this->baseUrl = config('ai.n8n.base_url');
        $this->timeout = config('ai.n8n.timeout', 30);
    }

    /**
     * Send a ping to the n8n test connection webhook.
     *
     * @return array
     */
    public function ping(): array
    {
        // Fail gracefully if integration is disabled
        if (!$this->enabled) {
            return [
                'success' => false,
                'status' => 503,
                'message' => 'N8n Integration is currently disabled.'
            ];
        }

        $endpoint = rtrim($this->baseUrl, '/') . '/webhook/ai/test-connection';

        try {
            $response = Http::timeout($this->timeout)->post($endpoint);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->status(),
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'status' => $response->status(),
                'message' => 'N8n request failed: ' . $response->body(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'status' => 500,
                'message' => 'N8n connection error: ' . $e->getMessage(),
            ];
        }
    }
}
