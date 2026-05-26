<?php

namespace App\Support;

class SafetyAssessment
{
    public function __construct(
        public readonly int $score,
        public readonly string $label,
        public readonly array $reasons,
        public readonly int $trustScore,
    ) {}

    public function requiresReview(): bool
    {
        return $this->score >= 40;
    }

    public function shouldReject(): bool
    {
        return $this->score >= 90;
    }

    public function attributes(): array
    {
        return [
            'ai_risk_score' => $this->score,
            'ai_risk_label' => $this->label,
            'ai_risk_reasons' => $this->reasons,
            'ai_review_required' => $this->requiresReview(),
        ];
    }
}
