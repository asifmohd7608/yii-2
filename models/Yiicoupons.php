<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "yiicoupons".
 *
 * @property int $id
 * @property string $Name
 * @property string $Code
 * @property int $Coupon_Offer
 * @property string $Coupon_Type
 * @property string $Image_Path
 * @property string $Coupon_Status
 * @property string $Validity_Start
 * @property string $Validity_End
 * @property string $Coupon_Category
 */
class Yiicoupons extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yiicoupons';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'Code', 'Coupon_Offer', 'Coupon_Type', 'Image_Path', 'Coupon_Status', 'Validity_Start', 'Validity_End', 'Coupon_Category'], 'required'],
            [['Coupon_Offer','Coupon_Status'], 'integer'],
            [['Validity_Start', 'Validity_End'], 'date', 'format' => 'y-m-d'],
            [['Name', 'Code', 'Coupon_Type', 'Image_Path', 'Coupon_Category'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Name' => 'Name',
            'Code' => 'Code',
            'Coupon_Offer' => 'Coupon Offer',
            'Coupon_Type' => 'Coupon Type',
            'Image_Path' => 'Image Path',
            'Coupon_Status' => 'Coupon Status',
            'Validity_Start' => 'Validity Start',
            'Validity_End' => 'Validity End',
            'Coupon_Category' => 'Coupon Category',
        ];
    }
}
