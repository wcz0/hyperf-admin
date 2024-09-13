<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $http_method
 * @property string $http_path
 * @property int $custom_order
 * @property int $parent_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class AdminPermission extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin_permissions';

    /**
     * The attributes that are mass assignable.
     */
    protected array $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'custom_order' => 'integer', 'parent_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
