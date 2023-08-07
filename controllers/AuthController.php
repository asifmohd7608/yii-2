<?php

namespace app\controllers;

use yii;
use app\models\Users;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use app\models\LoginModel;

class AuthController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // $auth = $behaviors['authenticator'];
        // unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'signup' => ['POST'],
                'login' => ['POST'],
                'usersignup' => ['POST'],
                'userlogin' => ['POST']
            ]
        ];
        return $behaviors;
    }

    public function actionSignup()
    {
        $request = yii::$app->getRequest();
        $newAdmin = new Users();
        $params = yii::$app->request->getBodyParams();

        $newAdmin->load($request->post(), '');
        $newAdmin->Password = yii::$app->security->generatePasswordHash($params['Password']);
        $newAdmin->role = 'admin';
        $newAdmin->access_token = yii::$app->security->generateRandomString(16);
        if ($newAdmin->validate() && $newAdmin->save()) {
            $auth = Yii::$app->authManager;
            $adminRole = $auth->getRole('admin');
            $auth->assign($adminRole, $newAdmin->getId());
            return $this->asJson(['success' => true, 'successMesage' => 'successfully registered user', 'user' => ['name' => $newAdmin->First_Name, 'Token' => $newAdmin->access_token, "Role" => 'admin']]);
        } else {
            return $this->asJson([
                'success' => false,
                'errorMessage' => 'unable to register the user',
                'error' => $newAdmin->errors
            ]);
        }
    }

    public function actionLogin()
    {
        $request = yii::$app->getRequest();
        $loginModel = new LoginModel();
        $loginModel->load($request->post(), '');


        $params = yii::$app->request->getBodyParams();

        if (!$loginModel->validate()) {
            return $this->asJson(['success' => false, 'message' => $loginModel->errors]);
        } else {
            $user = Users::findOne(['Email' => $params['username']]);
            if ($user) {
                if (yii::$app->security->validatePassword($params['password'], $user['Password'])) {
                    $user->access_token = yii::$app->security->generateRandomString(16);
                    $user->save();
                    return $this->asJson([
                        'success' => true,
                        'message' => 'successfully logged in',
                        'user' => [
                            'user_id' => $user['id'],
                            'Email' => $user['Email'],
                            'Token' => $user->access_token
                        ]
                    ]);
                }
            } else {
                return $this->asJson(['success' => false, 'message' => 'Either password or username is incorrect']);
            }
        }
    }


    public function actionUsersignup()
    {
        $request = yii::$app->getRequest();
        $newUser = new Users();
        $params = yii::$app->request->getBodyParams();

        $newUser->load($request->post(), '');
        $newUser->Address_line1=$params['Address']['AddressLine1'];
        $newUser->Address_line2=$params['Address']['AddressLine2'];
        $newUser->Address_line3=$params['Address']['AddressLine3'];
        $newUser->Password = yii::$app->security->generatePasswordHash($params['Password']);
        $newUser->role = 'user';
        $newUser->access_token = yii::$app->security->generateRandomString(16);
        if ($newUser->validate() && $newUser->save()) {
            $auth = yii::$app->authManager;
            $userRole = $auth->getRole('user');
            $auth->assign($userRole, $newUser->getId());
            return $this->asJson(['success' => true, 'successMesage' => 'successfully registered user', 'user' => ['name' => $newUser->First_Name, 'Token' => $newUser->access_token, "Role" => 'user']]);
        } else {
            return $this->asJson([
                'success' => false,
                'errorMessage' => 'unable to register the user',
                'error' => $newUser->errors,
                'params'=>$params
            ]);
        }
    }

    public function actionUserlogin()
    {
        $request = yii::$app->getRequest();
        $loginModel = new LoginModel();
        $loginModel->load($request->post(), '');
        $params = yii::$app->request->getBodyParams();
        if (!$loginModel->validate()) {
            return $this->asJson(['success' => false, 'message' => $loginModel->errors]);
        } else {
            $user = Users::findOne(['Email' => $params['username']]);
            if ($user) {
                if (yii::$app->security->validatePassword($params['password'], $user['Password'])) {
                    $user->access_token = yii::$app->security->generateRandomString(16);
                    $user->save();
                    return $this->asJson([
                        'success' => true,
                        'successMessage' => 'successfully logged in',
                        'user' => [
                            'user_id' => $user['id'],
                            'Email' => $user['Email'],
                            'Token' => $user->access_token,
                            'Role' => $user['role']
                        ]
                    ]);
                } else {
                    return $this->asJson(['success' => false, 'errMessage' => 'Either password or username is incorrect']);
                }
            } else {
                return $this->asJson(['success' => false, 'errMessage' => 'Either password or username is incorrect']);
            }
        }
    }

}