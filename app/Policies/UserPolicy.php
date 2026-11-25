<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    use AdminOnlyAbilities;

    public function before(User $user, string $ability): ?bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        return $ability === 'delete' ? null : true;
    }

    public function delete(User $user, mixed $model): bool
    {
        return $model instanceof User && $user->id !== $model->id;
    }
}
