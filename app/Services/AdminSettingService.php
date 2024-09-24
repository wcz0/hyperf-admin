<?php

namespace App\Services;

use App\Admin;
use App\Model\AdminSetting;
use Hyperf\Collection\Arr;
use Hyperf\Context\ApplicationContext;
use Hyperf\DbConnection\Db;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

class AdminSettingService extends AdminService
{
    protected string $modelName = AdminSetting::class;

    protected string $cacheKeyPrefix = 'app_setting_';

    /**
     * 保存设置
     *
     * @param $key
     * @param $value
     *
     * @return bool
     */
    public function set($key, $value = null)
    {
        try {
            $setting = $this->query()->firstOrNew(['key' => $key]);

            $setting->values = $value;
            $this->clearCache($key);
            $setting->save();
        } catch (\Exception $e) {
            amis_abort($e->getMessage());
        }

        return true;
    }

    /**
     * 批量保存设置
     *
     * @param array $data
     *
     * @return bool
     */
    public function setMany(array $data)
    {
        Db::beginTransaction();
        try {
            foreach ($data as $key => $value) {
                if (!$this->set($key, $value)) {
                    throw new \Exception($this->getError());
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            amis_abort($e->getMessage());
        }

        return true;
    }

    /**
     * 批量保存设置项并返回后台响应格式数据
     *
     * @param array $data
     *
     * @return ResponseInterface
     */
    public function adminSetMany(array $data): ResponseInterface
    {
        $prefix = admin_trans('admin.save');

        if ($this->setMany($data)) {
            return Admin::response()->successMessage($prefix . admin_trans('admin.successfully'));
        }

        return Admin::response()->fail($prefix . admin_trans('admin.failed'), $this->getError());
    }

    /**
     * 以数组形式返回所有设置
     *
     * @return array
     */
    public function all()
    {
        return $this->query()->pluck('values', 'key')->toArray();
    }

    /**
     * 获取设置项
     *
     * @param string     $key     设置项key
     * @param mixed|null $default 默认值
     * @param bool       $fresh   是否直接从数据库获取
     *
     * @return mixed|null
     */
    public function get(string $key, mixed $default = null, bool $fresh = false)
    {
        if ($fresh) {
            return $this->query()->where('key', $key)->value('values') ?? $default;
        }
        $cache = ApplicationContext::getContainer()->get(CacheInterface::class);

        if ($cache->has($this->getCacheKey($key))){
            $value = $this->query()->where('key', $key)->value('values');
            $cache->set($key, $value, 0); // 0 表示永久缓存
        };

        return $value ?? $default;
    }

    /**
     * 获取模块设置项
     *
     * @param string     $key
     * @param mixed|null $default
     * @param bool       $fresh
     *
     * @return mixed|null
     */
    public function getByModule(string $key, mixed $default = null, bool $fresh = false)
    {
        $module = Admin::currentModule(true);
        $prefix = $module ? $module . '_' : '';

        return $this->get($prefix . $key, $default, $fresh);
    }

    /**
     * 获取设置项中的某个值
     *
     * @param string $key  设置项key
     * @param string $path 通过点号分隔的路径, 同Arr::get()
     * @param        $default
     *
     * @return array|\ArrayAccess|mixed|null
     */
    public function arrayGet(string $key, string $path, $default = null)
    {
        $value = $this->get($key);

        if (is_array($value)) {
            return Arr::get($value, $path, $default);
        }

        return $default;
    }

    /**
     * 清除指定设置项
     *
     * @param string $key
     *
     * @return bool
     */
    public function del(string $key)
    {
        if ($this->query()->where('key', $key)->delete()) {
            $this->clearCache($key);

            return true;
        }

        return false;
    }

    /**
     * 清除指定设置项的缓存
     *
     * @param $key
     *
     * @return void
     */
    public function clearCache($key)
    {
        $cache = ApplicationContext::getContainer()->get(CacheInterface::class);
        $cache->delete($this->getCacheKey($key));
    }

    public function getCacheKey($key)
    {
        return $this->cacheKeyPrefix . $key;
    }
}
