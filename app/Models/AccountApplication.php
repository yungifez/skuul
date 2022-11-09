<?php

namespace App\Models;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountApplication extends Model
{
    use HasFactory, HasStatuses;

    protected $fillable = ['role_id', 'user_id'];

    /**
     * Get the user that owns the AccountApplication
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role that owns the AccountApplication
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Possible account application statuses
     *
     * @return void
     */
    public function getAllStatuses()
    {
        return collect(['approved', 'rejected', 'under review', 'user action required']);
    }
}
