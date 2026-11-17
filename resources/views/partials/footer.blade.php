<footer class="mt-16 border-t border-outline bg-surface">
    <div class="mx-auto grid max-w-6xl gap-4 p-6 text-sm text-ink md:grid-cols-3">
        <div>
            <p class="font-semibold">{{ \App\Models\Setting::get('site_name', config('app.name')) }}</p>
            <p class="text-slate-600">{{ \App\Models\Setting::get('address') }}</p>
        </div>
        <div>
            <p class="text-slate-600">Phone: {{ \App\Models\Setting::get('phone') }}</p>
            <p class="text-slate-600">Email: {{ \App\Models\Setting::get('email') }}</p>
        </div>
        <div>
            <p class="font-semibold">Opening hours</p>
            <p class="whitespace-pre-line text-slate-600">{{ \App\Models\Setting::get('opening_hours') }}</p>
        </div>
    </div>
</footer>