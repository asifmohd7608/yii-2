<?php


namespace app\models;

use yii\base\Model;

class ImageModel extends Model
{
    public $image;

    public function rules()
    {
        return [
            [['image'], 'required'],
            [['image'], 'file']
        ];
    }
}
