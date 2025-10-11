<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all users to assign tasks to
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->error('No users found! Please run UserSeeder first.');
            return;
        }

        // Create diverse task examples
        $this->createSimpleTasks($users);
        $this->createComplexTasks($users);
        $this->createAIReadyTasks($users);
        $this->createCompletedTasks($users);

        $totalTasks = Task::count();
        $pendingTasks = Task::where('status', Task::STATUS_PENDING)->count();
        $completedTasks = Task::where('status', Task::STATUS_COMPLETED)->count();

        $this->command->info("Created {$totalTasks} tasks successfully!");
        $this->command->info("- Pending tasks: {$pendingTasks}");
        $this->command->info("- Completed tasks: {$completedTasks}");
        
        foreach ($users as $user) {
            $userTaskCount = $user->tasks()->count();
            $this->command->info("- {$user->name}: {$userTaskCount} tasks");
        }
    }

    /**
     * Create simple, everyday tasks
     *
     * @param \Illuminate\Database\Eloquent\Collection $users
     * @return void
     */
    private function createSimpleTasks($users)
    {
        $simpleTasks = [
            [
                'title' => 'Buy groceries',
                'description' => 'Pick up milk, bread, eggs, and fresh vegetables from the local supermarket.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Call dentist',
                'description' => 'Schedule annual dental checkup appointment for next month.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Update resume',
                'description' => 'Add recent project experience and update skills section.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Pay electricity bill',
                'description' => 'Monthly electricity bill payment due by the 15th.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Water plants',
                'description' => 'Water all indoor plants and check soil moisture levels.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Backup computer files',
                'description' => 'Create backup of important documents and photos to external drive.',
                'status' => Task::STATUS_PENDING,
            ],
        ];

        $this->createTasksForUsers($simpleTasks, $users, 0);
    }

    /**
     * Create complex, multi-step tasks
     *
     * @param \Illuminate\Database\Eloquent\Collection $users
     * @return void
     */
    private function createComplexTasks($users)
    {
        $complexTasks = [
            [
                'title' => 'Plan summer vacation',
                'description' => 'Research destinations, compare flight prices, book accommodations, create itinerary, arrange pet care, and notify work about time off. Budget: $3000. Preferred dates: July 15-30.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Organize home office renovation',
                'description' => 'Measure room dimensions, research furniture options, get contractor quotes for electrical work, choose paint colors, order supplies, schedule installation, and set up new workspace layout.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Launch personal blog',
                'description' => 'Choose blogging platform, design website layout, write first 5 blog posts, set up social media accounts, create content calendar, implement SEO strategies, and promote to target audience.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Prepare for job interview',
                'description' => 'Research company background, practice common interview questions, prepare portfolio of work samples, choose appropriate outfit, plan route to office, and prepare thoughtful questions to ask interviewer.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Organize family reunion',
                'description' => 'Create guest list, send invitations, book venue, plan menu and catering, arrange accommodations for out-of-town guests, organize activities and games, and coordinate with family members for contributions.',
                'status' => Task::STATUS_PENDING,
            ],
        ];

        $this->createTasksForUsers($complexTasks, $users, 10);
    }

    /**
     * Create AI-ready tasks suitable for breakdown testing
     *
     * @param \Illuminate\Database\Eloquent\Collection $users
     * @return void
     */
    private function createAIReadyTasks($users)
    {
        $aiReadyTasks = [
            [
                'title' => 'Build a mobile app for expense tracking',
                'description' => 'Develop a cross-platform mobile application that allows users to track daily expenses, categorize spending, set budgets, generate reports, sync data across devices, and provide insights on spending patterns. Include features for receipt scanning, recurring expense tracking, and export functionality.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Learn machine learning and implement a project',
                'description' => 'Complete comprehensive machine learning course, understand algorithms like linear regression, decision trees, and neural networks. Build a practical project using Python and TensorFlow to predict house prices based on historical data. Document the entire learning process and create a presentation.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Start an e-commerce business',
                'description' => 'Research market opportunities, identify target audience, source products, create business plan, register business entity, build e-commerce website, set up payment processing, establish inventory management, create marketing strategy, and launch with initial product line.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Write and publish a technical book',
                'description' => 'Choose specialized technical topic, conduct thorough research, create detailed outline, write 200+ pages of content, include code examples and diagrams, find technical reviewers, work with editor, design cover, format for print and digital, and establish marketing plan for launch.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Develop a comprehensive fitness program',
                'description' => 'Assess current fitness level, consult with nutritionist and trainer, create 6-month workout plan, design meal prep strategy, track progress with metrics, research supplements, establish accountability system, and document transformation journey for motivation.',
                'status' => Task::STATUS_PENDING,
            ],
            [
                'title' => 'Create a documentary film',
                'description' => 'Research compelling story topic, write treatment and script, secure funding and permits, assemble production crew, conduct interviews, capture b-roll footage, edit in post-production, add music and graphics, submit to film festivals, and plan distribution strategy.',
                'status' => Task::STATUS_PENDING,
            ],
        ];

        $this->createTasksForUsers($aiReadyTasks, $users, 20);
    }

    /**
     * Create completed tasks to show task history
     *
     * @param \Illuminate\Database\Eloquent\Collection $users
     * @return void
     */
    private function createCompletedTasks($users)
    {
        $completedTasks = [
            [
                'title' => 'Set up home gym',
                'description' => 'Purchased equipment, arranged space, and created workout schedule.',
                'status' => Task::STATUS_COMPLETED,
            ],
            [
                'title' => 'Learn Spanish basics',
                'description' => 'Completed beginner Spanish course on language learning app.',
                'status' => Task::STATUS_COMPLETED,
            ],
            [
                'title' => 'Organize digital photos',
                'description' => 'Sorted and backed up 5 years worth of digital photos into organized folders.',
                'status' => Task::STATUS_COMPLETED,
            ],
            [
                'title' => 'Complete tax filing',
                'description' => 'Gathered all documents and filed annual tax return online.',
                'status' => Task::STATUS_COMPLETED,
            ],
            [
                'title' => 'Read 12 books this year',
                'description' => 'Successfully read 12 books covering various genres including fiction, business, and self-improvement.',
                'status' => Task::STATUS_COMPLETED,
            ],
            [
                'title' => 'Declutter bedroom closet',
                'description' => 'Sorted through clothes, donated items, and reorganized closet space.',
                'status' => Task::STATUS_COMPLETED,
            ],
            [
                'title' => 'Learn basic cooking skills',
                'description' => 'Mastered 10 essential recipes and improved kitchen confidence.',
                'status' => Task::STATUS_COMPLETED,
            ],
            [
                'title' => 'Build raised garden bed',
                'description' => 'Constructed wooden raised bed, filled with soil, and planted vegetables.',
                'status' => Task::STATUS_COMPLETED,
            ],
        ];

        $this->createTasksForUsers($completedTasks, $users, 30, true);
    }

    /**
     * Create tasks and assign them to users with proper priority ordering
     *
     * @param array $tasks
     * @param \Illuminate\Database\Eloquent\Collection $users
     * @param int $basePriority
     * @param bool $isCompleted
     * @return void
     */
    private function createTasksForUsers($tasks, $users, $basePriority = 0, $isCompleted = false)
    {
        $userIndex = 0;
        $priorityOffset = 0;

        foreach ($tasks as $taskData) {
            // Cycle through users
            $user = $users[$userIndex % $users->count()];
            
            // Set priority order (only matters for pending tasks)
            $priorityOrder = $taskData['status'] === Task::STATUS_PENDING 
                ? $basePriority + $priorityOffset 
                : 0;

            // Create the task
            $task = Task::create([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'status' => $taskData['status'],
                'priority_order' => $priorityOrder,
                'user_id' => $user->id,
                'created_at' => $isCompleted 
                    ? Carbon::now()->subDays(rand(1, 30)) 
                    : Carbon::now()->subHours(rand(1, 72)),
                'updated_at' => $isCompleted 
                    ? Carbon::now()->subDays(rand(1, 15)) 
                    : Carbon::now()->subHours(rand(1, 24)),
            ]);

            $userIndex++;
            
            // Only increment priority for pending tasks
            if ($taskData['status'] === Task::STATUS_PENDING) {
                $priorityOffset++;
            }
        }
    }

    /**
     * Create additional random tasks for stress testing
     *
     * @param int $count
     * @return void
     */
    public function createRandomTasks($count = 20)
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            return;
        }

        $taskTemplates = [
            'Review %s documentation',
            'Update %s configuration',
            'Test %s functionality',
            'Optimize %s performance',
            'Debug %s issues',
            'Implement %s feature',
            'Research %s alternatives',
            'Backup %s data',
            'Monitor %s metrics',
            'Upgrade %s version',
        ];

        $subjects = [
            'database', 'API', 'frontend', 'backend', 'security', 'authentication',
            'payment system', 'user interface', 'mobile app', 'web service',
            'cache layer', 'logging system', 'monitoring tools', 'deployment pipeline'
        ];

        for ($i = 0; $i < $count; $i++) {
            $template = $taskTemplates[array_rand($taskTemplates)];
            $subject = $subjects[array_rand($subjects)];
            $user = $users->random();
            $status = rand(0, 1) ? Task::STATUS_PENDING : Task::STATUS_COMPLETED;
            
            Task::create([
                'title' => sprintf($template, $subject),
                'description' => "Detailed work required for {$subject} component. This task involves analysis, implementation, and testing phases.",
                'status' => $status,
                'priority_order' => $status === Task::STATUS_COMPLETED ? 0 : Task::getNextPriorityOrder($user->id),
                'user_id' => $user->id,
                'created_at' => Carbon::now()->subHours(rand(1, 168)), // Within last week
                'updated_at' => Carbon::now()->subHours(rand(1, 24)),  // Within last day
            ]);
        }

        $this->command->info("Created {$count} additional random tasks for stress testing.");
    }

    /**
     * Get sample task data for testing purposes
     *
     * @return array
     */
    public static function getSampleTaskData()
    {
        return [
            'simple' => [
                'title' => 'Sample Simple Task',
                'description' => 'This is a simple task for testing purposes.',
                'status' => Task::STATUS_PENDING,
            ],
            'complex' => [
                'title' => 'Sample Complex Task',
                'description' => 'This is a complex task that involves multiple steps, coordination with team members, research phase, implementation phase, testing phase, and final review before completion.',
                'status' => Task::STATUS_PENDING,
            ],
            'completed' => [
                'title' => 'Sample Completed Task',
                'description' => 'This task has been successfully completed.',
                'status' => Task::STATUS_COMPLETED,
            ],
        ];
    }

    /**
     * Create tasks for a specific user (helper method for testing)
     *
     * @param int $userId
     * @param int $count
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function createTasksForUser($userId, $count = 5, $status = Task::STATUS_PENDING)
    {
        $tasks = collect();
        
        for ($i = 1; $i <= $count; $i++) {
            $task = Task::create([
                'title' => "Test Task {$i}",
                'description' => "Description for test task {$i}",
                'status' => $status,
                'priority_order' => Task::getNextPriorityOrder($userId),
                'user_id' => $userId,
            ]);
            
            $tasks->push($task);
        }

        return $tasks;
    }
}