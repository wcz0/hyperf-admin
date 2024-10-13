<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class AuthController extends Controller
{

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        return $response->raw('Hello Hyperf!');
    }


    public function logout()
    {

    }

    public function login()
    {
        if (Admin::config('admin.auth.login_captcha')) {
            if (!$this->request->has('captcha')) {
                return $this->fail(admin_trans('admin.required', ['attribute' => admin_trans('admin.captcha')]));
            }

            // if (strtolower(cache()->pull($this->request->input('sys_captcha'))) != strtolower($this->request->input('captcha'))) {
            //     return $this->fail(admin_trans('admin.captcha_error'));
            // }
        }

        try {
            $validator = $this->validationFactory->make($this->request->all(), [
                'username' => 'required',
                'password' => 'required',
            ], [
                'username.required' => admin_trans('admin.required', ['attribute' => admin_trans('admin.username')]),
                'password.required' => admin_trans('admin.required', ['attribute' => admin_trans('admin.password')]),
            ]);

            if ($validator->fails()) {
                throw new HttpException(400, $validator->errors()->first());
            }

            $user = Admin::adminUserModel()::query()->where('username', $this->request->input('username'))->first();

            if ($user && Hash::check($this->request->input('password'), $user->password)) {
                if (!$user->enabled) {
                    return $this->fail(admin_trans('admin.user_disabled'));
                }

                $module = Admin::currentModule(true);
                $prefix = $module ? $module . '.' : '';
                $token  = $user->createToken($prefix . 'admin')->plainTextToken;

                return $this->success(compact('token'), admin_trans('admin.login_successful'));
            }

            // abort(Response::HTTP_BAD_REQUEST, admin_trans('admin.login_failed'));
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function loginPage()
    {
        $form = amis()->Form()
            ->panelClassName('border-none')
            ->id('login-form')
            ->title()
            ->api(admin_url('/login'))
            ->initApi('/no-content')
            ->body([
                amis()->TextControl()->name('username')->placeholder(admin_trans('admin.username'))->required(),
                amis()
                    ->TextControl()
                    ->type('input-password')
                    ->name('password')
                    ->placeholder(admin_trans('admin.password'))
                    ->required(),
                amis()->InputGroupControl('captcha_group')->body([
                    amis()->TextControl('captcha', admin_trans('admin.captcha'))->placeholder(admin_trans('admin.captcha'))->required(),
                    amis()->HiddenControl()->name('sys_captcha'),
                    amis()->Service()->id('captcha-service')->api('get:' . admin_url('/captcha'))->body(
                        amis()->Image()
                            ->src('${captcha_img}')
                            ->height('1.917rem')
                            ->className('p-0 captcha-box')
                            ->imageClassName('rounded-r')
                            ->set(
                                'clickAction',
                                ['actionType' => 'reload', 'target' => 'captcha-service']
                            )
                    ),
                ])->visibleOn('${!!login_captcha}'),
                amis()->CheckboxControl()->name('remember_me')->option(admin_trans('admin.remember_me'))->value(true),

                // 登录按钮
                amis()->VanillaAction()
                    ->actionType('submit')
                    ->label(admin_trans('admin.login'))
                    ->level('primary')
                    ->className('w-full'),
            ])
            // 清空默认的提交按钮
            ->actions([])
            ->onEvent([
                // 页面初始化事件
                'inited'     => [
                    'actions' => [
                        // 读取本地存储的登录参数
                        [
                            'actionType' => 'custom',
                            'script'     => <<<JS
let loginParams = localStorage.getItem(window.\$owl.getCacheKey('loginParams'))
if(loginParams){
    loginParams = JSON.parse(decodeURIComponent(window.atob(loginParams)))
    doAction({
        actionType: 'setValue',
        componentId: 'login-form',
        args: { value: loginParams }
    })
}
JS
                            ,

                        ],
                    ],
                ],
                // 登录成功事件
                'submitSucc' => [
                    'actions' => [
                        // 保存登录参数到本地, 并跳转到首页
                        [
                            'actionType' => 'custom',
                            'script'     => <<<JS
let _data = {}
if(event.data.remember_me){
    _data = { username: event.data.username, password: event.data.password }
}
window.\$owl.afterLoginSuccess(_data, event.data.result.data.token)
JS,

                        ],
                    ],
                ],

                // 登录失败事件
                'submitFail' => [
                    'actions' => [
                        // 刷新验证码外层Service
                        ['actionType' => 'reload', 'componentId' => 'captcha-service'],
                    ],
                ],
            ]);

        $card = amis()->Card()->className('w-96 m:w-full')->body([
            amis()->Service()->api('/_settings')->body([
                amis()->Flex()->justify('space-between')->className('px-2.5 pb-2.5')->items([
                    amis()->Image()->src('${logo}')->width(40)->height(40),
                    amis()->Tpl()
                        ->className('font-medium')
                        ->tpl('<div style="font-size: 24px">${app_name}</div>'),
                ]),
                $form,
            ]),
        ]);

        return amis()->Page()->className('login-bg')->css([
            '.captcha-box .cxd-Image--thumb' => [
                'padding' => '0',
                'cursor'  => 'pointer',
                'border'  => 'var(--Form-input-borderWidth) solid var(--Form-input-borderColor)',

                'border-top-right-radius'    => '4px',
                'border-bottom-right-radius' => '4px',
            ],
            '.cxd-Image-thumb'               => ['width' => 'auto'],
            '.login-bg'                      => [
                'background' => 'var(--owl-body-bg)',
            ],
        ])->body(
            amis()->Wrapper()->className("h-screen w-full flex items-center justify-center")->body($card)
        );
    }
}
