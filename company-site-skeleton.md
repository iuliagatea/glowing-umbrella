# Company site skeleton: Laravel + Filament + Blade + Tailwind + MySQL

Pages: Home (with latest articles), Prices, About us, Contact, Admin login (`/admin`).
Admin (Filament) manages: service prices, categories, articles, contact messages, site settings.

---

## 1. Setup

```bash
composer create-project laravel/laravel company-site
cd company-site

composer require filament/filament:"^4.0"
php artisan filament:install --panels      # creates AdminPanelProvider, path = /admin

npm install                                # Tailwind comes with Laravel's default Vite setup
```

`.env` (database from cPanel > MySQL Databases):

```
APP_NAME="Company Name"
APP_URL=https://yourdomain.com
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=cpaneluser_company
DB_USERNAME=cpaneluser_dbuser
DB_PASSWORD=********
```

Generate everything:

```bash
php artisan make:model ServiceCategory -m
php artisan make:model Service -m
php artisan make:model Post -m
php artisan make:model ContactMessage -m
php artisan make:model Setting -m

php artisan make:controller PageController
php artisan make:controller PriceController
php artisan make:controller PostController
php artisan make:controller ContactController

php artisan make:filament-resource Service --generate
php artisan make:filament-resource ServiceCategory --generate
php artisan make:filament-resource Post --generate
php artisan make:filament-resource ContactMessage --generate
php artisan make:filament-resource Setting --generate

php artisan make:filament-user             # creates the owner's admin account
php artisan migrate
```

---

## 2. Folder structure (only the files you touch)

```
app/
  Http/Controllers/
    PageController.php        home, about
    PriceController.php       prices page
    PostController.php        single article page
    ContactController.php     contact page + form submit
  Models/
    User.php                  implements FilamentUser
    ServiceCategory.php
    Service.php
    Post.php
    ContactMessage.php
    Setting.php
  Filament/Resources/
    Services/ServiceResource.php
    ServiceCategories/ServiceCategoryResource.php
    Posts/PostResource.php
    ContactMessages/ContactMessageResource.php
    Settings/SettingResource.php
  Providers/Filament/AdminPanelProvider.php
database/migrations/          6 files (users is built in)
resources/
  css/app.css
  js/app.js
  views/
    layouts/app.blade.php
    partials/header.blade.php
    partials/footer.blade.php
    home.blade.php
    prices.blade.php
    about.blade.php
    contact.blade.php
    posts/show.blade.php
routes/web.php
```

---

## 3. Migrations

```php
// create_service_categories_table
Schema::create('service_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->unsignedInteger('sort_order')->default(0);
    $table->timestamps();
});

// create_services_table
Schema::create('services', function (Blueprint $table) {
    $table->id();
    $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2);
    $table->string('price_note')->nullable();      // e.g. "from", "per hour"
    $table->boolean('is_active')->default(true);
    $table->unsignedInteger('sort_order')->default(0);
    $table->timestamps();
});

// create_posts_table
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('excerpt')->nullable();
    $table->longText('body');
    $table->string('cover_image')->nullable();
    $table->boolean('is_published')->default(false);
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
});

// create_contact_messages_table
Schema::create('contact_messages', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email');
    $table->string('phone')->nullable();
    $table->text('message');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});

// create_settings_table  (phone, address, opening_hours, about_text, ...)
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->timestamps();
});
```

---

## 4. Models

```php
// app/Models/ServiceCategory.php
class ServiceCategory extends Model
{
    protected $fillable = ['name', 'slug', 'sort_order'];

    public function services()
    {
        return $this->hasMany(Service::class)->orderBy('sort_order');
    }
}

// app/Models/Service.php
class Service extends Model
{
    protected $fillable = [
        'service_category_id', 'name', 'description',
        'price', 'price_note', 'is_active', 'sort_order',
    ];

    protected $casts = ['price' => 'decimal:2', 'is_active' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
}

// app/Models/Post.php
class Post extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'excerpt', 'body',
        'cover_image', 'is_published', 'published_at',
    ];

    protected $casts = ['is_published' => 'boolean', 'published_at' => 'datetime'];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->where('published_at', '<=', now());
    }
}

// app/Models/ContactMessage.php
class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'message', 'read_at'];
    protected $casts = ['read_at' => 'datetime'];
}

// app/Models/Setting.php
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        return cache()->remember("setting.$key", 3600, fn () =>
            static::where('key', $key)->value('value') ?? $default
        );
    }

    protected static function booted(): void
    {
        static::saved(fn ($s) => cache()->forget("setting.{$s->key}"));
    }
}

// app/Models/User.php  (only the additions)
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    // ...default Laravel code...

    public function canAccessPanel(Panel $panel): bool
    {
        return true; // only the owner account exists; tighten later if you add more users
    }
}
```

