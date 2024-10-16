<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ai/encryption.
 *
 * @link     https://github.com/hyperf-ai/encryption
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ai/encryption/blob/master/LICENSE
 */

use function Hyperf\Support\env;

return [
    'default' => 'aes',

    'driver' => [
        'aes' => [
            'class' => \HyperfAi\Encryption\Driver\AesDriver::class,
            'options' => [
                'key' => env('AES_KEY', 'hyperfaihyperfaihyperf'),
                'cipher' => env('AES_CIPHER', 'AES-128-CBC'),
            ],
        ],
    ],
];
