<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'status',
        'ai_risk_score',
        'ai_risk_label',
        'ai_risk_reasons',
        'ai_review_required',
    ];

    protected function casts(): array
    {
        return [
            'ai_risk_score' => 'integer',
            'ai_risk_reasons' => 'array',
            'ai_review_required' => 'boolean',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
