<?php
namespace app\controllers;

use app\models\Books;
use app\models\Cart;
use yii;
use yii\db\Query;
use yii\rest\Controller;
use yii\web\UploadedFile;
use app\models\Yiicoupons;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use app\models\CouponImageUploadForm;

class CouponApiController extends Controller
{

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
                    'actions' => ['createcoupon', 'getcategories', 'fetchcoupons', 'fetchcouponbyid', 'updatecoupon', 'changecouponstatus'],
                    'roles' => ['admin']
                ],
                [
                    'allow' => true,
                    'actions' => ['fetcheligiblecoupons'],
                    'roles' => ['user']
                ]
            ]
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'createcoupon' => ['POST']
            ]
        ];
        return $behaviors;
    }

    public function actionCreatecoupon()
    {

        $newCoupon = new Yiicoupons();
        $request = yii::$app->getRequest();
        $params = $request->getBodyParams();
        $newCoupon->load($request->post(), '');

        $couponImage = new CouponImageUploadForm();
        $couponImage->imageFile = UploadedFile::getInstanceByName('ImageFile');
        if ($couponImage->upload()) {
            $newCoupon->Image_Path = $couponImage->getImageUrl();
            if ($newCoupon->validate()) {
                $newCoupon->save();
                return $this->asJson(['success' => true]);
            } else {
                return $this->asJson(['success' => false, 'errors' => $newCoupon->errors, 'errorMessage' => 'unable to add coupon']);
            }
        } else {
            return $this->asJson(['success' => false, 'errors' => $couponImage->errors, 'errorMessage' => 'unable to upload coupon image']);
        }
    }

    public function actionGetcategories()
    {
        $query = new Query();
        $query->select('*')->from('categories');
        $categories = $query->all();
        return $this->asJson(['success' => true, 'data' => $categories]);
    }

    public function actionFetchcoupons()
    {
        $query = new Query();
        $query->select(['Yiicoupons.*', 'categories.category_name AS Coupon_Category'])->from('Yiicoupons')->leftJoin('Categories', 'Yiicoupons.Coupon_Category=Categories.id');
        $Coupons = $query->all();
        return $this->asJson(['success' => true, 'data' => $Coupons]);
    }
    public function actionFetcheligiblecoupons()
    {
        $params = yii::$app->getRequest()->getBodyParams();
        $reqBook = Books::findOne($params['id']);


        $query = new Query();
        $query->select(['Yiicoupons.*', 'categories.category_name AS Coupon_Category'])->from('Yiicoupons')->Where(['or', ['Coupon_Category' => $reqBook['Category_Type']], ['Coupon_Category' => 0]])->leftJoin('Categories', 'Yiicoupons.Coupon_Category=Categories.id');
        $Coupons = $query->all();
        return $this->asJson(['success' => true, 'data' => $Coupons, 'book' => $reqBook]);


    }


    public function actionFetchcouponbyid($id)
    {
        $query = new Query();
        $coupon = Yiicoupons::findOne($id);
        return $this->asJson(['success' => true, 'data' => $coupon]);
    }


    public function actionUpdatecoupon($id)
    {

        $coupon = Yiicoupons::findOne($id);
        $request = yii::$app->getRequest();

        $updateData = $request->post();
        $coupon->attributes = $updateData;


        $model = new CouponImageUploadForm();
        $model->imageFile = UploadedFile::getInstanceByName('ImageFile');
        if ($model->imageFile) {
            if ($model->upload()) {
                unlink($coupon->Image_Path);
                $coupon->Image_Path = $model->getImageUrl();

            } else {
                return $this->asJson(['success' => false, 'errorMessage' => 'unable to update the coupon', 'error' => $model->errors]);
            }
        }
        if ($coupon->validate() && $coupon->save()) {
            return $this->asJson(['success' => true, 'successMessage' => 'successfully updated the coupon']);
        } else {
            return $this->asJson(['success' => false, 'errorMessage' => 'unable to update the coupon', 'error' => $model->errors]);
        }
    }

    public function actionChangecouponstatus()
    {
        $params = yii::$app->getRequest()->getBodyParams();
        $reqCoupon = Yiicoupons::findOne($params['id']);
        if ($reqCoupon) {
            if ($reqCoupon['Coupon_Status'] == 0) {
                $reqCoupon['Coupon_Status'] = 1;
            } else {
                $reqCoupon['Coupon_Status'] = 0;
            }
            $result = $reqCoupon->save();
            return $this->asJson(['success' => true, 'successMessage' => 'status change success', 'status' => $reqCoupon['Coupon_Status'], 'result' => $result, 'errors' => $reqCoupon->errors]);
        } else {
            return $this->asJson(['success' => false, 'errorMessage' => 'Unable to find that coupon']);
        }
    }

}


// $user=yii::$app->user->identity;
//         $userCart=Cart::findAll(['User_Id'=>$user['id']]);
//         $couponAlreadyApplied=false;
//         foreach($userCart as $item){
//             if($item['Applied_Coupon_Id']){
//                 $couponAlreadyApplied=true;
//             }
//         }
//         if( $couponAlreadyApplied){
//             return $this->asJson(['success'=>false,'errorMessage'=>'you have already applied a coupon']);
//         }