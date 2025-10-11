<div x-data="{ editing:false, copied:false }" class="card-glass card-elevated shadow-glow-accent p-6 my-6 fade-in-up">
    <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-2 text-accent-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </svg>
        <span>AI Task Breakdown</span>
        <span class="ml-3 badge badge-gradient">AI-Powered</span>
    </h3>

    <div class="mb-4">
        <label for="taskDescription" class="input-label">Describe a complex task to break down:</label>
        <textarea wire:model.defer="taskDescription" id="taskDescription" rows="3"
                  class="w-full form-input-glass js-autoresize-textarea focus:shadow-glow-accent"
                  maxlength="500"
                  placeholder="e.g., 'Plan a team-building event for the entire company'"></textarea>
        <div class="flex justify-between text-xs text-gray-400 mt-1">
            <span>Press Ctrl+Enter to generate</span>
            <span data-counter>0 / 500</span>
        </div>
        @error('taskDescription') <span class="input-error">{{ $message }}</span> @enderror
    </div>

    <div class="flex items-center justify-between">
        <button wire:click="generateSuggestions" wire:loading.attr="disabled"
                class="btn btn-gradient btn-lg btn-ripple inline-flex items-center">
            <svg wire:loading wire:target="generateSuggestions" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="generateSuggestions">Generate Suggestions</span>
            <span wire:loading wire:target="generateSuggestions">AI is thinking...</span>
        </button>
        <button type="button" wire:click="surpriseMe" class="btn btn-outline ml-3">Surprise me</button>
        @if($showSuggestions)
            <button wire:click="clearSuggestions" class="text-gray-400 hover:text-white transition">Cancel</button>
        @endif
    </div>

    @if ($isLoading)
        <div class="mt-4 text-center text-gray-400">
            <div class="ai-thinking-indicator">
                <span>Analyzing your task…</span>
                <span class="loading-dots"><span></span><span></span><span></span></span>
            </div>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="skeleton h-16"></div>
                <div class="skeleton h-16"></div>
                <div class="skeleton h-16"></div>
            </div>
        </div>
    @endif

    @if ($error)
        <div class="mt-4 alert alert-error animate-shake" role="alert">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline">{{ $error }}</span>
            <button wire:click="generateSuggestions" class="btn btn-outline btn-sm ml-3">Retry</button>
        </div>
    @endif

    @if ($showSuggestions && !$isLoading && !empty($suggestions))
        <div class="mt-6" data-suggestions-root>
            <h4 class="text-lg font-medium text-white mb-3">Suggested Sub-tasks <span class="badge badge-primary">{{ count($suggestions) }}</span></h4>
            <div class="space-y-3 card-glass p-4 rounded-md">
                @foreach ($suggestions as $index => $suggestion)
                    <div class="card card-hover p-3 flex items-center" data-suggestion-item data-suggestion-text="{{ $suggestion }}">
                        <input type="checkbox" id="suggestion-{{ $index }}"
                               wire:click="selectSuggestion({{ $index }})"
                               {{ in_array($suggestion, $selectedSuggestions, true) ? 'checked' : '' }}
                               class="h-5 w-5 text-accent-500 bg-gray-800 border-gray-600 rounded transition">
                        <label x-show="!editing" for="suggestion-{{ $index }}" class="ml-3 block text-sm font-medium text-gray-300">
                            {{ $suggestion }}
                        </label>
                        <input x-show="editing" type="text" class="ml-3 w-full form-input-glass"
                               oninput="this.closest('[data-suggestion-item]').setAttribute('data-suggestion-text', this.value)"
                               wire:model.defer="suggestions.{{ $index }}" />
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex items-center justify-between">
                <button wire:click="createTasksFromSuggestions" wire:loading.attr="disabled"
                        class="btn btn-gradient btn-lg btn-ripple inline-flex items-center">
                    <span class="mr-2">Create Selected Tasks</span>
                    <span class="badge badge-primary">{{ count($selectedSuggestions) }}</span>
                </button>
                <div class="flex items-center gap-3">
                    <div class="btn-group">
                        <button wire:click="generateSuggestions" wire:loading.attr="disabled" class="btn btn-outline btn-sm">Regenerate</button>
                        <button x-on:click="editing = !editing" type="button" class="btn btn-outline btn-sm" x-text="editing ? 'Done Editing' : 'Edit'" aria-pressed="false"></button>
                        <button type="button" class="btn btn-outline btn-sm"
                                x-on:click="(() => { const items=[...$el.closest('[data-suggestions-root]').querySelectorAll('[data-suggestion-item]')].map(el=>el.getAttribute('data-suggestion-text')?.trim()||'').filter(Boolean); navigator.clipboard.writeText(items.join('\n')).then(()=>{ copied=true; setTimeout(()=>copied=false,1500); }); })()">
                            <span x-show="!copied">Export</span>
                            <span x-show="copied">Copied!</span>
                        </button>
                    </div>
                    <div class="text-sm">
                        <button wire:click="selectAllSuggestions" class="text-accent-400 hover:underline">Select All</button>
                        <span class="text-gray-600 mx-1">|</span>
                        <button wire:click="$set('selectedSuggestions', [])" class="text-accent-400 hover:underline">Deselect All</button>
                    </div>
                </div>
            </div>
        </div>
    @elseif($showSuggestions && !$isLoading && empty($suggestions))
        <div class="mt-4 text-center text-gray-500">
            The AI couldn't generate suggestions for this task. Please try rephrasing it.
        </div>
    @endif
</div>