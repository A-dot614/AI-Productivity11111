<?php

namespace Tests\Feature;

use App\Models\ProductivityLog;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductivityLogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_does_not_create_duplicate_productivity_logs_for_same_day(): void
    {
        $this->seed(DatabaseSeeder::class);

        $userId = User::query()->where('email', 'user@example.com')->value('id');

        $this->assertNotNull($userId);
        $this->assertSame(
            ProductivityLog::query()->where('user_id', $userId)->count(),
            ProductivityLog::query()->where('user_id', $userId)->selectRaw('count(distinct log_date) as distinct_dates')->value('distinct_dates'),
        );
    }
}
