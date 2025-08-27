<?php

namespace App\Services;

use App\Notifications\ReactionMadeNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class ReactionService
{
    public function toggleReaction(Model $reactable, $user)
    {
        // Gate::authorize('react', $reactable);

        $existing = $reactable->reactions()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            return [
                'reacted' => false,
                'reactions_count' => $reactable->reactions()->count(),
            ];
        }

        $reactable->reactions()->create(['user_id' => $user->id]);

        $reactable->user->notify(new ReactionMadeNotification($reactable, $user));

        return [
            'reacted' => true,
            'reactions_count' => $reactable->reactions()->count(),
        ];
    }
}
