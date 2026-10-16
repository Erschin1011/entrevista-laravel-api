<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_posts_api_supports_crud_and_validation(): void
    {
        $this->postJson('/api/posts', [])->assertUnprocessable();

        $this->postJson('/api/posts', [
            'title' => 'First post',
            'body' => 'Post body',
        ])->assertCreated()
            ->assertJsonPath('title', 'First post');

        $postId = (int) $this->getJson('/api/posts')->assertOk()->json('data.0.id');

        $this->getJson("/api/posts/{$postId}")
            ->assertOk()
            ->assertJsonPath('body', 'Post body');

        $this->patchJson("/api/posts/{$postId}", ['title' => 'Updated post'])
            ->assertOk()
            ->assertJsonPath('title', 'Updated post');

        $this->deleteJson("/api/posts/{$postId}")->assertNoContent();
        $this->getJson("/api/posts/{$postId}")->assertNotFound();
    }
}