---

## 5. Routes

```php
// routes/web.php
use App\Http\Controllers\{PageController, PriceController, PostController, ContactController};
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/prices', [PriceController::class, 'index'])->name('prices');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])
    ->middleware('throttle:5,1')
    ->name('contact.send');

Route::get('/articles/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Admin login is provided by Filament at /admin/login
```

---

## 6. Controllers

```php
// PageController.php
class PageController extends Controller
{
    public function home()
    {
        $posts = Post::published()->latest('published_at')->take(3)->get();
        return view('home', compact('posts'));
    }

    public function about()
    {
        return view('about');
    }
}

// PriceController.php
class PriceController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::with(['services' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('prices', compact('categories'));
    }
}

// PostController.php
class PostController extends Controller
{
    public function show(Post $post)
    {
        abort_unless($post->is_published && $post->published_at <= now(), 404);
        return view('posts.show', compact('post'));
    }
}

// ContactController.php
class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        // "website" is a honeypot field: real visitors leave it empty
        if ($request->filled('website')) {
            return back();
        }

        $data = $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email|max:190',
            'phone'   => 'nullable|string|max:40',
            'message' => 'required|string|max:3000',
        ]);

        ContactMessage::create($data);

        return back()->with('success', 'Thank you! We will get back to you soon.');
    }
}
```

---

## 7. Blade views

### `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('description', 'Sales and repair services.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800 antialiased">
    @include('partials.header')
    <main>@yield('content')</main>
    @include('partials.footer')
</body>
</html>
```

### `partials/header.blade.php`

```blade
<header class="border-b">
    <nav class="mx-auto flex max-w-6xl items-center justify-between p-4">
        <a href="{{ route('home') }}" class="text-xl font-bold">{{ config('app.name') }}</a>
        <ul class="flex gap-6 text-sm">
            <li><a href="{{ route('home') }}" class="hover:underline">Home</a></li>
            <li><a href="{{ route('prices') }}" class="hover:underline">Prices</a></li>
            <li><a href="{{ route('about') }}" class="hover:underline">About us</a></li>
            <li><a href="{{ route('contact') }}" class="hover:underline">Contact</a></li>
        </ul>
    </nav>
</header>
```

### `partials/footer.blade.php`

```blade
<footer class="mt-16 border-t bg-gray-50">
    <div class="mx-auto grid max-w-6xl gap-4 p-6 text-sm md:grid-cols-3">
        <div>
            <p class="font-semibold">{{ config('app.name') }}</p>
            <p>{{ \App\Models\Setting::get('address') }}</p>
        </div>
        <div>
            <p>Phone: {{ \App\Models\Setting::get('phone') }}</p>
            <p>Email: {{ \App\Models\Setting::get('email') }}</p>
        </div>
        <div>
            <p class="font-semibold">Opening hours</p>
            <p class="whitespace-pre-line">{{ \App\Models\Setting::get('opening_hours') }}</p>
        </div>
    </div>
</footer>
```

### `home.blade.php`

```blade
@extends('layouts.app')
@section('title', config('app.name') . ' | Sales & repairs')

