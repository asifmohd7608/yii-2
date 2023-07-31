<?php

namespace app\controllers;

use app\models\Books;
use yii;
use yii\rest\Controller;
use app\models\Cart;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
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
                    'actions'=>['addtocart','sendcart','removeitem'],
                    'roles' => ['user']
                ],
            ]
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'addtocart'=>['POST']
            ]
        ];
        return $behaviors;
    }

    public function actionAddtocart()
    {
        $params=yii::$app->request->getBodyParams();
        $query=new Query();
        $bookId=$params['id'];
        $user=yii::$app->user->identity;
        $query->select('*')->from('cart')->where(['Book_Id'=>$bookId,'User_Id'=>$user['id']]);
        $book=Books::findOne($bookId);
        if($book){
            $reqCart=$query->all();
            if($reqCart){
                return $this->asJson(['success'=>false,'errorMsg'=>'already added cart']);
            }else{
                $newCartItem=new Cart();
                $newCartItem['Book_Id']=$bookId;
                $newCartItem['User_Id']=$user['id'];
                $newCartItem['Quantity']=1;
                $newCartItem['Unit_Price']=$book['Price'];
                $newCartItem['Total_Price']=$book['Price'];
                $newCartItem->save();
            }
        
        return $this->asJson(['success'=>true,'successMessage'=>'item added to cart']);
    }else{
        return $this->asJson(['success'=>false,'errorMessage'=>'item doesnt exist']);
        }
    }

    public function actionSendcart()
    {
        $user=yii::$app->user->identity;
        $query=new Query();
        $query->select(['cart.*','Books.Book_Title AS Book_Title'])->from('cart')->where(['User_Id'=>$user['id']])->leftJoin('books','cart.Book_Id=books.id');
        $cart=$query->all();
        return $this->asJson(['success'=>true,'cart'=>$cart]);
    }

    public function actionRemoveitem(){
        $user=yii::$app->user->identity;
        $params=yii::$app->request->getBodyParams();
        $userId=$user['id'];
        $bookId=$params['id'];
      
        $cartItem = Cart::findOne(['User_Id' => $userId, 'Book_Id' => $bookId]);
        if($cartItem){
            $result=$cartItem->delete();
            if($result){
                $query=new Query();
                $query->select(['cart.*','Books.Book_Title AS Book_Title'])->from('cart')->where(['User_Id'=>$user['id']])->leftJoin('books','cart.Book_Id=books.id');
                $cart=$query->all();
                return $this->asJson(['success'=>true,'successMessage'=>'successfully deleted cart item','cart'=>$cart]);
            }else{
            return $this->asJson(['success'=>false,'errorMessage'=>'unable to delete cart item']);
            }
        }else{
            return $this->asJson(['success'=>false,'errorMessage'=>'unable to delete cart item']);
        }
    }
    public function actionDeletecart(){
         $user=yii::$app->user->identity;
        $params=yii::$app->request->getBodyParams();
        $userId=$user['id'];
        $bookId=$params['id'];
      
        $cartItem = Cart::findOne(['User_Id' => $userId, 'Book_Id' => $bookId]);
    }
}
