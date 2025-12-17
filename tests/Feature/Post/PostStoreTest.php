<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Skenario: User yang login berhasil menyimpan post
     */
    public function test_authenticated_user_can_create_post()
    {

        $user = User::factory()->create();

        $postData = [
            'title' => 'Judul Postingan Baru',
            'content' => 'Isi konten yang sangat menarik di sini.',
            'is_draft' => false,
            'published_at' => now()->toDateTimeString(),
        ];

        $response = $this->actingAs($user)
            ->postJson(route('posts.store'), $postData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('posts', [
            'title' => 'Judul Postingan Baru',
            'user_id' => $user->id, // Memastikan pemiliknya adalah user yang login
        ]);
    }

    /**
     * Skenario: User yang belum login tidak boleh buat post
     */
    public function test_unauthenticated_user_cannot_create_post()
    {
        $postData = [
            'title' => 'Post Ilegal',
            'content' => 'Konten tanpa login',
        ];

        $response = $this->postJson(route('posts.store'), $postData);

        $response->assertStatus(401);
    }
}
