<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU', // <- здесь!
    'components' => [
        'user' => [
            'identityClass' => 'budyaga\users\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/login'],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'vkontakte' => [
                    'class' => 'budyaga\users\components\oauth\VKontakte',
                    'clientId' => '5630511',
                    'clientSecret' => 'TmzlAUpTTUMkUUMvOs5U',
                    'scope' => 'email'
                ],
                'facebook' => [
                    'class' => 'budyaga\users\components\oauth\Facebook',
                    'clientId' => 'XXX',
                    'clientSecret' => 'XXX',
                ],
                /*
            'google' => [
                'class' => 'budyaga\users\components\oauth\Google',
                'clientId' => 'XXX',
                'clientSecret' => 'XXX',
            ],

            'github' => [
                'class' => 'budyaga\users\components\oauth\GitHub',
                'clientId' => 'XXX',
                'clientSecret' => 'XXX',
                'scope' => 'user:email, user'
            ],
            'linkedin' => [
                'class' => 'budyaga\users\components\oauth\LinkedIn',
                'clientId' => 'XXX',
                'clientSecret' => 'XXX',
            ],
            'live' => [
                'class' => 'budyaga\users\components\oauth\Live',
                'clientId' => 'XXX',
                'clientSecret' => 'XXX',
            ],
            'yandex' => [
                'class' => 'budyaga\users\components\oauth\Yandex',
                'clientId' => 'XXX',
                'clientSecret' => 'XXX',
            ],
            'twitter' => [
                'class' => 'budyaga\users\components\oauth\Twitter',
                'consumerKey' => 'XXX',
                'consumerSecret' => 'XXX',
            ],*/
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/guest/'=>'/site/guest',
                '/signup' => '/user/user/signup',
                '/login' => '/user/user/login',
                '/logout' => '/user/user/logout',
                '/requestPasswordReset' => '/user/user/request-password-reset',
                '/resetPassword' => '/user/user/reset-password',
                '/profile' => '/user/user/profile',
                '/retryConfirmEmail' => '/user/user/retry-confirm-email',
                '/confirmEmail' => '/user/user/confirm-email',
                '/unbind/<id:[\w\-]+>' => '/user/auth/unbind',
                '/oauth/<authclient:[\w\-]+>' => '/user/auth/index'
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'assetManager' => [
            'linkAssets' => true,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'B0MXaGK1BDq3XP1BxCZ_bdedfEBcM3MP',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,//set this property to false to send mails to real email addresses
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'ailont.dekor@gmail.com',
                'password' => '8989898989g',
                'port' => 587,
                'encryption' => 'tls',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),

    ],
    'modules' => [
        'user' => [
            'class' => 'budyaga\users\Module',
            'userPhotoUrl' => '/images/user',
            'userPhotoPath' => '@app/web/images/user',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
