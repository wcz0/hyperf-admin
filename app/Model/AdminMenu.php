<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id
 * @property int $parent_id
 * @property int $custom_order
 * @property string $title
 * @property string $icon
 * @property string $url
 * @property int $url_type
 * @property int $visible
 * @property int $is_home
 * @property int $keep_alive
 * @property string $iframe_url
 * @property string $component
 * @property int $is_full
 * @property string $extension
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class AdminMenu extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin_menus';

    /**
     * The attributes that are mass assignable.
     */
    protected array $guarded = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'custom_order' => 'integer', 'url_type' => 'integer', 'visible' => 'integer', 'is_home' => 'integer', 'keep_alive' => 'integer', 'is_full' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
