<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Permission\Models\Role;

class AccountApplication extends Model
{
    use HasFactory;
    use HasStatuses;

    protected $fillable = ['role_id', 'user_id'];
    protected $accountStatuses = ['approved', 'rejected', 'under review', 'user action required'];

    /**
     * Get the user that owns the AccountApplication.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role that owns the AccountApplication.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Possible account application statuses.
     */
    public function getAllStatuses(): Collection
    {
        return collect($this->accountStatuses);
    }
}
