<?php

/*
 * This file is part of nomiscz/flarum-ext-auth-wechat.
 *
 * Copyright (c) 2021 NomisCZ.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace HamZone\QQAuth;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\Extend;
use HamZone\QQAuth\Http\Controllers\QQAuthController;
use HamZone\QQAuth\Api\Controllers\QQLinkController;
use HamZone\QQAuth\Api\Controllers\QQUnlinkController;

use FoF\Components\Extend\AddFofComponents;

return [
    new AddFofComponents(),

    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less'),

    new Extend\Locales(__DIR__ . '/resources/locale'),

    (new Extend\Routes('forum'))
        ->get('/auth/qq', 'auth.qq', QQAuthController::class),

    (new Extend\Routes('api'))
        ->get('/auth/qq/link', 'auth.qq.api.link', QQLinkController::class)
        ->post('/auth/qq/unlink', 'auth.qq.api.unlink', QQUnlinkController::class),

    (new Extend\ApiSerializer(UserSerializer::class))
        ->attributes(function($serializer, $user, $attributes) {

            $loginProviders = $user->loginProviders();
            $steamProvider = $loginProviders->where('provider', 'qq')->first();

            $attributes['QQAuth'] = [
                'isLinked' => $steamProvider !== null,
                'identifier' => null, // Hidden, don't expose this information
                'providersCount' => $loginProviders->count()
            ];

            return $attributes;
        }),
];
