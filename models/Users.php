<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $Email
 * @property string $First_Name
 * @property string|null $Last_Name
 * @property string $Address
 * @property string $City
 * @property int $Mobile
 * @property string $Password
 * @property string $role
 *  * @property string $access_token
 */
class Users extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Email', 'First_Name', 'Address', 'City', 'Mobile', 'Password', 'role', 'access_token'], 'required'],
            [['id', 'Mobile'], 'integer'],
            [['Email', 'First_Name', 'Last_Name', 'Address', 'City', 'Password'], 'string'],
            [['role', 'access_token'], 'string', 'max' => 45],
            [['id'], 'unique'],
            [['Email'], 'unique', 'targetClass' => self::class, 'message' => 'This email is already registered']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return
            [
                'id' => 'ID',
                'Email' => 'Email',
                'First_Name' => 'First Name',
                'Last_Name' => 'Last Name',
                'Address' => 'Address',
                'City' => 'City',
                'Mobile' => 'Mobile',
                'Password' => 'Password',
                'role' => 'Role',
                'access_token' => 'Access Token',
            ];
    }
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
}
