<?php

namespace App\Support;

use App\Models\ForumCategory;
use App\Models\ForumTag;
use App\Models\ForumTopic;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ForumAssistant
{
    private const STOP_WORDS = [
        'acaba', 'ama', 'ancak', 'bir', 'biri', 'bunu', 'cok', 'daha', 'diye', 'gibi',
        'icin', 'ile', 'ilgili', 'mi', 'mu', 'nasıl', 'nasil', 'ne', 'neden', 'olan',
        'olarak', 'sonra', 'var', 've', 'veya', 'ya', 'yani', 'yeni', 'ben', 'biz',
        'siz', 'forum', 'konu', 'cevap', 'hakkinda', 'hakkında',
    ];

    public function topicSuggestions(string $title, string $content, ?int $categoryId = null): array
    {
        $plain = $this->plainText($content);
        $keywords = $this->keywords($title . ' ' . $plain);
        $category = $categoryId ? ForumCategory::query()->find($categoryId) : null;

        return [
            'title_suggestions' => $this->titleSuggestions($title, $plain, $keywords, $category?->name),
            'summary' => $this->summary($plain),
            'similar_topics' => $this->similarTopics($title, $plain, $keywords),
            'tag_suggestions' => $this->tagSuggestions($keywords, $category?->name),
            'writing_suggestions' => $this->writingSuggestions($title, $plain),
            'moderation_explanation' => $this->moderationExplanation($title . ' ' . $plain),
            'moderator_risk_summary' => $this->moderatorRiskSummary($title . ' ' . $plain),
        ];
    }

    public function replySuggestions(ForumTopic $topic, string $content): array
    {
        $plain = $this->plainText($content);

        return [
            'title_suggestions' => [],
            'summary' => $this->summary($plain),
            'similar_topics' => [],
            'tag_suggestions' => [],
            'writing_suggestions' => $this->replyWritingSuggestions($topic, $plain),
            'moderation_explanation' => $this->moderationExplanation($plain),
            'moderator_risk_summary' => $this->moderatorRiskSummary($plain),
        ];
    }

    public function moderationReasons(string $title, string $content): array
    {
        $text = trim($title . ' ' . $this->plainText($content));
        $summary = $this->moderatorRiskSummary($text);

        if ($summary === 'Belirgin ek local risk sinyali yok.') {
            return [];
        }

        return ['Local assistant risk ozeti: ' . $summary];
    }

    private function titleSuggestions(string $title, string $plain, Collection $keywords, ?string $categoryName): array
    {
        $base = $keywords->take(4)->map(fn ($word) => Str::headline($word))->implode(' ');
        $categoryPrefix = $categoryName ? Str::headline($categoryName) . ': ' : '';
        $shortSummary = Str::of($plain)->words(8, '')->trim()->toString();

        return collect([
            $title && Str::length($title) >= 12 ? Str::of($title)->replaceMatches('/\s+/', ' ')->trim()->limit(80, '')->toString() : null,
            $base ? $categoryPrefix . $base . ' Hakkinda Soru' : null,
            $shortSummary ? Str::headline(Str::limit($shortSummary, 70, '')) : null,
        ])
            ->filter()
            ->unique(fn ($item) => Str::lower($item))
            ->take(3)
            ->values()
            ->all();
    }

    private function summary(string $plain): string
    {
        if ($plain === '') {
            return 'Ozet olusturmak icin daha fazla icerik gerekli.';
        }

        $sentences = collect(preg_split('/(?<=[.!?])\s+/u', $plain) ?: [])
            ->map(fn ($sentence) => trim($sentence))
            ->filter();

        $summary = $sentences->take(2)->implode(' ');

        return Str::limit($summary ?: $plain, 260);
    }

    private function similarTopics(string $title, string $plain, Collection $keywords): array
    {
        if ($keywords->isEmpty() && Str::length(trim($title)) < 8) {
            return [];
        }

        return ForumTopic::query()
            ->published()
            ->with('category:id,name')
            ->withCount('approvedPosts')
            ->where(function ($query) use ($title, $keywords) {
                $searchTitle = trim($title);

                if (Str::length($searchTitle) >= 8) {
                    $query->orWhere('title', 'like', '%' . Str::limit($searchTitle, 50, '') . '%');
                }

                foreach ($keywords->take(5) as $keyword) {
                    $query->orWhere('title', 'like', '%' . $keyword . '%')
                        ->orWhere('content', 'like', '%' . $keyword . '%');
                }
            })
            ->latest('last_post_at')
            ->take(5)
            ->get()
            ->map(fn (ForumTopic $topic) => [
                'title' => $topic->title,
                'url' => route('forum.topics.show', $topic->slug),
                'category' => $topic->category?->name,
                'replies' => $topic->approved_posts_count,
            ])
            ->all();
    }

    private function tagSuggestions(Collection $keywords, ?string $categoryName): array
    {
        $existingTags = ForumTag::query()
            ->where('is_active', true)
            ->where(function ($query) use ($keywords) {
                foreach ($keywords->take(8) as $keyword) {
                    $query->orWhere('name', 'like', '%' . $keyword . '%');
                }
            })
            ->limit(5)
            ->pluck('name');

        return $existingTags
            ->merge($keywords->take(5)->map(fn ($word) => Str::headline($word)))
            ->when($categoryName, fn ($tags) => $tags->push($categoryName))
            ->filter()
            ->unique(fn ($tag) => Str::lower($tag))
            ->take(5)
            ->values()
            ->all();
    }

    private function writingSuggestions(string $title, string $plain): array
    {
        $suggestions = [];

        if (Str::length(trim($title)) < 12) {
            $suggestions[] = 'Basligi biraz daha acik yazin; konu, sorun ve ana anahtar kelime gorunsun.';
        }

        if ($this->wordCount($plain) < 25) {
            $suggestions[] = 'Icerige kisa bir baglam, ne denediginiz ve beklediginiz yaniti ekleyin.';
        }

        if (! Str::contains($plain, ['?', 'nedir', 'nasil', 'nasıl', 'hangi'])) {
            $suggestions[] = 'Okuyucularin cevap verebilmesi icin net bir soru cumlesi ekleyin.';
        }

        if (preg_match_all('/https?:\/\/|www\./i', $plain) > 1) {
            $suggestions[] = 'Coklu link varsa her linkin neden gerekli oldugunu kisaca aciklayin.';
        }

        return $suggestions ?: ['Baslik ve icerik forum icin yeterince anlasilir gorunuyor.'];
    }

    private function replyWritingSuggestions(ForumTopic $topic, string $plain): array
    {
        $suggestions = [];

        if ($this->wordCount($plain) < 12) {
            $suggestions[] = 'Cevabi biraz daha gerekcelendirin; tek cumlelik yanitlar moderasyonda zayif gorunebilir.';
        }

        if (! Str::contains(Str::lower($plain), $this->keywords($topic->title)->take(3)->all())) {
            $suggestions[] = 'Cevabinizin konuyla baglantisini guclendirmek icin basliktaki ana kavramlardan birini kullanin.';
        }

        return $suggestions ?: ['Cevap konu baglamiyla uyumlu gorunuyor.'];
    }

    private function moderationExplanation(string $text): string
    {
        $signals = $this->riskSignals($text);

        return $signals->isEmpty()
            ? 'Local kontrol belirgin spam, hakaret veya reklam sinyali bulmadi.'
            : 'Local kontrol su sinyalleri buldu: ' . $signals->implode(', ') . '.';
    }

    private function moderatorRiskSummary(string $text): string
    {
        $signals = $this->riskSignals($text);

        if ($signals->isEmpty()) {
            return 'Belirgin ek local risk sinyali yok.';
        }

        return $signals->map(fn ($signal) => Str::headline($signal))->implode('; ');
    }

    private function riskSignals(string $text): Collection
    {
        $lower = Str::lower($this->plainText($text));
        $signals = collect();

        if (preg_match_all('/https?:\/\/|www\./i', $lower) >= 2) {
            $signals->push('coklu link');
        }

        if (Str::contains($lower, ['aptal', 'salak', 'hakaret', 'nefret', 'tehdit'])) {
            $signals->push('hakaret veya toksik ifade');
        }

        if (Str::contains($lower, ['satilik', 'reklam', 'kampanya', 'whatsapp', 'telegram', 't.me/', 'wa.me/'])) {
            $signals->push('reklam veya dis iletisim yonlendirmesi');
        }

        if (preg_match('/(.)\1{7,}/u', $lower)) {
            $signals->push('tekrarlayan karakter');
        }

        return $signals->unique()->values();
    }

    private function keywords(string $text): Collection
    {
        return collect(preg_split('/[^\pL\pN]+/u', Str::lower($this->plainText($text))) ?: [])
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => Str::length($word) >= 4 && ! in_array($word, self::STOP_WORDS, true))
            ->countBy()
            ->sortDesc()
            ->keys()
            ->values();
    }

    private function plainText(string $content): string
    {
        return Str::of(strip_tags($content))
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();
    }

    private function wordCount(string $text): int
    {
        return count(preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY) ?: []);
    }
}
