<?php

namespace App\Traits;

use App\Models\Reaction;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasReactions
{
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function reactedByAuthUser(): HasOne
    {
        return $this->hasOne(Reaction::class, 'reactable_id')
            ->where('user_id', auth()->id())
            ->where('reactable_type', static::class);
    }
}
// This trait can be used in models that support reactions, such as Post and Comment.
// It provides methods to get all reactions and check if the authenticated user has reacted to the model
