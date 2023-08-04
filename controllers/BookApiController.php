<?php

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\rest\Controller;
use app\models\Books;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;
use app\models\ImageUploadForm;
use yii\web\UploadedFile;
use app\models\Users;




class BookApiController extends Controller
{

    public function behaviors()
    {

        $behaviors = parent::behaviors();


        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'getbookbyid', 'status', 'fetchbooksuser'],
                    'roles' => ['admin', 'user']
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'updatebook', 'deletebook', 'status', 'getcategories', 'changebookstatus'],
                    'roles' => ['admin']
                ]
            ]
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'getbookbyid' => ['GET'],
                'create' => ['POST'],
                'updatebook' => ['POST'],
                'deletebook' => ['DELETE'],
                'getcategories' => ['GET']
            ]
        ];


        return $behaviors;
    }

    // --------------------get all books-----------------


    public function actionIndex()
    {
        $request = yii::$app->getRequest();
        $query = new Query();
        $query->select(['books.*', 'Categories.category_name AS Category_Type'])
            ->from('books')
            ->leftJoin('Categories', 'books.Category_Type = Categories.id');
        $books = $query->all();
        return $this->asJson(['success' => true, 'data' => $books]);
    }
    public function actionFetchbooksuser()
    {
        $request = yii::$app->getRequest();
        $query = new Query();
        $query->select(['books.*', 'Categories.category_name AS Category_Type'])
            ->from('books')
            ->where(['and', 'Status' => 1, ['>', 'No_Of_Copies_Current', 0]])
            ->leftJoin('Categories', 'books.Category_Type = Categories.id');
        $books = $query->all();
        return $this->asJson(['success' => true, 'data' => $books]);
    }

    // ----------------------get book by id------------------

    public function actionGetbookbyid($id)
    {
        $request = yii::$app->getRequest();
        $query = new Query();
        // $query->select(['books.*', 'Categories.category_name AS Category_Type'])
        //     ->from('books')
        //     ->where("books.id=:id", [':id' => $id])
        //     ->leftJoin('Categories', 'books.Category_Type = Categories.id');

        //   $query->select('*')
        //     ->from('books')
        //     ->where("books.id=:id", [':id' => $id]);

        // $reqBook = $query->all();
        $reqBook = Books::findOne($id);
        // if (count($reqBook) > 0) {
        if ($reqBook) {
            return $this->asJson(['success' => true, 'data' => $reqBook]);
        } else {
            return $this->asJson(['success' => false, 'message' => 'invalid book id']);
        }
    }
    // -----------------add new book--------------------

    public function actionCreate()
    {
        $book = new Books();
        $model = new ImageUploadForm();
        $model->imageFile = UploadedFile::getInstanceByName('imageFile');

        $request = yii::$app->getRequest();
        $params = $request->getBodyParams();
        $book->load($request->post(), '');

        if ($model->upload()) {
            $book->File_Path = $model->getImageUrl();
        }


        if ($book->validate()) {
            if ($book->save()) {
                return $this->asJson((['success' => true, 'message' => 'sucessfully added book to db']));
            } else {

                return $this->asJson(['success' => false, 'message' => 'unable to add book to db', 'error' => $book->errors]);
            }
        } else {
            return $this->asJson(['success' => false, 'error' => $book->errors]);
        }
    }

    // -----------------------update book--------------------

    public function actionUpdatebook($id)
    {
        $book = Books::findOne($id);
        $request = yii::$app->getRequest();

        $updateData = $request->post();
        $book->attributes = $updateData;


        $model = new ImageUploadForm();
        $model->imageFile = UploadedFile::getInstanceByName('imageFile');
        if ($model->imageFile) {
            if ($model->upload()) {
                unlink($book->File_Path);
                $book->File_Path = $model->getImageUrl();

            } else {
                return $this->asJson(['success' => false, 'errorMessage' => 'unable to update the data', 'error' => $model->errors]);
            }
        }
        if ($book->validate() && $book->save()) {
            return $this->asJson(['success' => true, 'successMessage' => 'successfully updated the data', 'req' => $request->post()]);
        } else {
            return $this->asJson(['success' => false, 'errorMessage' => 'unable to update the data', 'error' => $model->errors]);
        }
    }

    public function actionDeletebook($id)
    {
        $request = yii::$app->getRequest();
        $book = Books::findOne($id);
        if ($book) {
            if ($book->delete()) {
                return $this->asJson(['success' => true, 'data' => $book, 'message' => 'successfully deleted book']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'unable to delete the book']);
            }
        } else {
            return $this->asJson(['success' => false, 'message' => "couldn't find book with id $id"]);
        }
    }

    public function actionGetcategories()
    {
        $query = new Query();
        $query->select('*')->from('categories');
        $categories = $query->all();
        return $this->asJson(['success' => true, 'data' => $categories]);
    }

    public function actionChangebookstatus()
    {
        $params = yii::$app->request->getBodyParams();
        $book = Books::findOne($params['id']);
        if ($book->Status == 0) {
            $book->Status = 1;
        } else {
            $book->Status = 0;
        }
        if ($book->save()) {
            return $this->asJson(['success' => true, 'successMessage' => 'changed status to' . $book->Status, 'data' => ['id' => $params['id'], 'status' => $book->Status]]);
        } else {
            return $this->asJson(['success' => false, 'errorMessage' => 'unable to change status at the moment', 'book' => $book]);
        }
    }

    public function actionStatus()
    {
        $userData = yii::$app->user->identity;
        $data = ['Token' => $userData->access_token, 'Role' => $userData->role];
        return $this->asJson(['success' => true, 'data' => $data]);
    }
}