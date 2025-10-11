<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Exception;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Cache;

class AISuggestion extends Component
{
    public string $taskDescription = '';
    public array $suggestions = [];
    public bool $isLoading = false;
    public ?string $error = null;
    public bool $showSuggestions = false;
    public array $selectedSuggestions = [];

    public function generateSuggestions()
    {
        $this->validate([
            'taskDescription' => 'required|string|min:10|max:500',
        ]);

        $this->isLoading = true;
        $this->error = null;
        $this->suggestions = [];
        $this->selectedSuggestions = [];
        $this->showSuggestions = true;

        try {
            $useMock = (bool) config('ai.mock_responses', false);
            $enabled = (bool) config('ai.suggestions_enabled', true);
            $max = (int) config('ai.max_suggestions', 5);

            if (!$enabled) {
                throw new Exception('AI suggestions are disabled by configuration.');
            }

            if ($useMock) {
                $list = $this->generateSimpleSuggestions($this->taskDescription);
            } else {
                $cacheKey = 'ai_suggestions:' . md5($this->taskDescription);
                $ttl = (int) config('ai.cache_ttl', 3600);
                $list = Cache::remember($cacheKey, $ttl, function () {
                    $service = app(GeminiService::class);
                    return $service->generateTaskBreakdown($this->taskDescription);
                });
            }

            // Normalize to array of strings for the blade that prints {{ $suggestion }}
            $list = array_map(function ($item) {
                if (is_array($item)) {
                    return $item['title'] ?? ($item['description'] ?? json_encode($item));
                }
                return (string) $item;
            }, $list);

            // Truncate to max suggestions and set state
            $this->suggestions = array_slice($list, 0, max(1, $max));
            $this->selectAllSuggestions();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        } finally {
            $this->isLoading = false;
        }
    }

    private function generateSimpleSuggestions($description)
    {
        // Simple suggestion generation based on keywords
        $suggestions = [];
        
        if (str_contains(strtolower($description), 'project')) {
            $suggestions[] = ['title' => 'Plan project timeline', 'description' => 'Create detailed project schedule'];
            $suggestions[] = ['title' => 'Define project requirements', 'description' => 'List all project requirements'];
            $suggestions[] = ['title' => 'Assign project team roles', 'description' => 'Delegate tasks to team members'];
        } elseif (str_contains(strtolower($description), 'meeting')) {
            $suggestions[] = ['title' => 'Prepare meeting agenda', 'description' => 'Create agenda with key topics'];
            $suggestions[] = ['title' => 'Send meeting invitations', 'description' => 'Invite all relevant participants'];
            $suggestions[] = ['title' => 'Book meeting room', 'description' => 'Reserve appropriate meeting space'];
        } else {
            $suggestions[] = ['title' => 'Research and planning', 'description' => 'Gather information and create plan'];
            $suggestions[] = ['title' => 'Implementation phase', 'description' => 'Execute the main task'];
            $suggestions[] = ['title' => 'Review and finalize', 'description' => 'Review completed work and finalize'];
        }

        return $suggestions;
    }

    public function selectSuggestion($index)
    {
        if (isset($this->selectedSuggestions[$index])) {
            unset($this->selectedSuggestions[$index]);
        } else {
            $this->selectedSuggestions[$index] = $this->suggestions[$index];
        }
    }

    public function selectAllSuggestions()
    {
        $this->selectedSuggestions = $this->suggestions;
    }

    public function clearSuggestions()
    {
        $this->taskDescription = '';
        $this->suggestions = [];
        $this->selectedSuggestions = [];
        $this->showSuggestions = false;
        $this->error = null;
        $this->isLoading = false;
    }

    public function createTasksFromSuggestions()
    {
        if (empty($this->selectedSuggestions)) {
            return;
        }

        $user = Auth::user();

        foreach ($this->selectedSuggestions as $suggestion) {
            // Selected suggestions are normalized to string titles
            $title = is_array($suggestion) ? ($suggestion['title'] ?? (string) reset($suggestion)) : (string) $suggestion;
            Task::create([
                'title' => $title,
                'description' => null,
                'user_id' => $user->id,
                'status' => Task::STATUS_PENDING,
            ]);
        }

        $this->dispatch('tasks-created');
        $this->clearSuggestions();
    }

    public function surpriseMe()
    {
        $examples = [
            'Plan a product launch in 6 weeks for our SaaS app; include timeline, marketing, website updates, PR, webinar, KPIs.',
            'Migrate the company website from shared hosting to AWS with zero downtime and a rollback plan.',
            'Implement feature flags in the Laravel app for gradual rollout; include config, middleware, and kill‑switch.',
            'Set up CI/CD with GitHub Actions for Laravel + MySQL: lint, test, Dusk e2e, build, deploy to staging.',
            'Build a CSV import pipeline for tasks with validation, preview, error report, and idempotency.',
            'Upgrade Laravel to the latest version with minimal downtime; update composer deps and fix breaking changes.',
        ];

        $this->taskDescription = $examples[array_rand($examples)];
        // Auto-generate after filling in
        $this->generateSuggestions();
    }

    public function render()
    {
        return view('livewire.ai-suggestion');
    }
}