@section('content')
    {{-- Hero --}}
    <section class="bg-gray-100">
        <div class="mx-auto max-w-6xl px-4 py-20">
            <h1 class="text-4xl font-bold">Quality products and fast repairs</h1>
            <p class="mt-4 max-w-xl text-lg">Short company pitch goes here.</p>
            <a href="{{ route('prices') }}" class="mt-6 inline-block rounded bg-black px-5 py-3 text-white">See our prices</a>
        </div>
    </section>

    {{-- What we do --}}
    <section class="mx-auto grid max-w-6xl gap-6 px-4 py-16 md:grid-cols-2">
        <div class="rounded border p-6"><h2 class="text-xl font-semibold">Our products</h2><p class="mt-2">Describe what you sell in the store.</p></div>
        <div class="rounded border p-6"><h2 class="text-xl font-semibold">Repair service</h2><p class="mt-2">Describe what you repair.</p></div>
    </section>

    {{-- Latest articles --}}
    @if($posts->isNotEmpty())
    <section class="mx-auto max-w-6xl px-4">
        <h2 class="text-2xl font-bold">Latest articles</h2>
        <div class="mt-6 grid gap-6 md:grid-cols-3">
            @foreach($posts as $post)
                <article class="overflow-hidden rounded border">
                    @if($post->cover_image)
                        <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="h-44 w-full object-cover">
                    @endif
                    <div class="p-4">
                        <time class="text-xs text-gray-500">{{ $post->published_at->format('d M Y') }}</time>
                        <h3 class="mt-1 font-semibold">
                            <a href="{{ route('posts.show', $post) }}" class="hover:underline">{{ $post->title }}</a>
                        </h3>
                        <p class="mt-2 text-sm">{{ $post->excerpt }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
    @endif
@endsection
```

### `prices.blade.php`

```blade
@extends('layouts.app')
@section('title', 'Prices | ' . config('app.name'))

@section('content')
<div class="mx-auto max-w-4xl px-4 py-12">
    <h1 class="text-3xl font-bold">Service prices</h1>

    @foreach($categories as $category)
        @continue($category->services->isEmpty())
        <h2 class="mt-10 text-xl font-semibold">{{ $category->name }}</h2>
        <table class="mt-3 w-full text-left text-sm">
            <tbody class="divide-y">
            @foreach($category->services as $service)
                <tr>
                    <td class="py-3 pr-4">
                        <p class="font-medium">{{ $service->name }}</p>
                        @if($service->description)<p class="text-gray-500">{{ $service->description }}</p>@endif
                    </td>
                    <td class="py-3 text-right font-semibold whitespace-nowrap">
                        {{ $service->price_note }} {{ number_format($service->price, 2) }} RON
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach
</div>
@endsection
```

### `about.blade.php`

```blade
@extends('layouts.app')
@section('title', 'About us | ' . config('app.name'))

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12">
    <h1 class="text-3xl font-bold">About us</h1>
    <div class="mt-6 space-y-4 whitespace-pre-line">{{ \App\Models\Setting::get('about_text', 'Tell your story here.') }}</div>
</div>
@endsection
```

### `contact.blade.php`

```blade
@extends('layouts.app')
@section('title', 'Contact | ' . config('app.name'))

@section('content')
<div class="mx-auto grid max-w-5xl gap-10 px-4 py-12 md:grid-cols-2">
    <div>
        <h1 class="text-3xl font-bold">Contact</h1>
        <p class="mt-4">{{ \App\Models\Setting::get('address') }}</p>
        <p>Phone: {{ \App\Models\Setting::get('phone') }}</p>
        <p>Email: {{ \App\Models\Setting::get('email') }}</p>
    </div>

    <form method="POST" action="{{ route('contact.send') }}" class="space-y-4">
        @csrf
        @if(session('success'))
            <p class="rounded bg-green-100 p-3 text-green-800">{{ session('success') }}</p>
        @endif

        {{-- honeypot --}}
        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">

        <div>
            <label class="block text-sm font-medium">Name</label>
            <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded border p-2" required>
            @error('name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded border p-2" required>
            @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Phone (optional)</label>
            <input name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded border p-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Message</label>
            <textarea name="message" rows="5" class="mt-1 w-full rounded border p-2" required>{{ old('message') }}</textarea>
            @error('message')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button class="rounded bg-black px-5 py-2 text-white">Send message</button>
    </form>
</div>
@endsection
```

### `posts/show.blade.php`

```blade
@extends('layouts.app')
@section('title', $post->title . ' | ' . config('app.name'))
@section('description', $post->excerpt)

@section('content')
<article class="mx-auto max-w-3xl px-4 py-12">
    <time class="text-sm text-gray-500">{{ $post->published_at->format('d M Y') }}</time>
    <h1 class="mt-1 text-3xl font-bold">{{ $post->title }}</h1>
    @if($post->cover_image)
        <img src="{{ asset('storage/'.$post->cover_image) }}" alt="" class="mt-6 w-full rounded">
    @endif
    <div class="prose mt-6 max-w-none">{!! $post->body !!}</div>
</article>
@endsection
```

> `{!! $post->body !!}` outputs raw HTML. That is fine here because only the owner can write posts through the admin, but keep the admin password strong.
> For nicer article typography, run `npm i -D @tailwindcss/typography` and enable it in your Tailwind setup so `prose` works.

---

## 8. Filament admin (login at `/admin/login`)

Filament gives you the login page, session handling, and password-protected area out of the box.

Optional tweak in `AdminPanelProvider.php`:

```php
return $panel
    ->default()
    ->id('admin')
    ->path('admin')
    ->login()
    ->brandName('Company admin')
    ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
    // ...
;
```

### `ServiceResource` (the important one: the owner edits prices here)

```php
use BackedEnum;
use Filament\Forms\Components\{Select, TextInput, Textarea, Toggle};
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\{TextColumn, ToggleColumn};
use Filament\Tables\Table;
use Filament\Actions\{EditAction, DeleteAction};

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('service_category_id')
                ->relationship('category', 'name')->required(),
            TextInput::make('name')->required()->maxLength(190),
            Textarea::make('description')->rows(3),
            TextInput::make('price')->numeric()->prefix('RON')->required(),
            TextInput::make('price_note')->label('Price note (e.g. "from")')->maxLength(40),
            TextInput::make('sort_order')->numeric()->default(0),
            Toggle::make('is_active')->label('Visible on website')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('price')->money('RON')->sortable(),
                ToggleColumn::make('is_active')->label('Visible'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->defaultSort('sort_order');
    }
}
```

### `PostResource` form (rich text editor + image upload + slug)

```php
use Filament\Forms\Components\{DateTimePicker, FileUpload, RichEditor, TextInput, Textarea, Toggle};
use Illuminate\Support\Str;

TextInput::make('title')->required()->live(onBlur: true)
    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
TextInput::make('slug')->required()->unique(ignoreRecord: true),
Textarea::make('excerpt')->rows(2)->maxLength(300),
RichEditor::make('body')->required()->columnSpanFull(),
FileUpload::make('cover_image')->image()->directory('posts'),
Toggle::make('is_published'),
DateTimePicker::make('published_at')->default(now()),
```

Also set the author automatically in `CreatePost`:

```php
protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['user_id'] = auth()->id();
    return $data;
}
```

### `ServiceCategoryResource` form

```php
TextInput::make('name')->required()->live(onBlur: true)
    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
