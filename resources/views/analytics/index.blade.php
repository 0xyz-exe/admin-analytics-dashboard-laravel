<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Admin Analytics</h2></x-slot>
    <main class="py-8 max-w-6xl mx-auto space-y-6">
        <section class="bg-white p-6 shadow rounded"><div class="flex justify-between gap-3 flex-wrap"><h3 class="font-bold text-lg">Account metrics</h3><a class="text-indigo-700 underline" href="{{ route('dashboard.export',request()->query()) }}">Export CSV</a></div>
            <form method="get" class="flex gap-3 flex-wrap mt-4"><label>From<input type="date" name="from" value="{{ $from->toDateString() }}" class="block rounded border"></label><label>To<input type="date" name="to" value="{{ $to->toDateString() }}" class="block rounded border"></label><button class="self-end px-4 py-2 bg-indigo-600 text-white rounded">Apply</button></form>
        </section>
        <section class="grid sm:grid-cols-3 gap-4" aria-label="Summary metrics"><article class="bg-white p-5 shadow rounded"><p class="text-gray-600">Total accounts</p><p class="text-3xl font-bold">{{ number_format($totalUsers) }}</p><small>All persisted users</small></article><article class="bg-white p-5 shadow rounded"><p class="text-gray-600">Signups in range</p><p class="text-3xl font-bold">{{ number_format($signups) }}</p><small>{{ $from->toDateString() }} to {{ $to->toDateString() }}</small></article><article class="bg-white p-5 shadow rounded"><p class="text-gray-600">Verified in range</p><p class="text-3xl font-bold">{{ number_format($verified) }}</p><small>{{ $signups ? number_format($verified / $signups * 100, 1).'%' : '0%' }} of signups</small></article></section>
        <section class="bg-white p-6 shadow rounded"><h3 class="font-bold text-lg">Daily signups</h3><p class="text-sm text-gray-600">Counted from persisted user creation dates; days without signups are omitted.</p>
            <div class="mt-6 flex items-end gap-1 h-56 overflow-x-auto" role="img" aria-label="Daily signup bar chart">
                @forelse($monthly as $point)<div class="min-w-8 flex flex-col items-center justify-end h-full" title="{{ $point->day }}: {{ $point->total }} signups"><span class="text-xs">{{ $point->total }}</span><div class="w-6 bg-indigo-600 rounded-t" style="height: {{ max(4, $point->total / max(1, $monthly->max('total')) * 80) }}%"></div><small class="text-xs rotate-45 origin-left">{{ \Illuminate\Support\Carbon::parse($point->day)->format('M j') }}</small></div>@empty<p class="text-gray-600 self-center">No signups in this range.</p>@endforelse
            </div>
            <div class="mt-8 overflow-x-auto"><table class="w-full text-left"><thead><tr><th>Date</th><th>Signups</th><th>Verified</th></tr></thead><tbody>@forelse($monthly as $point)<tr class="border-t"><td class="py-2">{{ $point->day }}</td><td>{{ $point->total }}</td><td>{{ $point->verified }}</td></tr>@empty<tr><td colspan="3">No records.</td></tr>@endforelse</tbody></table></div>
        </section>
    </main>
</x-app-layout>
