<?php

namespace Tests\Feature\Admin;

use App\Filament\Widgets\DigitOverview;
use App\Models\Announcement;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use ReflectionMethod;
use Tests\TestCase;

class DigitOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_renders_successfully(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(DigitOverview::class)->assertOk();
    }

    public function test_overview_counts_only_active_services(): void
    {
        $category = ServiceCategory::create(['name' => 'Laptops', 'slug' => 'laptops', 'sort_order' => 1]);
        $this->createService($category, ['name' => 'Active Service', 'is_active' => true]);
        $this->createService($category, ['name' => 'Inactive Service', 'is_active' => false]);

        $stat = $this->getStat('Active Services');

        $this->assertSame(1, $stat->getValue());
    }

    public function test_overview_counts_only_published_posts(): void
    {
        $author = User::factory()->create();
        $this->createPost($author, ['title' => 'Published', 'is_published' => true, 'published_at' => now()->subDay()]);
        $this->createPost($author, ['title' => 'Draft', 'is_published' => false, 'published_at' => null]);

        $stat = $this->getStat('Published Posts');

        $this->assertSame(1, $stat->getValue());
    }

    public function test_overview_counts_only_active_announcements(): void
    {
        Announcement::factory()->create(['is_active' => true, 'published_at' => now()->subDay()]);
        Announcement::factory()->create(['is_active' => false, 'published_at' => now()->subDay()]);

        $stat = $this->getStat('Active Announcements');

        $this->assertSame(1, $stat->getValue());
    }

    public function test_overview_counts_only_unread_messages(): void
    {
        $this->createMessage(['name' => 'Unread', 'read_at' => null]);
        $this->createMessage(['name' => 'Read', 'read_at' => now()]);

        $stat = $this->getStat('Unread Messages');

        $this->assertSame(1, $stat->getValue());
    }

    /**
     * @return array<Stat>
     */
    private function getStats(): array
    {
        $widget = new DigitOverview;

        $method = new ReflectionMethod($widget, 'getStats');
        $method->setAccessible(true);

        return $method->invoke($widget);
    }

    private function getStat(string $label): Stat
    {
        foreach ($this->getStats() as $stat) {
            if ($stat->getLabel() === $label) {
                return $stat;
            }
        }

        $this->fail("No stat found with label [{$label}].");
    }

    private function createService(ServiceCategory $category, array $attributes): Service
    {
        return Service::create(array_merge([
            'service_category_id' => $category->id,
            'name' => 'Test service',
            'description' => 'Description',
            'price' => 10,
            'sort_order' => 1,
        ], $attributes));
    }

    private function createPost(User $author, array $attributes): Post
    {
        return Post::create(array_merge([
            'user_id' => $author->id,
            'title' => 'Test post',
            'slug' => 'test-post-'.uniqid(),
            'body' => 'Body',
        ], $attributes));
    }

    private function createMessage(array $attributes = []): ContactMessage
    {
        return ContactMessage::create(array_merge([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0712 345 678',
            'message' => 'I need a quote for a laptop repair.',
        ], $attributes));
    }
}
