<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->generateTaskTitle(),
            'description' => $this->generateTaskDescription(),
            'status' => fake()->randomElement([Task::STATUS_PENDING, Task::STATUS_COMPLETED]),
            'priority_order' => fake()->numberBetween(0, 100),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the task should be pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Task::STATUS_PENDING,
        ]);
    }

    /**
     * Indicate that the task should be completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Task::STATUS_COMPLETED,
        ]);
    }

    /**
     * Create a task with high priority (low priority_order number).
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_order' => fake()->numberBetween(0, 10),
        ]);
    }

    /**
     * Create a task with medium priority.
     */
    public function mediumPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_order' => fake()->numberBetween(11, 50),
        ]);
    }

    /**
     * Create a task with low priority (high priority_order number).
     */
    public function lowPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_order' => fake()->numberBetween(51, 100),
        ]);
    }

    /**
     * Create a task for a specific user.
     */
    public function forUser(User|int $user): static
    {
        $userId = $user instanceof User ? $user->id : $user;
        
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    /**
     * Create a task with a specific priority order.
     */
    public function withPriority(int $priority): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_order' => $priority,
        ]);
    }

    /**
     * Create a simple task with minimal content.
     */
    public function simple(): static
    {
        $simpleTasks = [
            'Buy groceries',
            'Call mom',
            'Pay bills',
            'Walk the dog',
            'Check email',
            'Water plants',
            'Take vitamins',
            'Read news',
            'Backup files',
            'Clean desk'
        ];

        return $this->state(fn (array $attributes) => [
            'title' => fake()->randomElement($simpleTasks),
            'description' => fake()->optional(0.3)->sentence(),
        ]);
    }

    /**
     * Create a complex task with detailed content.
     */
    public function complex(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $this->generateComplexTaskTitle(),
            'description' => $this->generateComplexTaskDescription(),
        ]);
    }

    /**
     * Create a work-related task.
     */
    public function work(): static
    {
        $workTasks = [
            'Prepare quarterly report',
            'Review team performance',
            'Update project documentation',
            'Schedule client meeting',
            'Analyze market trends',
            'Optimize database queries',
            'Conduct code review',
            'Plan sprint activities',
            'Update security protocols',
            'Train new employees'
        ];

        $workDescriptions = [
            'This task requires coordination with multiple departments and stakeholders.',
            'Priority task that needs to be completed before the deadline.',
            'Regular maintenance task to ensure system efficiency.',
            'Strategic planning task for upcoming quarter.',
            'Quality assurance task to maintain standards.',
        ];

        return $this->state(fn (array $attributes) => [
            'title' => fake()->randomElement($workTasks),
            'description' => fake()->randomElement($workDescriptions),
        ]);
    }

    /**
     * Create a personal task.
     */
    public function personal(): static
    {
        $personalTasks = [
            'Plan weekend trip',
            'Organize photo albums',
            'Learn new recipe',
            'Exercise routine',
            'Read book chapter',
            'Call old friend',
            'Organize closet',
            'Plan birthday party',
            'Update resume',
            'Practice guitar'
        ];

        $personalDescriptions = [
            'Personal development activity for self-improvement.',
            'Leisure activity to maintain work-life balance.',
            'Health and wellness related task.',
            'Social activity to maintain relationships.',
            'Creative pursuit for personal satisfaction.',
        ];

        return $this->state(fn (array $attributes) => [
            'title' => fake()->randomElement($personalTasks),
            'description' => fake()->optional(0.7)->randomElement($personalDescriptions),
        ]);
    }

    /**
     * Create an urgent task.
     */
    public function urgent(): static
    {
        $urgentTasks = [
            'Fix critical bug in production',
            'Respond to client emergency',
            'Submit tax documents',
            'Renew expired license',
            'Handle security incident',
            'Address customer complaint',
            'Fix broken deployment',
            'Update expired certificates',
            'Resolve payment issue',
            'Handle system outage'
        ];

        return $this->state(fn (array $attributes) => [
            'title' => fake()->randomElement($urgentTasks),
            'description' => 'URGENT: This task requires immediate attention and should be prioritized.',
            'priority_order' => fake()->numberBetween(0, 5),
            'status' => Task::STATUS_PENDING,
        ]);
    }

    /**
     * Create a task suitable for AI breakdown testing.
     */
    public function aiReady(): static
    {
        $aiTasks = [
            'Develop comprehensive marketing strategy for Q2',
            'Implement user authentication system with OAuth',
            'Plan and execute company retreat event',
            'Create automated testing suite for web application',
            'Design and build customer onboarding workflow',
            'Conduct market research for new product launch',
            'Optimize website performance and SEO',
            'Develop mobile app with cross-platform compatibility',
            'Create comprehensive employee training program',
            'Implement data analytics dashboard'
        ];

        $aiDescriptions = [
            'This is a complex project that involves multiple phases and can be broken down into smaller, manageable subtasks. It requires careful planning, resource allocation, and timeline management.',
            'Multi-faceted initiative requiring research, planning, implementation, and testing phases. Each phase has specific deliverables and dependencies.',
            'Comprehensive project involving stakeholder coordination, technical implementation, and quality assurance processes.',
            'Strategic initiative requiring analysis, design, development, and deployment phases with clear milestones.',
            'Large-scale project that benefits from systematic breakdown into actionable steps and subtasks.',
        ];

        return $this->state(fn (array $attributes) => [
            'title' => fake()->randomElement($aiTasks),
            'description' => fake()->randomElement($aiDescriptions),
            'status' => Task::STATUS_PENDING,
        ]);
    }

    /**
     * Create a task with no description.
     */
    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }

    /**
     * Create a task with a long title for UI testing.
     */
    public function longTitle(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => fake()->sentence(12) . ' ' . fake()->sentence(8),
        ]);
    }

    /**
     * Create a task with a long description for UI testing.
     */
    public function longDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => fake()->paragraphs(3, true),
        ]);
    }

    /**
     * Create a sequence of tasks with ordered priorities.
     */
    public function sequence(int $startPriority = 0): static
    {
        static $sequenceCounter = 0;
        $priority = $startPriority + $sequenceCounter++;
        
        return $this->state(fn (array $attributes) => [
            'priority_order' => $priority,
        ]);
    }

    /**
     * Create a task with custom data callback support.
     */
    public function withCustomData(callable $callback): static
    {
        return $this->state($callback);
    }

    /**
     * Generate a realistic task title.
     */
    private function generateTaskTitle(): string
    {
        $actions = [
            'Complete', 'Review', 'Update', 'Create', 'Implement', 'Design', 'Plan', 'Organize',
            'Prepare', 'Analyze', 'Optimize', 'Test', 'Deploy', 'Configure', 'Research', 'Document'
        ];

        $objects = [
            'project proposal', 'user interface', 'database schema', 'marketing campaign',
            'security audit', 'performance report', 'client presentation', 'team meeting',
            'code documentation', 'system backup', 'user feedback', 'product roadmap',
            'budget analysis', 'training materials', 'quality standards', 'workflow process'
        ];

        $action = fake()->randomElement($actions);
        $object = fake()->randomElement($objects);

        return $action . ' ' . $object;
    }

    /**
     * Generate a realistic task description.
     */
    private function generateTaskDescription(): ?string
    {
        $templates = [
            'This task involves {action} to ensure {outcome}. Expected completion time: {time}.',
            'Priority task requiring {action} with focus on {focus}. Deadline: {deadline}.',
            '{action} is needed to improve {area}. This will help achieve {goal}.',
            'Regular maintenance task to {action} and maintain {standard}.',
            'Strategic initiative to {action} and enhance {benefit}.',
        ];

        $actions = [
            'careful planning and execution', 'thorough analysis and review', 'collaborative effort',
            'detailed research and documentation', 'systematic approach', 'creative problem-solving'
        ];

        $outcomes = [
            'optimal results', 'improved efficiency', 'better user experience', 'enhanced security',
            'increased productivity', 'cost reduction', 'quality improvement', 'customer satisfaction'
        ];

        $focuses = [
            'quality assurance', 'user experience', 'performance optimization', 'security compliance',
            'scalability', 'maintainability', 'accessibility', 'cross-platform compatibility'
        ];

        $areas = [
            'system performance', 'user engagement', 'data accuracy', 'process efficiency',
            'team collaboration', 'customer service', 'product quality', 'operational workflow'
        ];

        $goals = [
            'business objectives', 'user satisfaction', 'operational excellence', 'competitive advantage',
            'market leadership', 'innovation goals', 'sustainability targets', 'growth metrics'
        ];

        $standards = [
            'high quality standards', 'security protocols', 'performance benchmarks', 'compliance requirements',
            'best practices', 'industry standards', 'operational procedures', 'quality metrics'
        ];

        $benefits = [
            'user experience', 'system reliability', 'operational efficiency', 'team productivity',
            'customer satisfaction', 'business value', 'competitive position', 'market presence'
        ];

        $times = ['2-3 hours', '1-2 days', '3-5 days', '1 week', '2 weeks'];
        $deadlines = ['end of week', 'next Monday', 'month end', 'next sprint', 'Q2 deadline'];

        // 30% chance of no description
        if (fake()->boolean(30)) {
            return null;
        }

        $template = fake()->randomElement($templates);
        
        return str_replace([
            '{action}', '{outcome}', '{time}', '{deadline}', '{focus}', 
            '{area}', '{goal}', '{standard}', '{benefit}'
        ], [
            fake()->randomElement($actions),
            fake()->randomElement($outcomes),
            fake()->randomElement($times),
            fake()->randomElement($deadlines),
            fake()->randomElement($focuses),
            fake()->randomElement($areas),
            fake()->randomElement($goals),
            fake()->randomElement($standards),
            fake()->randomElement($benefits)
        ], $template);
    }

    /**
     * Generate a complex task title.
     */
    private function generateComplexTaskTitle(): string
    {
        $complexTasks = [
            'Develop comprehensive digital transformation strategy',
            'Implement enterprise-wide security framework',
            'Design and deploy microservices architecture',
            'Create multi-channel customer engagement platform',
            'Build automated CI/CD pipeline with monitoring',
            'Establish data governance and analytics framework',
            'Develop cross-platform mobile application suite',
            'Implement advanced machine learning algorithms',
            'Create comprehensive disaster recovery plan',
            'Design scalable cloud infrastructure solution'
        ];

        return fake()->randomElement($complexTasks);
    }

    /**
     * Generate a complex task description.
     */
    private function generateComplexTaskDescription(): string
    {
        $complexDescriptions = [
            'This comprehensive project requires extensive planning, stakeholder coordination, and phased implementation. The initiative involves multiple teams, technologies, and deliverables with interdependent timelines. Success metrics include performance improvements, cost optimization, and enhanced user satisfaction.',
            
            'Multi-phase strategic initiative requiring thorough analysis, design, development, and deployment phases. Each phase has specific milestones, quality gates, and success criteria. The project involves cross-functional collaboration and requires careful resource management.',
            
            'Large-scale transformation project involving process redesign, technology implementation, and organizational change management. The initiative requires stakeholder buy-in, training programs, and continuous monitoring to ensure successful adoption and ROI achievement.',
            
            'Complex technical implementation requiring architecture design, security considerations, performance optimization, and scalability planning. The project involves multiple integration points, data migration, and comprehensive testing phases.',
            
            'Strategic business initiative requiring market research, competitive analysis, solution design, and implementation planning. Success depends on user adoption, performance metrics, and business value realization through measurable outcomes.'
        ];

        return fake()->randomElement($complexDescriptions);
    }
}