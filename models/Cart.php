<?php

namespace app\models;

use Yii;
use app\models\Books;
use app\models\Users;

/**
 * This is the model class for table "cart".
 *
 * @property int $id
 * @property int $Book_Id
 * @property int $User_Id
 * @property int $Quantity
 * @property int $Unit_Price
 * @property int $Total_Price
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cart';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Book_Id', 'User_Id', 'Quantity', 'Unit_Price', 'Total_Price'], 'required'],
            [['Book_Id', 'User_Id', 'Quantity', 'Unit_Price', 'Total_Price'], 'integer'],
            // [['Book_Id'],'exist','targetClass'=>Books::class,'targetAttribute'=>['id']],
            // [[['User_Id'],'exist','targetClass'=>Users::class,'targetAttribute'=>['id']]]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Book_Id' => 'Book ID',
            'User_Id' => 'User ID',
            'Quantity' => 'Quantity',
            'Unit_Price' => 'Unit Price',
            'Total_Price' => 'Total Price',
        ];
    }
}