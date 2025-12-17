<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostAuthTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $stranger;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create(); // pemilik
        $this->stranger = User::factory()->create(); // bukan pemilik

        $this->post = Post::factory()->create([
            'user_id' => $this->owner->id,
            'title' => 'Original Title',
        ]);
    }

    /**
     * Skenario Update: Pemilik asli BOLEH update
     */
    public function test_owner_can_update_their_own_post()
    {
        $response = $this->actingAs($this->owner)
            ->putJson(route('posts.update', $this->post->id), [
                'title' => 'Updated by Owner',
                'content' => 'New Content',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', ['title' => 'Updated by Owner']);
    }

    /**
     * Skenario Update: Orang lain DILARANG update
     */
    public function test_other_users_cannot_update_post_they_do_not_own()
    {
        $response = $this->actingAs($this->stranger)
            ->putJson(route('posts.update', $this->post->id), [
                'title' => 'Hacked Title',
            ]);

        // Harus 403 Forbidden karena Policy menolak
        $response->assertStatus(403);
    }

    /**
     * Skenario Delete: Pemilik asli BOLEH hapus
     */
    public function test_owner_can_delete_their_own_post()
    {
        $response = $this->actingAs($this->owner)
            ->deleteJson(route('posts.destroy', $this->post->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('posts', ['id' => $this->post->id]);
    }

    /**
     * Skenario Delete: Orang lain DILARANG hapus
     */
    public function test_other_users_cannot_delete_post_they_do_not_own()
    {
        $response = $this->actingAs($this->stranger)
            ->deleteJson(route('posts.destroy', $this->post->id));

        $response->assertStatus(403);
        // Pastikan datanya masih ada di DB (tidak terhapus)
        $this->assertDatabaseHas('posts', ['id' => $this->post->id]);
    }
}