TextInput::make('slug')->required()->unique(ignoreRecord: true),
TextInput::make('sort_order')->numeric()->default(0),
```

### `ContactMessageResource`

Make it read-only: show `name`, `email`, `phone`, `message`, `created_at`, and a "mark as read" action that sets `read_at`. Remove the Create page.

### `SettingResource`

Seed these keys so the owner just edits values: `phone`, `email`, `address`, `opening_hours`, `about_text`.

```php
// database/seeders/DatabaseSeeder.php
foreach (['phone', 'email', 'address', 'opening_hours', 'about_text'] as $key) {
    \App\Models\Setting::firstOrCreate(['key' => $key]);
}
```

In the form, make `key` read-only (`->disabled()`) and `value` a `Textarea`.

---

## 9. Deploying on cPanel

1. Build assets locally: `npm run build`, then `composer install --no-dev --optimize-autoloader`.
2. Upload the project **outside** `public_html` (e.g. `/home/user/company-site`).
3. Copy the contents of the project's `public/` folder into `public_html/` and edit `public_html/index.php` so the two paths point to the app folder:
   ```php
   require __DIR__.'/../company-site/vendor/autoload.php';
   $app = require_once __DIR__.'/../company-site/bootstrap/app.php';
   ```
4. Create the MySQL database and user in cPanel, fill in `.env`, then run `php artisan key:generate` and `php artisan migrate --force` (Terminal/SSH, or "Setup PHP/Run script" if there is no SSH).
5. Run `php artisan storage:link` (or create the symlink in `public_html/storage` manually) so uploaded images show up.
6. Select PHP 8.2+ in cPanel > MultiPHP Manager, enable AutoSSL, and keep `APP_DEBUG=false`.
7. Back up the database regularly (cPanel > Backup).

---

## 10. Suggested build order

1. Fresh Laravel + Filament install, migrations, admin user, confirm `/admin` login works.
2. Models + Filament resources, enter a few test services and posts.
3. Layout, header/footer, and the four public pages.
4. Contact form + messages in admin.
5. Design polish, SEO titles/descriptions, favicon, deploy.
