<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    use HasFactory;
    protected $fillable = [
        'short_code',
        'long_url',
        'user_id',
        'company_id',
        'hits',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shortUrl) {
            if (empty($shortUrl->short_code)) {
                $shortUrl->short_code = Str::random(8);
            }
        });
    }

    /**
     * Get the user that created the short URL.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company that the short URL belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the full short URL.
     */
    public function getShortUrlAttribute(): string
    {
        return url('/s/' . $this->short_code);
    }

    /**
     * Increment hits.
     */
    public function incrementHits(): void
    {
        $this->increment('hits');
    }
}
