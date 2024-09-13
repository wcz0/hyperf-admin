<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $username
 * @property string $password
 * @property int $enabled
 * @property string $name
 * @property string $avatar
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class AdminUser extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin_users';

    /**
     * The attributes that are mass assignable.
     */
    protected array $guarded  = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'enabled' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
