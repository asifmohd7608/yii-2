<?php

namespace app\controllers;

use yii;
use app\models\Users;
use yii\rest\Controller;

class AdminAuthController extends Controller
{




    public function actionSignup()
    {
        $request = yii::$app->getRequest();
        if ($request->isPost) {
            $newAdmin = new Users();
            $params = yii::$app->request->getBodyParams();

            $newAdmin->load($request->post(), '');
            $newAdmin->Password = yii::$app->security->generatePasswordHash($params['Password']);
            $newAdmin->role = 'admin';
            $newAdmin->access_token = yii::$app->security->generateRandomString(16);
            if ($newAdmin->validate() && $newAdmin->save()) {
                return $this->asJson(['success' => true, 'mesage' => 'successfully registered user']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'unable to register the user', 'error' => $newAdmin->errors]);
            }
        } else {

            return $this->asJson(['success' => false, 'message' => 'invalid method']);
        }
    }

    public function actionLogin()
    {
        $request = yii::$app->getRequest();
        if ($request->isPost) {
            $params = yii::$app->request->getBodyParams();
            if (!$params['username'] || !$params['password']) {
                $errorMsg = '';
                if (!$params['username'] && !$params['password']) {
                    $errorMsg = 'username and password is required';
                } else if (!$params['username']) {
                    $errorMsg = 'username is required';
                } else {
                    $errorMsg = 'password is required';
                }
                return $this->asJson(['success' => false, 'message' => $errorMsg]);
            } else {
                $user = Users::findOne(['Email' => $params['username']]);
                if ($user) {
                    if (yii::$app->security->validatePassword($params['password'], $user['Password'])) {
                        $user->access_token = yii::$app->security->generateRandomString(16);
                        $user->save();
                        return $this->asJson([
                            'success' => true, 'message' => 'successfully logged in',
                            'user' => [
                                'user_id' => $user['id'], 'Email' => $user['Email'],
                                'Token' => $user->access_token
                            ]
                        ]);
                    }
                } else {
                    return $this->asJson(['success' => false, 'message' => 'Either password or username is incorrect']);
                }
            }
        }
    }
}
