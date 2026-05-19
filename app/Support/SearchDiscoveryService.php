<?php

namespace App\Support;

use App\Models\Announcement;
use App\Models\ForumPost;
use App\Models\ForumTag;
use App\Models\ForumTopic;
use App\Models\News;
use App\Models\SearchQuery;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SearchDiscoveryService
{
    public function search(string $query, ?string $type = null, int $limit = 8, bool $recordQuery = true): array
    {
        $query = $this->cleanQuery($query);
        $tokens = $this->tokens($query);
        $type = $this->allowedType($type);

        if ($recordQuery && $query !== '') {
            SearchQuery::record($query, ['source' => 'public_search']);
        }

        $sections = [
            'news' => $type && $type !== 'news' ? collect() : $this->news($query, $tokens, $limit),
            'announcements' => $type && $type !== 'announcements' ? collect() : $this->announcements($query, $tokens, $limit),
            'forum_topics' => $type && $type !== 'forum_topics' ? collect() : $this->forumTopics($query, $tokens, $limit),
            'forum_posts' => $type && $type !== 'forum_posts' ? collect() : $this->forumPosts($query, $tokens, $limit),
            'users' => $type && $type !== 'users' ? collect() : $this->users($query, $tokens, $limit),
            'tags' => $type && $type !== 'tags' ? collect() : $this->tags($query, $tokens, $limit),
        ];

        return [
            'query' => $query,
            'type' => $type,
            'sections' => $sections,
            'results' => $this->flatten($sections)->sortByDesc('score')->values(),
            'suggestions' => $this->suggestions($query),
            'trending' => $this->trending(),
        ];
    }

    public function instant(string $query, int $limit = 6): array
    {
        $data = $this->search($query, null, $limit, false);

        return [
            'query' => $data['query'],
            'suggestions' => $data['suggestions'],
            'trending' => $data['trending']->take(5)->values(),
            'results' => $data['results']
                ->take(12)
                ->map(fn (array $result) => collect($result)->only([
                    'type',
                    'type_label',
                    'title',
                    'excerpt',
                    'url',
                    'meta',
                    'score',
                ])->all())
                ->values(),
        ];
    }

    private function news(string $query, array $tokens, int $limit): Collection
    {
        return News::query()
            ->with('category')
            ->when($query !== '', fn (Builder $builder) => $this->whereLike($builder, ['title', 'summary', 'content'], $query, $tokens))
            ->latest()
            ->limit($limit * 3)
            ->get()
            ->map(fn (News $news) => $this->result(
                type: 'news',
                label: 'Haber',
                title: $news->title,
                excerpt: $news->summary ?: Str::limit(strip_tags($news->content), 150),
                url: url('/haber/'.$news->slug),
                meta: $news->category?->name ?: 'Haber',
                haystack: [$news->title, $news->summary, $news->content],
                query: $query,
            ))
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    private function announcements(string $query, array $tokens, int $limit): Collection
    {
        return Announcement::query()
            ->where('is_active', true)
            ->when($query !== '', fn (Builder $builder) => $this->whereLike($builder, ['title', 'summary', 'content', 'institution', 'city', 'category'], $query, $tokens))
            ->latest()
            ->limit($limit * 3)
            ->get()
            ->map(fn (Announcement $announcement) => $this->result(
                type: 'announcements',
                label: 'İlan',
                title: $announcement->title,
                excerpt: $announcement->summary ?: Str::limit(strip_tags($announcement->content), 150),
                url: url('/ilan/'.$announcement->slug),
                meta: collect([$announcement->city, $announcement->institution])->filter()->implode(' - ') ?: 'İlan',
                haystack: [$announcement->title, $announcement->summary, $announcement->content, $announcement->institution, $announcement->city],
                query: $query,
            ))
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    private function forumTopics(string $query, array $tokens, int $limit): Collection
    {
        return ForumTopic::published()
            ->with(['category', 'tags'])
            ->withCount(['posts' => fn ($builder) => $builder->where('status', 'approved')])
            ->when($query !== '', function (Builder $builder) use ($query, $tokens) {
                $builder->where(function (Builder $inner) use ($query, $tokens) {
                    $this->whereLike($inner, ['title', 'content'], $query, $tokens);
                    $inner->orWhereHas('tags', fn (Builder $tagQuery) => $this->whereLike($tagQuery, ['name'], $query, $tokens));
                });
            })
            ->activeOrder()
            ->limit($limit * 3)
            ->get()
            ->map(fn (ForumTopic $topic) => $this->result(
                type: 'forum_topics',
                label: 'Forum Konusu',
                title: $topic->title,
                excerpt: Str::limit(strip_tags($topic->content), 150),
                url: route('forum.topics.show', $topic->slug),
                meta: $topic->category?->name ?: 'Forum',
                haystack: [$topic->title, $topic->content, $topic->tags->pluck('name')->implode(' ')],
                query: $query,
                extra: ['posts_count' => $topic->posts_count],
            ))
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    private function forumPosts(string $query, array $tokens, int $limit): Collection
    {
        return ForumPost::query()
            ->where('status', 'approved')
            ->whereHas('topic', fn (Builder $builder) => $builder->published())
            ->with(['topic.category', 'user:id,name'])
            ->when($query !== '', fn (Builder $builder) => $this->whereLike($builder, ['content'], $query, $tokens))
            ->latest()
            ->limit($limit * 3)
            ->get()
            ->map(fn (ForumPost $post) => $this->result(
                type: 'forum_posts',
                label: 'Forum Cevabı',
                title: $post->topic?->title ?: 'Forum cevabı',
                excerpt: Str::limit(strip_tags($post->content), 160),
                url: $post->topic ? route('forum.topics.show', $post->topic->slug).'#post-'.$post->id : route('forum.index'),
                meta: $post->user?->name ? 'Yanıtlayan: '.$post->user->name : 'Forum cevabı',
                haystack: [$post->content, $post->topic?->title],
                query: $query,
            ))
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    private function users(string $query, array $tokens, int $limit): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->when($query !== '', fn (Builder $builder) => $this->whereLike($builder, ['name', 'bio'], $query, $tokens))
            ->orderByDesc('forum_reputation')
            ->limit($limit * 3)
            ->get(['id', 'name', 'bio', 'forum_reputation', 'forum_level'])
            ->map(fn (User $user) => $this->result(
                type: 'users',
                label: 'Kullanıcı',
                title: $user->name,
                excerpt: $user->bio ? Str::limit($user->bio, 140) : 'Forum üyesi',
                url: url('/profil/'.$user->id),
                meta: (int) $user->forum_reputation.' reputation',
                haystack: [$user->name, $user->bio],
                query: $query,
            ))
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    private function tags(string $query, array $tokens, int $limit): Collection
    {
        return ForumTag::active()
            ->withCount(['topics' => fn (Builder $builder) => $builder->published()])
            ->when($query !== '', fn (Builder $builder) => $this->whereLike($builder, ['name', 'slug'], $query, $tokens))
            ->orderByDesc('topics_count')
            ->limit($limit * 3)
            ->get()
            ->map(fn (ForumTag $tag) => $this->result(
                type: 'tags',
                label: 'Etiket',
                title: '#'.$tag->name,
                excerpt: $tag->topics_count.' public forum konusu',
                url: route('forum.tags.show', $tag->slug),
                meta: 'Forum etiketi',
                haystack: [$tag->name, $tag->slug],
                query: $query,
            ))
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    private function whereLike(Builder $builder, array $columns, string $query, array $tokens): Builder
    {
        return $builder->where(function (Builder $inner) use ($columns, $query, $tokens) {
            foreach ($columns as $column) {
                $inner->orWhere($column, 'like', '%'.$this->escapeLike($query).'%');

                foreach ($tokens as $token) {
                    $inner->orWhere($column, 'like', '%'.$this->escapeLike($token).'%');
                }
            }
        });
    }

    private function result(
        string $type,
        string $label,
        string $title,
        string $excerpt,
        string $url,
        string $meta,
        array $haystack,
        string $query,
        array $extra = []
    ): array {
        return array_merge([
            'type' => $type,
            'type_label' => $label,
            'title' => $title,
            'excerpt' => trim(strip_tags($excerpt)),
            'url' => $url,
            'meta' => $meta,
            'score' => $this->score($query, $haystack),
        ], $extra);
    }

    private function score(string $query, array $haystack): int
    {
        if ($query === '') {
            return 1;
        }

        $needle = SearchQuery::normalize($query);
        $score = 0;

        foreach ($haystack as $value) {
            $text = SearchQuery::normalize((string) $value);

            if ($text === '') {
                continue;
            }

            if ($text === $needle) {
                $score += 100;
            } elseif (str_contains($text, $needle)) {
                $score += 50;
            }

            foreach ($this->tokens($needle) as $token) {
                if (str_contains($text, $token)) {
                    $score += 12;
                } elseif (mb_strlen($token) >= 4 && levenshtein($token, mb_substr($text, 0, 60)) <= 2) {
                    $score += 6;
                }
            }
        }

        return max($score, 1);
    }

    private function suggestions(string $query): Collection
    {
        $normalized = SearchQuery::normalize($query);

        $trending = $this->trending();

        if ($normalized === '') {
            return $trending->take(6)->values();
        }

        return $trending
            ->filter(function (array $item) use ($normalized) {
                $candidate = SearchQuery::normalize($item['query']);

                return str_contains($candidate, $normalized)
                    || str_contains($normalized, $candidate)
                    || (mb_strlen($normalized) >= 4 && levenshtein($normalized, $candidate) <= 2);
            })
            ->take(6)
            ->values();
    }

    private function trending(): Collection
    {
        return SearchQuery::query()
            ->orderByDesc('hits')
            ->orderByDesc('last_searched_at')
            ->limit(10)
            ->get(['query', 'hits'])
            ->map(fn (SearchQuery $query) => [
                'query' => $query->query,
                'hits' => $query->hits,
                'url' => route('search', ['q' => $query->query]),
            ]);
    }

    private function flatten(array $sections): Collection
    {
        return collect($sections)->flatMap(fn (Collection $section) => $section);
    }

    private function tokens(string $query): array
    {
        return str($query)
            ->lower()
            ->squish()
            ->explode(' ')
            ->filter(fn (string $token) => mb_strlen($token) >= 2)
            ->take(5)
            ->values()
            ->all();
    }

    private function cleanQuery(?string $query): string
    {
        return str((string) $query)
            ->replaceMatches('/[^\pL\pN\s#\-_\.]/u', ' ')
            ->squish()
            ->limit(120, '')
            ->toString();
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '%_\\');
    }

    private function allowedType(?string $type): ?string
    {
        return in_array($type, ['news', 'announcements', 'forum_topics', 'forum_posts', 'users', 'tags'], true)
            ? $type
            : null;
    }
}
