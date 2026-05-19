<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($this->stats() as $stat)
                <a
                    href="{{ $stat['url'] }}"
                    class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:border-primary-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</div>
                    <div class="mt-3 text-3xl font-bold text-gray-950 dark:text-white">{{ number_format($stat['value']) }}</div>
                    <div class="mt-3 text-xs font-semibold text-primary-600">Detaya git</div>
                </a>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.5fr_1fr]">
            <section id="high-risk-content" class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                    <h2 class="text-base font-bold text-gray-950 dark:text-white">Yuksek AI Riskli Icerikler</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($this->highRiskContent() as $item)
                        <a href="{{ $item['url'] }}" class="grid gap-3 px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/60 md:grid-cols-[150px_1fr_90px]">
                            <div>
                                <div class="text-xs font-bold text-gray-500">{{ $item['type'] }}</div>
                                <div class="mt-1 text-xs text-gray-400">{{ $item['created_at']?->format('d.m.Y H:i') }}</div>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-950 dark:text-white">{{ $item['title'] }}</div>
                                <div class="mt-1 text-xs text-gray-500">{{ $item['user'] }} · {{ $item['status'] }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-danger-600">{{ $item['risk_score'] }}</div>
                                <div class="text-xs font-semibold text-gray-500">{{ $item['risk_label'] }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-sm text-gray-500">Yuksek riskli icerik bulunmuyor.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                    <h2 class="text-base font-bold text-gray-950 dark:text-white">Son 24 Saat</h2>
                </div>
                <div class="grid grid-cols-2 gap-3 p-5">
                    @foreach($this->last24HoursStats() as $label => $value)
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                            <div class="text-xs font-semibold uppercase text-gray-500">{{ str($label)->replace('_', ' ') }}</div>
                            <div class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">{{ number_format($value) }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-100 p-5 dark:border-gray-800">
                    <h3 class="text-sm font-bold text-gray-950 dark:text-white">Hizli Aksiyonlar</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($this->quickLinks() as $link)
                            <a href="{{ $link['url'] }}" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 transition hover:border-primary-300 hover:text-primary-700 dark:border-gray-700 dark:text-gray-200">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                    <h2 class="text-base font-bold text-gray-950 dark:text-white">Son Moderasyon Aksiyonlari</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($this->recentModerationActions() as $action)
                        <a href="{{ $action['url'] }}" class="block px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/60">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-sm font-bold text-gray-950 dark:text-white">{{ $action['title'] }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ $action['type'] }} · {{ $action['user'] }} · {{ $action['moderator'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold text-primary-600">{{ $action['action'] }}</div>
                                    <div class="mt-1 text-xs text-gray-400">{{ $action['time']?->format('d.m H:i') }}</div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-sm text-gray-500">Son moderasyon aksiyonu yok.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                    <h2 class="text-base font-bold text-gray-950 dark:text-white">Guven Skoru Dusen Kullanicilar</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($this->lowTrustUsers() as $user)
                        <a href="{{ url('/admin/users/' . $user->id) }}" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/60">
                            <div>
                                <div class="font-semibold text-gray-950 dark:text-white">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-danger-600">{{ $user->community_trust_score }}</div>
                                <div class="text-xs text-gray-500">{{ $user->forum_reputation }} itibar</div>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-sm text-gray-500">Dusuk guven skorlu kullanici yok.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <h2 class="text-base font-bold text-gray-950 dark:text-white">En Cok Raporlanan Kullanicilar</h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($this->topReportedUsers() as $row)
                    <a href="{{ url('/admin/users/' . $row['user']->id) }}" class="grid gap-3 px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/60 md:grid-cols-[1fr_120px_120px_120px]">
                        <div>
                            <div class="font-semibold text-gray-950 dark:text-white">{{ $row['user']->name }}</div>
                            <div class="text-xs text-gray-500">{{ $row['user']->email }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-500">Rapor</div>
                            <div class="text-lg font-bold text-gray-950 dark:text-white">{{ $row['reports_count'] }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-500">Max AI Risk</div>
                            <div class="text-lg font-bold text-danger-600">{{ $row['max_risk'] }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-500">Guven</div>
                            <div class="text-lg font-bold text-gray-950 dark:text-white">{{ $row['user']->community_trust_score }}</div>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-8 text-sm text-gray-500">Raporlanan kullanici verisi yok.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-filament-panels::page>
