<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostShowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Skenario: Menampilkan post yang sudah aktif (Berhasil)
     */
    public function test_it_can_display_active_post_details()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'is_draft' => false,
            'published_at' => now()->subHour(),
        ]);

        $response = $this->getJson(route('posts.show', $post->id));

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $post->id);
    }

    /**
     * Skenario: Post Draft harus return 404 (Poin 4-5)
     */
    public function test_it_returns_404_for_draft_post()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'is_draft' => true,
        ]);

        $response = $this->getJson(route('posts.show', $post->id));

        // Meskipun ID-nya ada di database, tapi karena Draft, user tidak boleh lihat
        $response->assertStatus(404);
    }

    /**
     * Skenario: Post Scheduled (Masa Depan) harus return 404 (Poin 4-5)
     */
    public function test_it_returns_404_for_scheduled_post()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'is_draft' => false,
            'published_at' => now()->addDay(), // Besok
        ]);

        $response = $this->getJson(route('posts.show', $post->id));

        $response->assertStatus(404);
    }
}
