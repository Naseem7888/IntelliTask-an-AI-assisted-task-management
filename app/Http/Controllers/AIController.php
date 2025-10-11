<?php

namespace App\Http\Controllers;

use App\Http\Requests\AISuggestionRequest;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class AIController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Generate task suggestions based on a description.
     *
     * @param AISuggestionRequest $request
     * @return JsonResponse
     */
    public function suggestTasks(AISuggestionRequest $request): JsonResponse
    {
        try {
            $suggestions = $this->geminiService->generateTaskBreakdown($request->validated('description'));
            return response()->json([
                'suggestions' => $suggestions,
                'model' => config('gemini.model'),
            ], 200);
        } catch (Exception $e) {
            Log::error('AI Suggestion Error', [
                'user_id' => auth()->id(),
                'request_id' => request()->header('X-Request-Id') ?? (string) Str::uuid(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Optional: for more detailed debugging
            ]);
            return response()->json(['error' => 'Failed to generate suggestions from the AI service.'], 500);
        }
    }

    /**
     * Break down a complex task.
     * This is an alias for suggestTasks for semantic clarity.
     *
     * @param AISuggestionRequest $request
     * @return JsonResponse
     */
    public function breakdownTask(AISuggestionRequest $request): JsonResponse
    {
        return $this->suggestTasks($request);
    }

    /**
     * Clear cached suggestions for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function clearSuggestions(Request $request): JsonResponse
    {
        // This is a placeholder for a more robust implementation.
        // For example, if we cached based on user and description hash:
        // We can't clear a specific user's suggestions without knowing all their descriptions.
        // A better approach would be to use cache tags.
        if (method_exists(Cache::store()->getStore(), 'tags')) {
            $userId = $request->user()->id;
            Cache::tags(["ai", "user:{$userId}"])->flush();
            return response()->json(['message' => 'User-specific AI suggestions cache cleared.']);
        }

        return response()->json(['message' => 'Cache driver does not support tags. No action taken.'], 200);
    }
}