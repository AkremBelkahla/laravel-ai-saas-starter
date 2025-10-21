<?php

namespace App\Console\Commands;

use App\Domain\Billing\Enums\Plan;
use App\Domain\Billing\Services\CreditService;
use App\Models\Team;
use App\Models\Template;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class DemoCommand extends Command
{
    protected $signature = 'app:demo';

    protected $description = 'Seed demo data (users, teams, templates)';

    public function __construct(
        protected CreditService $creditService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Seeding demo data...');

        // Create demo users
        $users = [
            [
                'name' => 'Demo User (Free)',
                'email' => 'demo@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'plan' => Plan::FREE,
            ],
            [
                'name' => 'Pro User',
                'email' => 'pro@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'plan' => Plan::PRO,
            ],
            [
                'name' => 'Team Owner',
                'email' => 'team@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'plan' => Plan::TEAM,
            ],
        ];

        foreach ($users as $userData) {
            $plan = $userData['plan'];
            unset($userData['plan']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Create team
            $team = Team::firstOrCreate(
                ['owner_id' => $user->id],
                [
                    'name' => $user->name."'s Team",
                    'plan' => $plan,
                ]
            );

            // Add user to team
            if (! $team->hasUser($user)) {
                $team->addUser($user, 'owner');
            }

            // Allocate credits
            $this->creditService->resetMonthlyCredits($team);

            $this->info("Created user: {$user->email} with plan: {$plan->value}");
        }

        // Create public templates
        $templates = [
            [
                'name' => 'Blog Post',
                'description' => 'Generate a blog post on any topic',
                'type' => 'text',
                'prompt_template' => 'Write a comprehensive blog post about: {topic}. Include an introduction, main points, and conclusion.',
                'is_public' => true,
            ],
            [
                'name' => 'Product Description',
                'description' => 'Create compelling product descriptions',
                'type' => 'text',
                'prompt_template' => 'Write a compelling product description for: {product_name}. Highlight key features and benefits.',
                'is_public' => true,
            ],
            [
                'name' => 'Social Media Post',
                'description' => 'Generate engaging social media content',
                'type' => 'text',
                'prompt_template' => 'Create an engaging social media post about: {topic}. Keep it concise and include relevant hashtags.',
                'is_public' => true,
            ],
            [
                'name' => 'Logo Design',
                'description' => 'Generate logo concepts',
                'type' => 'image',
                'prompt_template' => 'A professional logo design for {company_name}, {style} style, clean and modern',
                'is_public' => true,
            ],
        ];

        foreach ($templates as $templateData) {
            Template::firstOrCreate(
                ['name' => $templateData['name']],
                $templateData
            );

            $this->info("Created template: {$templateData['name']}");
        }

        $this->newLine();
        $this->info('Demo data seeded successfully!');
        $this->info('Login credentials:');
        $this->table(
            ['Email', 'Password', 'Plan'],
            [
                ['demo@example.com', 'password', 'Free'],
                ['pro@example.com', 'password', 'Pro'],
                ['team@example.com', 'password', 'Team'],
            ]
        );

        return self::SUCCESS;
    }
}
