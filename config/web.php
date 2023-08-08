<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'name' => 'Bookify',
    'timeZone' => 'Asia/Kolkata',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'ZwkL-Yshw5F54bF3ShJrE7uyroYhkE36',
            'parsers' => [
                'multipart/form-data' => 'yii\web\MultipartFormDataParser',
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableSession' => false,
            'loginUrl' => null,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
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
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                'api/books' => 'book-api/index',
                /*fetch all books*/
                'api/user/books' => 'book-api/fetchbooksuser',
                /*fetch all active books for users*/
                'api/books/<id:\d+>' => 'book-api/getbookbyid',
                /*fetch book by id*/
                'api/books/categories' => 'book-api/getcategories',
                /* fetch a;; categories*/
                'api/books/add' => 'book-api/create',
                /*create a new book*/
                'api/books/update/<id:\d+>' => 'book-api/updatebook',
                /* update book by id*/
                'api/books/delete/<id:\d+>' => 'book-api/deletebook',
                /* delete book */
                'api/books/changestatus' => 'book-api/changebookstatus',
                'api/auth/signup/admin' => 'auth/signup',
                'api/auth/login/admin' => 'auth/login',
                'api/auth/signup/user' => 'auth/usersignup',
                'api/auth/login/user' => 'auth/userlogin',
                'api/auth/status' => 'book-api/status',

                // ---------------userapi-------------

                'api/user/addtocart' => 'user-api/addtocart',
                'api/user/getcart' => 'user-api/sendcart',
                'api/user/cart/removeitem' => 'user-api/removeitem',
                'api/user/cart/delete' => 'user-api/deletecart',
                'api/user/cart/changequantity' => 'user-api/changequantity',
                'api/user/cart/applycoupon' => 'user-api/applycoupon',
                'api/user/cart/removecoupon' => 'user-api/removecoupon',
                'api/user/cart/checkout' => 'user-api/checkoutcart',
                'api/user/orders' => 'user-api/getorders',
                'api/user/profile' => 'user-api/getuserdetails',
                'api/user/profile/update' => 'user-api/updateuserdetails',

                // -----------------coupon---------------
                'api/coupons' => 'coupon-api/fetchcoupons',
                'api/coupons/eligible' => 'coupon-api/fetcheligiblecoupons',
                'api/coupons/edit/<id:\d+>' => 'coupon-api/fetchcouponbyid',
                'api/coupons/update/<id:\d+>' => 'coupon-api/updatecoupon',
                'api/coupons/create' => 'coupon-api/createcoupon',
                'api/coupons/categories' => 'coupon-api/getcategories',
                'api/coupons/changestatus' => 'coupon-api/changecouponstatus',

            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;