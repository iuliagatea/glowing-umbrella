<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_about_page_renders(): void
    {
        $this->get('/about')
            ->assertStatus(200)
            ->assertSee('About us');
    }

    public function test_prices_page_renders(): void
    {
        $this->get('/prices')
            ->assertStatus(200)
            ->assertSee('Service prices');
    }

    public function test_contact_page_renders(): void
    {
        $this->get('/contact')
            ->assertStatus(200)
            ->assertSee('Contact');
    }

    public function test_articles_nav_link_is_hidden_when_there_are_no_published_articles(): void
    {
        $author = User::factory()->create();
        Post::create([
            'user_id' => $author->id,
            'title' => 'Draft guide',
            'slug' => 'draft-guide-nav',
            'body' => 'Draft body',
            'is_published' => false,
            'published_at' => now()->subDay(),
        ]);

        $this->get('/')->assertDontSee('Articles');
    }

    public function test_articles_nav_link_is_shown_when_a_published_article_exists(): void
    {
        $author = User::factory()->create();
        Post::create([
            'user_id' => $author->id,
            'title' => 'Published nav guide',
            'slug' => 'published-nav-guide',
            'body' => 'Body',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $this->get('/')->assertSee('Articles');
    }

    public function test_articles_index_lists_only_published_articles(): void
    {
        $author = User::factory()->create();

        $published = Post::create([
            'user_id' => $author->id,
            'title' => 'Published guide',
            'slug' => 'published-guide',
            'body' => 'Published body',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        Post::create([
            'user_id' => $author->id,
            'title' => 'Draft guide',
            'slug' => 'draft-guide-index',
            'body' => 'Draft body',
            'is_published' => false,
            'published_at' => now()->subDay(),
        ]);

        $this->get(route('posts.index'))
            ->assertStatus(200)
            ->assertSee('Published guide')
            ->assertDontSee('Draft guide')
            ->assertSee(route('posts.show', $published), false);
    }

    public function test_published_article_renders_by_slug(): void
    {
        $author = User::factory()->create();
        $post = Post::create([
            'user_id' => $author->id,
            'title' => 'Repair guide',
            'slug' => 'repair-guide',
            'body' => 'Article body',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->get(route('posts.show', ['post' => $post->slug]))
            ->assertStatus(200)
            ->assertSee('Repair guide')
            ->assertSee('Article body')
            ->assertSee('<meta name="description" content="Sales and repair services.">', false);
    }

    public function test_article_with_an_excerpt_uses_it_as_the_meta_description(): void
    {
        $author = User::factory()->create();
        $post = Post::create([
            'user_id' => $author->id,
            'title' => 'Repair guide',
            'slug' => 'repair-guide-with-excerpt',
            'excerpt' => 'Everything you need to know about screen repairs.',
            'body' => 'Article body',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->get(route('posts.show', ['post' => $post->slug]))
            ->assertSee('<meta name="description" content="Everything you need to know about screen repairs.">', false);
    }

    public function test_unpublished_article_returns_404(): void
    {
        $author = User::factory()->create();
        $post = Post::create([
            'user_id' => $author->id,
            'title' => 'Draft guide',
            'slug' => 'draft-guide',
            'body' => 'Draft body',
            'is_published' => false,
            'published_at' => now(),
        ]);

        $this->get(route('posts.show', ['post' => $post->slug]))
            ->assertStatus(404);
    }

    public function test_article_scheduled_for_the_future_returns_404(): void
    {
        $author = User::factory()->create();
        $post = Post::create([
            'user_id' => $author->id,
            'title' => 'Scheduled guide',
            'slug' => 'scheduled-guide',
            'body' => 'Scheduled body',
            'is_published' => true,
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('posts.show', ['post' => $post->slug]))
            ->assertStatus(404);
    }
}
