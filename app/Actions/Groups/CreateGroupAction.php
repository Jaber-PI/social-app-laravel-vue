<?php

namespace App\Actions\Groups;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CreateGroupAction
{
    public function execute(User $creator, array $data): Group
    {

        $group = Group::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'auto_approval' => $data['auto_approval'] ?? true,
            'slug' => Str::slug($data['name']) . '-' . uniqid(),
            'created_by' => $creator->id,
        ]);

        $group->members()->attach($creator->id, [
            'role' => 'admin',
            'approved_at' => now(),
            'status' => 'approved',
            'added_by' => $creator->id,
        ]);

        return $group;
    }
}
