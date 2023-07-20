<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "admins".
 *
 * @property int $id
 * @property string $First_Name
 * @property string $Last_Name
 * @property string $Email
 * @property string $Password
 */
class Admins extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admins';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['First_Name', 'Last_Name', 'Email', 'Password'], 'required'],
            [['First_Name', 'Last_Name', 'Email', 'Password'], 'string', 'max' => 255],
            [['Email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'First_Name' => 'First Name',
            'Last_Name' => 'Last Name',
            'Email' => 'Email',
            'Password' => 'Password',
        ];
    }
}
