<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property string $key 
 * @property string $values 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class AdminSetting extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin_settings';

    /**
     * The attributes that are mass assignable.
     */
    protected array $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];
}
