<?php

namespace app\models;

use yii\base\Model;

class ProfileImageUpload extends Model
{
    public $imageFile;
    public $filePath;

    public function rules()
    {
        return [
            [['imageFile'], 'image', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg, gif, jfif'],
        ];
    }

    public function upload()
    {
        global $filePath;
        if ($this->validate()) {
            $uploadPath = 'uploads/users/profilePic/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $imageName = time() . '_' . $this->imageFile->baseName . '.' . $this->imageFile->extension;
            $this->imageFile->saveAs($uploadPath . $imageName);
            $filePath = $uploadPath . $imageName;
            return true;
        } else {
            return false;
        }
    }

    public function getImageUrl()
    {
        global $filePath;
        return $filePath;
    }
}