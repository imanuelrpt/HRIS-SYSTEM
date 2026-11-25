<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserManagementService
{
    public function __construct(
        private readonly LeaveBalanceService $leaveBalances,
        private readonly ActivityLogService $activityLog,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $photoPath = $this->storePhoto($data['photo'] ?? null);
            $user = User::query()->create($this->attributes($data, $photoPath));

            $this->leaveBalances->generateForUser($user, now()->year);
            $this->activityLog->log('user.created', "Menambahkan karyawan {$user->name}.", $user);

            return $user;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $photoPath = $this->storePhoto($data['photo'] ?? null, $user->photo_path);
            $user->update($this->attributes($data, $photoPath));

            $this->activityLog->log('user.updated', "Memperbarui data karyawan {$user->name}.", $user);

            return $user;
        });
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->activityLog->log('user.deleted', "Menonaktifkan karyawan {$user->name}.", $user);

            if ($user->photo_path !== null) {
                Storage::disk('public')->delete($user->photo_path);
            }

            $user->delete();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function attributes(array $data, ?string $photoPath): array
    {
        $attributes = Arr::except($data, ['photo', 'password_confirmation']);

        if (array_key_exists('password', $attributes) && blank($attributes['password'])) {
            unset($attributes['password']);
        }

        $attributes['photo_path'] = $photoPath;

        return $attributes;
    }

    private function storePhoto(mixed $photo, ?string $existingPath = null): ?string
    {
        if (! $photo instanceof UploadedFile) {
            return $existingPath;
        }

        if ($existingPath !== null) {
            Storage::disk('public')->delete($existingPath);
        }

        return $photo->store('photos/users', 'public');
    }
}
