<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = ['user_id', 'title', 'content', 'is_draft', 'published_at'];

    protected function casts(): array
    {
        return [
            'is_draft' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    // implemenet method scope where post active
    public function scopeActive($query)
    {
        return $query->where('is_draft', false)
            ->where(function ($q) {
                $q->where('published_at', '<=', now())
                    ->orWhereNull('published_at');
            });
    }

    // relation
    public function user(): Relation
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
