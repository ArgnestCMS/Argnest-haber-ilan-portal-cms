<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($this->insightCards() as $card)
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $card['label'] }}</div>
                    <div class="mt-3 text-3xl font-bold text-gray-950 dark:text-white">{{ $card['value'] }}</div>
                    <div class="mt-2 text-xs font-semibold text-gray-500">{{ $card['detail'] }}</div>
                </section>
            @endforeach
        </div>

        <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <h2 class="text-base font-bold text-gray-950 dark:text-white">Zaman Araligi Metrikleri</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($this->quickLinks() as $link)
                        <a href="{{ $link['url'] }}" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 transition hover:border-primary-300 hover:text-primary-700 dark:border-gray-700 dark:text-gray-200">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-bold uppercase text-gray-500 dark:bg-gray-800/60">
                            <th class="px-5 py-3">Aralik</th>
                            <th class="px-5 py-3">Yeni Kullanici</th>
                            <th class="px-5 py-3">Forum Konu</th>
                            <th class="px-5 py-3">Forum Cevap</th>
                            <th class="px-5 py-3">Canli Sohbet</th>
                            <th class="px-5 py-3">Rapor</th>
                            <th class="px-5 py-3">Yuksek AI Risk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($this->rangeMetrics() as $range => $metrics)
                            <tr>
                                <td class="px-5 py-4 font-bold text-gray-950 dark:text-white">{{ $range }}</td>
                                <td class="px-5 py-4">{{ number_format($metrics['new_users']) }}</td>
                                <td class="px-5 py-4">{{ number_format($metrics['forum_topics']) }}</td>
                                <td class="px-5 py-4">{{ number_format($metrics['forum_posts']) }}</td>
                                <td class="px-5 py-4">{{ number_format($metrics['chat_messages']) }}</td>
                                <td class="px-5 py-4">{{ number_format($metrics['reports']) }}</td>
                                <td class="px-5 py-4 font-bold text-danger-600">{{ number_format($metrics['high_ai_risk']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                    <h2 class="text-base font-bold text-gray-950 dark:text-white">En Aktif Kullanicilar</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($this->activeUsers() as $row)
                        <a href="{{ url('/admin/users/' . $row['user']->id) }}" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/60">
                            <div>
                                <div class="font-semibold text-gray-950 dark:text-white">{{ $row['user']->name }}</div>
                                <div class="text-xs text-gray-500">{{ $row['user']->email }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-primary-600">{{ number_format($row['score']) }}</div>
                                <div class="text-xs text-gray-500">{{ $row['user']->forum_reputation }} rep · {{ $row['user']->forum_xp }} XP</div>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-sm text-gray-500">Aktif kullanici verisi yok.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                    <h2 class="text-base font-bold text-gray-950 dark:text-white">En Cok Etkilesim Alan Konular</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($this->topInteractedTopics() as $topic)
                        <a href="{{ $topic['url'] }}" class="grid gap-3 px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/60 md:grid-cols-[1fr_90px_90px_90px]">
                            <div>
                                <div class="font-semibold text-gray-950 dark:text-white">{{ $topic['title'] }}</div>
                                <div class="text-xs text-gray-500">{{ $topic['user'] }} · skor {{ number_format($topic['score']) }}</div>
                            </div>
                            <div><div class="text-xs text-gray-500">Okunma</div><div class="font-bold">{{ number_format($topic['views']) }}</div></div>
                            <div><div class="text-xs text-gray-500">Begeni</div><div class="font-bold">{{ number_format($topic['likes']) }}</div></div>
                            <div><div class="text-xs text-gray-500">Cevap</div><div class="font-bold">{{ number_format($topic['posts']) }}</div></div>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-sm text-gray-500">Konu etkilesim verisi yok.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <h2 class="text-base font-bold text-gray-950 dark:text-white">En Cok Raporlanan Icerikler</h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($this->topReportedContent() as $item)
                    <a href="{{ $item['url'] }}" class="grid gap-3 px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/60 md:grid-cols-[140px_1fr_110px_110px]">
                        <div>
                            <div class="text-xs font-bold text-gray-500">{{ $item['type'] }}</div>
                            <div class="mt-1 text-xs text-gray-400">{{ $item['latest']?->format('d.m.Y H:i') }}</div>
                        </div>
                        <div class="font-semibold text-gray-950 dark:text-white">{{ $item['title'] }}</div>
                        <div><div class="text-xs text-gray-500">Rapor</div><div class="text-lg font-bold">{{ number_format($item['reports_count']) }}</div></div>
                        <div><div class="text-xs text-gray-500">Max Risk</div><div class="text-lg font-bold text-danger-600">{{ number_format($item['max_risk']) }}</div></div>
                    </a>
                @empty
                    <div class="px-5 py-8 text-sm text-gray-500">Raporlanan icerik verisi yok.</div>
                @endforelse
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                    <h2 class="text-base font-bold text-gray-950 dark:text-white">Reputation Liderleri</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($this->reputationLeaders() as $user)
                        <a href="{{ url('/admin/users/' . $user->id) }}" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/60">
                            <div>
                                <div class="font-semibold text-gray-950 dark:text-white">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">Seviye {{ $user->forum_level }} · guven {{ $user->community_trust_score }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-primary-600">{{ number_format($user->forum_reputation) }}</div>
                                <div class="text-xs text-gray-500">{{ number_format($user->forum_xp) }} XP</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                    <h2 class="text-base font-bold text-gray-950 dark:text-white">XP Liderleri</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($this->xpLeaders() as $user)
                        <a href="{{ url('/admin/users/' . $user->id) }}" class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-gray-50 dark:hover:bg-gray-800/60">
                            <div>
                                <div class="font-semibold text-gray-950 dark:text-white">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">Seviye {{ $user->forum_level }} · rep {{ $user->forum_reputation }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-success-600">{{ number_format($user->forum_xp) }}</div>
                                <div class="text-xs text-gray-500">guven {{ $user->community_trust_score }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-filament-panels::page>
