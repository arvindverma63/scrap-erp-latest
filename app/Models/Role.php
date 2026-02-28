<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    protected $fillable = [
        'name', // and guard_name if you want
        'group_id',
        'guard_name',
        'color'
    ];

    /**
     * Relationship to users (through role_users pivot)
     */
  public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_users', 'role_id', 'user_id');
    }
}
