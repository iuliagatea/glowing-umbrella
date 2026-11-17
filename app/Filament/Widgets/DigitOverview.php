<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DigitOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active Services', Service::query()->where('is_active', true)->count())
                ->description('Published to the services page')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),

            Stat::make('Published Posts', Post::query()->published()->count())
                ->description('Live on the blog')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('info'),

            Stat::make('Active Announcements', Announcement::query()->active()->count())
                ->description('Currently visible to visitors')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('warning'),

            Stat::make('Unread Messages', ContactMessage::query()->whereNull('read_at')->count())
                ->description('Awaiting a reply')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('danger'),
        ];
    }
}
