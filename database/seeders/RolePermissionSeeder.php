<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public const GUARD = 'web';

    public function run(): void
    {
        foreach (User::ROLE_NAMES as $name) {
            Role::findOrCreate($name, self::GUARD);
        }

        $this->syncUsersFromRoleColumn();
    }

    /**
     * Align Spatie roles with existing `users.role` (additive; does not change the column).
     */
    public function syncUsersFromRoleColumn(): void
    {
        User::query()->chunkById(100, function ($users) {
            foreach ($users as $user) {
                if (! $user->role) {
                    continue;
                }
                if (! in_array($user->role, User::ROLE_NAMES, true)) {
                    continue;
                }
                $user->syncRoles([$user->role]);
            }
        });
    }
}
