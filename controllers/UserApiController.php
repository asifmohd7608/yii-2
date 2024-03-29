<?php

namespace app\controllers;

use yii;
use yii\db\Query;
use app\models\Cart;
use app\models\Books;
use app\models\Users;
use yii\rest\Controller;
use yii\web\UploadedFile;
use app\models\Yiicoupons;
use yii\filters\VerbFilter;
use app\models\Yiipurchases;
use yii\filters\AccessControl;
use app\models\ProfileImageUpload;
use yii\filters\auth\HttpBearerAuth;

class UserApiController extends Controller
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
                    'actions' => ['addtocart', 'sendcart', 'removeitem', 'deletecart', 'changequantity', 'applycoupon', 'removecoupon', 'checkoutcart', 'getorders','getuserdetails','changeprofilepic','updateuserdetails'],
                    'roles' => ['user']
                ],
            ]
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'addtocart' => ['POST'],
                'applycoupon' => ['POST'],
                'sendcart' => ['GET'],
                'removeitem' => ['POST'],
                'deletecart' => ["DELETE"],
                'changequantity' => ['POST'],
                'removecoupon' => ['POST'],
                'checkoutcart' => ['GET'],
                'getorders' => ['GET'],
                'getuserdetails'=>['GET'],
                'updateuserdetails'=>['POST'],
                'changeprofilepic'=>['POST']
            ]
        ];
        return $behaviors;
    }

    public function actionAddtocart()
    {
        $params = yii::$app->request->getBodyParams();
        $query = new Query();
        $bookId = $params['id'];
        $user = yii::$app->user->identity;
        $query->select('*')->from('cart')->where(['Book_Id' => $bookId, 'User_Id' => $user['id']]);
        $book = Books::findOne($bookId);
        if ($book) {
            $reqCart = $query->all();
            if ($reqCart) {
                return $this->asJson(['success' => false, 'errorMsg' => 'already added cart']);
            } else {
                $newCartItem = new Cart();
                $newCartItem['Book_Id'] = $bookId;
                $newCartItem['User_Id'] = $user['id'];
                $newCartItem['Quantity'] = 1;
                $newCartItem['Unit_Price'] = $book['Price'];
                $newCartItem['Total_Price'] = $book['Price'];
                $newCartItem['Amount_Payable'] = $book['Price'];
                $newCartItem['Discount'] = 0;
                if ($newCartItem->save()) {
                    return $this->asJson(['success' => true, 'successMessage' => 'item added to cart']);
                } else {
                    return $this->asJson(['success' => false, 'errorMessage' => 'unable to add to cart']);
                }
            }


        } else {
            return $this->asJson(['success' => false, 'errorMessage' => 'item doesnt exist']);
        }
    }

    public function actionSendcart()
    {
        $user = yii::$app->user->identity;
        $query = new Query();
        $query->select(['cart.*', 'Books.Book_Title AS Book_Title'])->from('cart')->where(['User_Id' => $user['id']])->leftJoin('books', 'cart.Book_Id=books.id');
        $cart = $query->all();
        return $this->asJson(['success' => true, 'cart' => $cart]);
    }

    public function actionRemoveitem()
    {
        $user = yii::$app->user->identity;
        $params = yii::$app->request->getBodyParams();
        $userId = $user['id'];
        $bookId = $params['id'];

        $cartItem = Cart::findOne(['User_Id' => $userId, 'Book_Id' => $bookId]);
        if ($cartItem) {
            $result = $cartItem->delete();
            if ($result) {
                $query = new Query();
                $query->select(['cart.*', 'Books.Book_Title AS Book_Title'])->from('cart')->where(['User_Id' => $user['id']])->leftJoin('books', 'cart.Book_Id=books.id');
                $cart = $query->all();
                return $this->asJson(['success' => true, 'successMessage' => 'successfully deleted cart item', 'cart' => $cart]);
            } else {
                return $this->asJson(['success' => false, 'errorMessage' => 'unable to delete cart item']);
            }
        } else {
            return $this->asJson(['success' => false, 'errorMessage' => 'unable to delete cart item']);
        }
    }
    public function actionDeletecart()
    {
        $user = yii::$app->user->identity;
        $userId = $user['id'];
        $cart = Cart::findAll(['User_Id' => $userId]);
        foreach ($cart as $cartItem) {
            $cartItem->delete();
        }
        return $this->asJson(['success' => true, 'successMessage' => 'successfully deleted cart']);
    }

    public function actionChangequantity()
    {
        $user = yii::$app->user->identity;
        $params = yii::$app->getRequest()->getBodyParams();
        $reqCartItem = Cart::findOne($params['id']);

        if ($reqCartItem) {
            $reqBook = Books::findOne($reqCartItem['Book_Id']);
            if ($reqBook) {
                if ($params['change'] === 'increment') {
                    if ($reqBook['No_Of_Copies_Current'] > $reqCartItem['Quantity']) {
                        $reqCartItem['Quantity'] = $reqCartItem['Quantity'] + 1;
                        $reqCartItem['Total_Price'] = $reqCartItem['Total_Price'] + $reqCartItem['Unit_Price'];
                        $reqCartItem['Amount_Payable'] = $reqCartItem['Amount_Payable'] + $reqCartItem['Unit_Price'];
                        $reqCartItem->save();
                        $query = new Query();
                        $query->select(['cart.*', 'Books.Book_Title AS Book_Title'])->from('cart')
                            ->where(['cart.id' => $params['id']])
                            ->leftJoin('books', 'cart.Book_Id=books.id');
                        $cart = $query->all();
                        return $this->asJson(['success' => true, 'successMessage' => 'quantity increment success', 'data' => $cart[0]]);
                    } else {
                        return $this->asJson(['success' => false, 'errorMessage' => 'only ' . $reqBook['No_Of_Copies_Current'] . ' quantity available']);
                    }
                } else if ($params['change'] === 'decrement') {
                    if ($reqCartItem['Quantity'] > 1) {
                        $reqCartItem['Quantity'] = $reqCartItem['Quantity'] - 1;
                        $reqCartItem['Total_Price'] = $reqCartItem['Total_Price'] - $reqCartItem['Unit_Price'];
                        $reqCartItem['Amount_Payable'] = $reqCartItem['Amount_Payable'] - $reqCartItem['Unit_Price'];
                        $reqCartItem->save();
                        $query = new Query();
                        $query->select(['cart.*', 'Books.Book_Title AS Book_Title'])->from('cart')
                            ->where(['cart.id' => $params['id']])
                            ->leftJoin('books', 'cart.Book_Id=books.id');
                        $cart = $query->all();
                        return $this->asJson(['success' => true, 'successMessage' => 'quantity decrement success', 'data' => $cart[0]]);
                    } else {
                        return $this->asJson(['success' => false, 'errorMessage' => 'cant decrement below 1']);
                    }
                } else {
                    return $this->asJson(['success' => false, 'errorMessage' => 'invalid argument']);
                }
            } else {
                return $this->asJson(['success' => false, 'errorMessage' => 'unable to find that cart item']);
            }
        } else {
            return $this->asJson(['success' => false, 'errorMessage' => 'unable to find that cart item']);
        }
    }

    public function actionApplycoupon()
    {
        $params = yii::$app->getRequest()->getBodyParams();
        $reqCoupon = Yiicoupons::findOne($params['id']);
        $reqBook = Books::findOne($params['bookId']);
        $reqCartItem = Cart::findOne([$params['cartId']]);

        $user = yii::$app->user->identity;
        $userCart = Cart::findAll(['User_Id' => $user['id']]);
        $couponAlreadyApplied = false;
        foreach ($userCart as $item) {
            if ($item['Applied_Coupon_Id']) {
                $couponAlreadyApplied = true;
            }
        }
        if ($couponAlreadyApplied) {
            return $this->asJson(['success' => false, 'errorMessage' => 'you have already applied a coupon']);
        } else {
            if ($reqCoupon && $reqBook && $reqCartItem) {
                if ($reqCartItem['Applied_Coupon_Id']) {
                    return $this->asJson(['success' => false, 'errorMessage' => 'you have already applied a coupon']);
                } else {
                    if ($reqCoupon['Coupon_Type'] === 'Fixed') {
                        if ($reqCartItem['Total_Price'] < $reqCoupon['Coupon_Offer']) {
                            $reqCartItem['Amount_Payable'] = 0;
                            $reqCartItem['Discount'] = $reqCartItem['Total_Price'];
                        } else {
                            $reqCartItem['Amount_Payable'] = $reqCartItem['Amount_Payable'] - $reqCoupon['Coupon_Offer'];
                            $reqCartItem['Discount'] = $reqCoupon['Coupon_Offer'];
                        }
                        $reqCartItem['Applied_Coupon_Id'] = $params['id'];
                    } else if ($reqCoupon['Coupon_Type'] === 'Percentage') {
                        $reqCartItem['Amount_Payable'] = $reqCartItem['Amount_Payable'] - ($reqCartItem['Unit_Price'] *
                            ($reqCoupon['Coupon_Offer'] / 100));
                        $reqCartItem['Discount'] = ($reqCartItem['Unit_Price'] *
                            ($reqCoupon['Coupon_Offer'] / 100));
                        $reqCartItem['Applied_Coupon_Id'] = $params['id'];
                    } else {
                        return $this->asJson(['success' => false, 'errorMessage' => 'unable to apply coupon']);
                    }
                    if ($reqCartItem->save()) {
                        $query = new Query();
                        $query->select(['cart.*', 'Books.Book_Title AS Book_Title'])->from('cart')
                            ->leftJoin('books', 'cart.Book_Id=books.id');
                        $cart = $query->all();
                        return $this->asJson(['success' => true, 'data' => $cart]);
                    } else {
                        return $this->asJson(['success' => false, 'errorMessage' => 'unable to apply coupon']);
                    }
                }
            } else {
                return $this->asJson(['success' => false, 'errorMessage' => 'unable to apply coupon']);
            }
        }


    }
    public function actionRemovecoupon()
    {
        $params = yii::$app->getRequest()->getBodyParams();
        $reqBook = Books::findOne($params['bookId']);
        $reqCartItem = Cart::findOne([$params['cartId']]);

        if ($reqCartItem['Applied_Coupon_Id']) {
            $reqCartItem['Amount_Payable'] = $reqBook['Price'] * $reqCartItem['Quantity'];
            $reqCartItem['Discount'] = 0;
            $reqCartItem['Applied_Coupon_Id'] = null;
            if ($reqCartItem->save()) {
                $query = new Query();
                $query->select(['cart.*', 'Books.Book_Title AS Book_Title'])->from('cart')
                    ->leftJoin('books', 'cart.Book_Id=books.id');
                $cart = $query->all();
                return $this->asJson(['success' => true, 'successMessage' => 'remove coupon success', 'data' => $cart]);
            } else {
                return $this->asJson(['success' => false, 'errorMessage' => 'unable to remove coupon']);
            }
        } else {
            return $this->asJson(['success' => false, 'errorMessage' => 'no coupon to remove']);
        }
    }

    public function actionCheckoutcart()
    {
        $user = yii::$app->user->identity;
        $reqCart = Cart::findAll(['User_Id' => $user['id']]);
        if ($reqCart) {
            foreach ($reqCart as $cart) {
                $newPurchase = new Yiipurchases();
                $newPurchase->User_Id = $user['id'];
                $newPurchase->Coupon_Id = $cart['Applied_Coupon_Id'];
                $newPurchase->Book_Id = $cart['Book_Id'];
                $newPurchase->Quantity = $cart['Quantity'];
                $newPurchase->Unit_Price = $cart['Unit_Price'];
                $newPurchase->Total_Price = $cart['Total_Price'];
                $newPurchase->Amount_Paid = $cart['Amount_Payable'];
                $newPurchase->Discount = $cart['Discount'];
                // $newPurchase->created_at = time();
                // $newPurchase->updated_at = time();

                if ($newPurchase->save()) {
                    $reqBook=Books::findOne($cart['Book_Id']);
                    $reqBook['No_Of_Copies_Current']=$reqBook['No_Of_Copies_Current']-$cart['Quantity'];
                    if($reqBook->save()){
                    $cart->delete();
                    }else{
                        return $this->asJson(['success' => false, 'errorMessage' => 'unable to checkout entire cart']);
                    }
                } else {
                    return $this->asJson(['success' => false, 'errorMessage' => 'unable to checkout entire cart at this moment', 'errors' => $newPurchase->errors]);
                }
            }
            return $this->asJson(['success' => true, 'successMessage' => 'cart checkout success']);
        } else {
            return $this->asJson(['success' => true, 'errorMessage' => 'no cart available', 'cart' => $reqCart]);
        }
    }

    public function actionGetorders()
    {
        $query = new Query();
        $user = yii::$app->user->identity;
        $query->select([
            'yiipurchases.*',
            'books.Book_Title AS Book_Title',
            'yiicoupons.Name AS Coupon_Name'
        ])->from('yiipurchases')->where(['yiipurchases.User_Id' => $user['id']])->leftJoin('books', 'books.id =yiipurchases.Book_Id')->leftJoin('yiicoupons', 'yiicoupons.id = yiipurchases.Coupon_Id');

        $orders = $query->all();
        if ($orders) {
        $timeZone=yii::$app->timeZone;

            return $this->asJson(['success' => true, 'successMessage' => 'order fetch success', 'data' => $orders,'time'=>$timeZone]);
        } else {
            return $this->asJson(['success' => false, 'errorMessage' => 'no orders']);
        }
    }

    public function actionGetuserdetails(){
        $user=yii::$app->user->identity;
        unset($user['Password']);
        unset($user['role']);
        unset($user['access_token']);
        return $this->asJson(['success'=>true,'data'=>$user]);
    }

    public function actionUpdateuserdetails(){
        $user=yii::$app->user->identity;
        $data=yii::$app->getRequest()->getBodyParams();
        $reqUser=Users::findOne($user['id']);

        $reqUser['First_Name']=$data['First_Name'];
        $reqUser['Last_Name']=$data['Last_Name'];
        $reqUser['City']=$data['City'];
        $reqUser['Mobile']=$data['Mobile'];
        $reqUser['Address_line1']=$data['Address']['Address_line1'];
        $reqUser['Address_line2']=$data['Address']['Address_line2'];
        $reqUser['Address_line3']=$data['Address']['Address_line3'];
        if($reqUser->validate() && $reqUser->save()){
            return $this->asJson(['success'=>true]);
        }else{
            return $this->asJson(['success'=>false,'errors'=>$reqUser->errors]);
        }
    }

    public function actionChangeprofilepic(){
        $user=yii::$app->user->identity;
        $reqUser=Users::findOne($user['id']);
        $profilePic=new ProfileImageUpload();
        $profilePic->imageFile=UploadedFile::getInstanceByName('imageFile');
        $oldPic=$reqUser->Profile_Pic;
        if($profilePic->upload()){
            $reqUser['Profile_Pic']= $profilePic->getImageUrl();
            if($reqUser->Profile_Pic){
                    unlink($oldPic);
                }
            if($reqUser->save()){
                $updatedUser=Users::findOne($user['id']);
                unset($updatedUser['Password']);
                unset($updatedUser['role']);
                unset($updatedUser['access_token']);
                return $this->asJson(['success'=>true,'data'=>$updatedUser]);
            }else{
                return $this->asJson(['success'=>false]);
            }
        }else{
            return $this->asJson(['success'=>false,'error'=>$profilePic->errors]);
        }
    }

}