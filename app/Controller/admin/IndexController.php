<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

use function Hyperf\Config\config;

class IndexController extends Controller
{
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->raw('Hello Hyperf!');
    }

    public function settings()
    {
        $localeOptions = Admin::config('admin.layout.locale_options') ?? [
            'en'    => 'English',
            'zh_CN' => '简体中文',
        ];

        $locale = settings()->getByModule('admin_locale', 'zh_CN');

        if ($locale == 'null') {
            $locale = 'zh_CN';
        }

        return $this->success([
            'nav'      => Admin::getNav(),
            'assets'   => Admin::getAssets(),
            'app_name' => Admin::config('admin.name'),
            'locale'   => $locale,
            'layout'   => Admin::config('admin.layout'),
            'logo'     => url(Admin::config('admin.logo')),

            'login_captcha'          => Admin::config('admin.auth.login_captcha'),
            'locale_options'         => map2options($localeOptions),
            'show_development_tools' => Admin::config('admin.show_development_tools'),
            'system_theme_setting'   => settings()->getByModule('system_theme_setting'),
            // 'enabled_extensions'     => Extension::query()->where('is_enabled', 1)->pluck('name')?->toArray(),
            'enabled_extensions' => false,
        ]);
    }

    public function saveSettings()
    {
        $data          = $this->request->all();
        $currentModule = Admin::currentModule(true);

        $distinguishingModule = ['system_theme_setting', 'admin_locale'];
        foreach ($data as $key => $value) {
            if (in_array($key, $distinguishingModule) && $currentModule) {
                $data[$currentModule . '_' . $key] = $value;
                unset($data[$key]);
            }
        }

        Admin::setting()->setMany($data);

        return $this->success();
    }

    // public function downloadExport()
    // {
    //     $path = $this->request->input('path');

    //     try {
    //         Storage::exists($path);
    //     } catch (\Throwable $e) {
    //         abort(404);
    //     }

    //     $path = storage_path('app/' . $path);

    //     if (!file_exists($path)) abort(404);

    //     return response()->download($path)->deleteFileAfterSend();
    // }

    /**
     * 图标搜索
     *
     */
    public function iconifySearch()
    {
        $query = $this->request->input('query', 'home');

        $icons = file_get_contents(owl_admin_path('/Support/iconify.json'));
        $icons = json_decode($icons, true);

        $items = [];
        foreach ($icons as $item) {
            if (str_contains($item, $query)) {
                $items[] = ['icon' => $item];
            }
            if (count($items) > 999) {
                break;
            }
        }

        $total = count($items);

        return $this->success(compact('items', 'total'));
    }

    /**
     * 获取页面结构
     *
     * @return JsonResponse|JsonResource
     */
    // public function pageSchema()
    // {
    //     return $this->success(AdminPageService::make()->get(request('sign')));
    // }
}
