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
        // $behaviors['authenticator'] = [
        //     'class' => HttpBearerAuth::class,
        // ];
        // $behaviors['access'] = [
        //     'class' => AccessControl::class,
        //     'rules' => [
        //         [
        //             'allow' => true,
        //             'actions' => ['index', 'getbookbyid'],
        //             'roles' => ['manageBook', 'userPermissions']
        //         ],
        //         [
        //             'allow' => true,
        //             'actions' => ['create', 'updatebook', 'deletebook'],
        //             'roles' => ['manageBook']
        //         ]
        //     ]
        // ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index'  => ['GET'],
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

    // ----------------------get book by id------------------

    public function actionGetbookbyid($id)
    {
        $request = yii::$app->getRequest();
        $query = new Query();
        $query->select(['books.*', 'Categories.category_name AS Category_Type'])
            ->from('books')
            ->where("books.id=:id", [':id' => $id])
            ->leftJoin('Categories', 'books.Category_Type = Categories.id');
        $reqBook = $query->all();
        if (
            count($reqBook) > 0
        ) {
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
        } else {
            $book->validate();
            return ['status' => 'error', 'errors' => [...$model->getErrors(), ...$book->errors], 'data' => $params];
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
        if ($book->save()) {
            return $this->asJson(['success' => true, 'message' => 'successfully updated the data']);
        } else {
            return $this->asJson(['success' => false, 'message' => 'unable to update the data']);
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

    public function actionGetrole()
    {
    }
}
