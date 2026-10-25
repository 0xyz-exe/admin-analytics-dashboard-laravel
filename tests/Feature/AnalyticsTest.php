<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_only_real_account_counts_and_can_export_csv(): void
    {
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->forceFill(['is_admin' => true])->save();
        User::factory()->create(['created_at' => now()->subDay(), 'email_verified_at' => now()]);
        User::factory()->create(['created_at' => now()->subDays(10), 'email_verified_at' => null]);
        $this->actingAs($admin)->get(route('dashboard', ['from' => now()->subDays(2)->toDateString(), 'to' => now()->toDateString()]))
            ->assertOk()->assertSee('3')->assertSee('2')->assertSee('100.0%');
        $this->actingAs($admin)->get(route('dashboard.export', ['from' => now()->subDays(2)->toDateString(), 'to' => now()->toDateString()]))
            ->assertOk()->assertHeader('content-type', 'text/csv; charset=utf-8');
    }

    public function test_non_admin_cannot_view_or_export_and_bad_ranges_fail_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('dashboard.export'))->assertForbidden();
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin)->get(route('dashboard', ['from' => '2026-05-02', 'to' => '2026-05-01']))->assertSessionHasErrors(['from', 'to']);
    }
}
