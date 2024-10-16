<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Relations\BelongsTo;

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

    const TYPE_ROUTE  = 1;
    const TYPE_LINK   = 2;
    const TYPE_IFRAME = 3;
    const TYPE_PAGE   = 4;

    public static function getType(): array
    {
        return [
            self::TYPE_ROUTE  => admin_trans('admin.admin_menu.route'),
            self::TYPE_LINK   => admin_trans('admin.admin_menu.link'),
            self::TYPE_IFRAME => admin_trans('admin.admin_menu.iframe'),
            self::TYPE_PAGE   => admin_trans('admin.admin_menu.page'),
        ];
    }

    /**
     * 父级菜单
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function getTitleAttribute()
    {
        $transKey = ($this->extension ? $this->extension . '::' : '') . "menu.{$this->attributes['title']}";
        $translate = admin_trans($transKey);

        return $translate == $transKey ? $this->attributes['title'] : $translate;
    }
}
