<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModerationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_safe_word_is_allowed(): void
    {
        $response = $this->getJson('/api/moderate/test?text=Hello+world');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'flagged' => false,
            'is_safe' => true,
        ]);
    }

    public function test_inappropriate_word_is_flagged(): void
    {
        $response = $this->getJson('/api/moderate/test?text=bodoh');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'flagged' => true,
            'is_safe' => false,
        ]);
        
        $response->assertJsonStructure([
            'violations' => [
                '*' => [
                    'classifier',
                    'flagged',
                    'reason',
                ]
            ]
        ]);
    }

    public function test_announcement_store_moderation_blocks_inappropriate_content(): void
    {
        $user = \App\Models\User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($user)
            ->post('/announcements', [
                'title' => 'Test Announcement bodoh',
                'content' => 'This is a clean content',
                'category' => 'general',
                'priority' => 'normal',
                'announcement_type' => 'unofficial',
                'status' => 'published',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('moderation');
        
        $errors = session('errors');
        $moderationError = $errors->first('moderation');
        $this->assertStringContainsString('Your announcement was blocked by our content moderation system', $moderationError);
        $this->assertStringContainsString("contains inappropriate language: 'bodoh'", $moderationError);
    }

    public function test_announcement_update_moderation_blocks_inappropriate_content(): void
    {
        $user = \App\Models\User::factory()->create(['role' => 'student']);
        $announcement = \App\Models\Announcement::create([
            'title' => 'Clean Title',
            'content' => 'Clean Content',
            'category' => 'general',
            'priority' => 'normal',
            'author_id' => $user->id,
            'is_official' => false,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)
            ->put("/announcements/{$announcement->id}", [
                'title' => 'Clean Title',
                'content' => 'Inappropriate content gila',
                'category' => 'general',
                'priority' => 'normal',
                'announcement_type' => 'unofficial',
                'status' => 'published',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('moderation');
        
        $errors = session('errors');
        $moderationError = $errors->first('moderation');
        $this->assertStringContainsString('Your updated announcement was blocked by our content moderation system', $moderationError);
        $this->assertStringContainsString("contains inappropriate language: 'gila'", $moderationError);
    }
}
