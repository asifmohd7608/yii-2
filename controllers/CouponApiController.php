<?php
namespace app\controllers;

use yii;
use yii\db\Query;
use yii\rest\Controller;
use yii\web\UploadedFile;
use app\models\Yiicoupons;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use app\models\CouponImageUploadForm;

class CouponApiController extends Controller{

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
                    'actions'=>['createcoupon','getcategories'],
                    'roles' => ['admin']
                ],
            ]
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'createcoupon'=>['POST']
            ]
        ];
        return $behaviors;
    }

    public function actionCreatecoupon(){

        $newCoupon=new Yiicoupons();
        $request=yii::$app->getRequest();
        $params=$request->getBodyParams();
        $newCoupon->load($request->post(),'');

        $couponImage=new CouponImageUploadForm();
        $couponImage->imageFile=UploadedFile::getInstanceByName('ImageFile');
        if($couponImage->upload()){
            $newCoupon->Image_Path=$couponImage->getImageUrl();
            if($newCoupon->validate()){
                $newCoupon->save();
                return $this->asJson(['success'=>true]);
            }else{
            return $this->asJson(['success'=>false,'errors'=>$newCoupon->errors,'errorMessage'=>'unable to add coupon']);
            }
        }else{
            return $this->asJson(['success'=>false,'errors'=>$couponImage->errors,'errorMessage'=>'unable to upload coupon image']);
        }
    }

    public function actionGetcategories()
    {
        $query = new Query();
        $query->select('*')->from('categories');
        $categories = $query->all();
        return $this->asJson(['success' => true, 'data' => $categories]);
    }
    
}