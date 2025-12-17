<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat satu user untuk digunakan di semua skenario tes di bawah
        $this->user = User::factory()->create();
    }

    /**
     * Skenario 1: Memastikan postingan yang sudah "Published" muncul
     */
    public function test_it_displays_active_posts_correctly()
    {
        Post::factory()->create([
            'user_id' => $this->user->id,
            'is_draft' => false,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson(route('posts.index'));

        $response->assertStatus(200);
        // Harus ada 1 data yang muncul
        $response->assertJsonCount(1, 'data');
    }

    /**
     * Skenario 2: Memastikan postingan "Draft" TIDAK muncul
     */
    public function test_it_does_not_display_draft_posts()
    {
        Post::factory()->create([
            'user_id' => $this->user->id,
            'is_draft' => true,
        ]);

        $response = $this->getJson(route('posts.index'));

        $response->assertStatus(200);
        // Harus 0 data karena draft dilarang tampil
        $response->assertJsonCount(0, 'data');
    }

    /**
     * Skenario 3: Memastikan postingan "Scheduled" (Masa Depan) TIDAK muncul
     */
    public function test_it_does_not_display_scheduled_posts()
    {
        Post::factory()->create([
            'user_id' => $this->user->id,
            'is_draft' => false,
            'published_at' => now()->addDay(),
        ]);

        $response = $this->getJson(route('posts.index'));

        $response->assertStatus(200);
        // Harus 0 data karena belum waktunya tayang
        $response->assertJsonCount(0, 'data');
    }
}
