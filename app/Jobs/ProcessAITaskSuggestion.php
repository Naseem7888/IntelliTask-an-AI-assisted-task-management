<?php

namespace App\Jobs;

use App\Services\GeminiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Throwable;

class ProcessAITaskSuggestion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $taskDescription;
    public int $userId;
    public string $requestId;

    public function __construct(string $taskDescription, int $userId, string $requestId)
    {
        $this->taskDescription = $taskDescription;
        $this->userId = $userId;
        $this->requestId = $requestId;
    }

    public function handle(GeminiService $geminiService): void
    {
        try {
            $suggestions = $geminiService->generateTaskBreakdown($this->taskDescription);
            $cacheKey = "ai_suggestions_{$this->userId}_{$this->requestId}";
            Cache::put($cacheKey, ['status' => 'completed', 'data' => $suggestions], now()->addMinutes(30));
            Log::info("AI suggestions generated for user {$this->userId}, request {$this->requestId}");
        } catch (Exception $e) {
            Log::error("AI Job failed for user {$this->userId}: " . $e->getMessage());
            $this->fail($e);
        }
    }

    public function failed(Throwable $exception): void
    {
        $cacheKey = "ai_suggestions_{$this->userId}_{$this->requestId}";
        Cache::put($cacheKey, ['status' => 'failed', 'error' => $exception->getMessage()], now()->addMinutes(30));
        Log::error("ProcessAITaskSuggestion job failed for user {$this->userId}. Request ID: {$this->requestId}. Error: {$exception->getMessage()}");
    }
}
