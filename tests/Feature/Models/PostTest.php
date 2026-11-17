<?php

namespace Tests\Feature\Models;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_scope_includes_posts_published_in_the_past(): void
    {
        $post = $this->createPost(['is_published' => true, 'published_at' => now()->subDay()]);

        $this->assertTrue(Post::published()->whereKey($post->id)->exists());
    }

    public function test_published_scope_excludes_unpublished_posts(): void
    {
        $post = $this->createPost(['is_published' => false, 'published_at' => now()->subDay()]);

        $this->assertFalse(Post::published()->whereKey($post->id)->exists());
    }

    public function test_published_scope_excludes_posts_scheduled_in_the_future(): void
    {
        $post = $this->createPost(['is_published' => true, 'published_at' => now()->addDay()]);

        $this->assertFalse(Post::published()->whereKey($post->id)->exists());
    }

    private function createPost(array $attributes): Post
    {
        return Post::create(array_merge([
            'user_id' => User::factory()->create()->id,
            'title' => 'Test post',
            'slug' => 'test-post-'.Str::random(8),
            'body' => 'Body',
        ], $attributes));
    }
}
