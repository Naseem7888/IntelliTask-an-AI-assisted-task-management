<div class="p-4 sm:p-8 text-white min-h-screen relative overflow-hidden task-scope"
    style="background-image: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)), url('{{ asset('Images/background.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <style>
        @keyframes pulseGlow {

            0%,
            100% {
                opacity: 0.25;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.05);
            }
        }

        .glow-overlay {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background:
                radial-gradient(900px 600px at 10% 15%, rgba(168, 85, 247, 0.18), rgba(168, 85, 247, 0) 60%),
                radial-gradient(700px 500px at 90% 20%, rgba(99, 102, 241, 0.14), rgba(99, 102, 241, 0) 60%),
                radial-gradient(800px 700px at 50% 100%, rgba(56, 189, 248, 0.12), rgba(56, 189, 248, 0) 60%);
            filter: blur(8px);
            animation: pulseGlow 8s ease-in-out infinite;
        }
    </style>
    <style>
        /* Force transparent inputs on dashboard */
        .task-scope input[type="text"],
        .task-scope input[type="search"],
        .task-scope input[type="email"],
        .task-scope input[type="password"],
        .task-scope textarea,
        .task-scope .form-input-glass {
            background-color: transparent !important;
            background: transparent !important;
            color: #e5e7eb !important;
            /* gray-200 */
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
        }

        .task-scope .form-input-glass:focus,
        .task-scope input:focus,
        .task-scope textarea:focus {
            outline: none !important;
            box-shadow: 0 0 0 4px rgba(var(--color-primary-500-rgb) / 0.25), 0 0 20px rgba(var(--color-primary-500-rgb) / 0.2) !important;
            border-color: rgba(var(--color-primary-500-rgb) / 0.6) !important;
        }

        .task-scope ::placeholder {
            color: rgba(203, 213, 225, 0.65) !important;
        }
    </style>
    <div class="glow-overlay"></div>
    <div class="max-w-7xl mx-auto relative z-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-4xl font-extrabold gradient-text">My Tasks</h1>
                <p class="text-gray-400 mt-1">Manage and complete your tasks efficiently</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button wire:click="$toggle('showAISuggestions')" class="btn btn-gradient btn-ripple">
                    {{ $showAISuggestions ? 'Hide AI Suggestions' : '✨ AI Suggestions' }}
                </button>
                <button wire:click="$toggle('showCreateForm')" class="btn btn-glass">
                    {{ $showCreateForm ? 'Cancel' : 'Add Task Manually' }}
                </button>
            </div>
        </div>

        <!-- AI Suggestion Panel -->
        @if($showAISuggestions)
            <div wire:transition class="card-glass card-elevated shadow-glow-accent fade-in-up mb-6">
                @livewire('ai-suggestion')
            </div>
        @endif

        <!-- Manual Task Creation Form -->
        @if($showCreateForm)
            <div wire:transition class="card-glass p-6 my-4 shadow-lg">
                <form wire:submit.prevent="createTask">
                    <div class="mb-4">
                        <x-input-label for="new-title" :value="'Title'" required />
                        <x-text-input id="new-title" glass class="mt-1 block w-full" type="text"
                            wire:model.defer="newTask.title" />
                        <x-input-error :messages="$errors->get('newTask.title')" class="mt-1" />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="new-description" :value="'Description (Optional)'" />
                        <textarea id="new-description" wire:model.defer="newTask.description" rows="3"
                            class="mt-1 block w-full form-input-glass js-autoresize-textarea"></textarea>
                        <x-input-error :messages="$errors->get('newTask.description')" class="mt-1" />
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" wire:click="$set('showCreateForm', false)"
                            class="btn btn-outline">Cancel</button>
                        <button type="submit" class="btn btn-gradient btn-ripple">Save Task</button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Filters -->
        <div class="mb-6">
            <div class="btn-group w-full sm:w-auto">
                <button wire:click="$set('filter', 'all')"
                    class="btn {{ $filter === 'all' ? 'btn-gradient' : 'btn-glass' }}">
                    All <span class="badge badge-primary ml-2">{{ $countAll }}</span>
                </button>
                <button wire:click="$set('filter', 'pending')"
                    class="btn {{ $filter === 'pending' ? 'btn-gradient' : 'btn-glass' }}">
                    Pending <span class="badge ml-2">{{ $countPending }}</span>
                </button>
                <button wire:click="$set('filter', 'completed')"
                    class="btn {{ $filter === 'completed' ? 'btn-gradient' : 'btn-glass' }}">
                    Completed <span class="badge ml-2">{{ $countCompleted }}</span>
                </button>
            </div>
        </div>

        <!-- Task List -->
        <div class="space-y-4">
            @forelse($tasks as $task)
                <div wire:key="{{ $task->id }}"
                    class="card card-glass card-hover p-6 shadow-md flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    @if($editingTaskId === $task->id)
                        <div class="flex-grow">
                            <x-input-label for="editing-title-{{ $task->id }}" :value="'Title'" required />
                            <x-text-input id="editing-title-{{ $task->id }}" glass class="w-full" type="text"
                                wire:model.defer="editingTask.title" />
                            <x-input-error :messages="$errors->get('editingTask.title')" class="mt-1" />
                            <x-input-label for="editing-description-{{ $task->id }}" :value="'Description'" class="mt-3" />
                            <textarea id="editing-description-{{ $task->id }}" wire:model.defer="editingTask.description"
                                rows="2" class="mt-1 w-full form-input-glass js-autoresize-textarea"></textarea>
                            <x-input-error :messages="$errors->get('editingTask.description')" class="mt-1" />
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button wire:click="updateTask({{ $task->id }})" class="btn btn-gradient btn-sm">Save</button>
                            <button wire:click="cancelEditing" class="btn btn-outline btn-sm">Cancel</button>
                        </div>
                    @else
                        <div class="flex items-center">
                            <input type="checkbox" wire:click="toggleTaskStatus({{ $task->id }})" {{ $task->isCompleted() ? 'checked' : '' }}
                                class="h-6 w-6 text-accent-500 bg-gray-800 border-gray-600 rounded transition">
                            <div class="ml-4">
                                <h3
                                    class="text-xl font-semibold {{ $task->isCompleted() ? 'line-through text-gray-500' : '' }}">
                                    {{ $task->title }}
                                </h3>
                                @if($task->description)
                                    <p
                                        class="text-sm text-gray-400 leading-relaxed {{ $task->isCompleted() ? 'line-through' : '' }}">
                                        {{ \Illuminate\Support\Str::limit($task->description, 100) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="editTask({{ $task->id }})"
                                class="btn-icon text-accent-400 hover:text-accent-300 tooltip" aria-label="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                    <path fill-rule="evenodd"
                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button wire:click="deleteTask({{ $task->id }})"
                                onclick="confirm('Are you sure you want to delete this task?') || event.stopImmediatePropagation()"
                                class="btn-icon text-error-400 hover:text-error-300 tooltip" aria-label="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="card-glass p-8 text-center">
                    <p class="text-gray-400">
                        @if($filter === 'pending')
                            You have no pending tasks. Great job!
                        @elseif($filter === 'completed')
                            You haven't completed any tasks yet.
                        @else
                            You don't have any tasks yet.
                        @endif
                    </p>
                    <p class="mt-2 text-gray-500">
                        Try the <button wire:click="$set('showAISuggestions', true)"
                            class="text-accent-400 hover:underline">AI Suggestions</button> or add one manually.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $tasks->links() }}
        </div>
    </div>
</div>