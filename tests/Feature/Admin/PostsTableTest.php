<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class PostsTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_page_shows_post_records(): void
    {
        $author = User::factory()->create();
        $posts = collect([
            $this->createPost($author, ['title' => 'First Post']),
            $this->createPost($author, ['title' => 'Second Post']),
            $this->createPost($author, ['title' => 'Third Post']),
        ]);

        $this->actingAs($author);

        Livewire::test(ListPosts::class)
            ->assertOk()
            ->assertCanSeeTableRecords($posts);
    }

    public function test_title_column_is_searchable(): void
    {
        $author = User::factory()->create();
        $matching = $this->createPost($author, ['title' => 'Unique Searchable Title']);
        $other = $this->createPost($author, ['title' => 'Something Else']);

        $this->actingAs($author);

        Livewire::test(ListPosts::class)
            ->searchTable('Unique Searchable Title')
            ->assertCanSeeTableRecords([$matching])
            ->assertCanNotSeeTableRecords([$other]);
    }

    private function createPost(User $author, array $attributes): Post
    {
        return Post::create(array_merge([
            'user_id' => $author->id,
            'title' => 'Test post',
            'slug' => 'test-post-'.Str::random(8),
            'body' => 'Body',
        ], $attributes));
    }
}
