<?php
namespace app\models;

use app\models\Books;
use app\models\Users;
use yii\db\ActiveRecord;
use app\models\Yiicoupons;
use yii\behaviors\TimestampBehavior;

class Yiipurchases extends ActiveRecord{



public function behaviors()
{
    return [
        TimestampBehavior::class,
    ];
}
    public function rules(){
        return [
            [['User_Id', 'Book_Id','Quantity','Unit_Price', 'Total_Price', 'Discount', 'Amount_Paid'],'required'],
            [['User_Id', 'Book_Id',  'Total_Price', 'Discount', 'Amount_Paid','Quantity','Unit_Price'],'integer'],
            [['User_Id'],'exist','targetClass'=>Users::class,'targetAttribute'=>'id'],
            [['Book_Id'],'exist','targetClass'=>Books::class,'targetAttribute'=>'id'],
            [['Coupon_Id'],'exist','targetClass'=>Yiicoupons::class,'targetAttribute'=>'id']
        ];
    }
}