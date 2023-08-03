<?php
namespace app\controllers;


use yii;
use yii\db\Query;
use yii\rest\Controller;
use app\models\Cart;
use app\models\Books;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class CartApiController extends Controller{
    public function behaviors()
    {
         $behaviors = parent::behaviors();

        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions'=>['changequantity'],
                    'roles' => ['user']
                ],
            ]
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'changequantity'=>['POST']
            ]
        ];
        return $behaviors;
    }

    
}