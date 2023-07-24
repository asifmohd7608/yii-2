<?php

namespace app\models;

use yii\base\Model;

class LoginModel extends Model
{
    public $username;
    public $password;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username'], 'email']
        ];
    }
}
