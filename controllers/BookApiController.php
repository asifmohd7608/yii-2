<?php

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\rest\Controller;
use app\models\Books;
use yii\filters\auth\HttpBearerAuth;


class BookApiController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        return $behaviors;
    }

    // --------------------get all books-----------------


    public function actionIndex()
    {
        $request = yii::$app->getRequest();
        if ($request->isGet) {
            $query = new Query();
            $query->select('*')->from('books');
            $books = $query->all();
            return $this->asJson(['success' => true, 'data' => $books]);
        } else {
            return $this->asJson(['success' => false, 'message' => 'invalid method']);
        }
    }

    // ----------------------get book by id------------------

    public function actionGetbookbyid($id)
    {
        $request = yii::$app->getRequest();
        if ($request->isGet) {
            $query = new Query();
            $query->select(['books.*', 'Categories.category_name'])
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
        } else {
            return $this->asJson(['success' => false, 'message' => 'invalid method']);
        }
    }
    // -----------------add new book--------------------

    public function actionCreate()
    {
        $book = new Books();
        $request = yii::$app->getRequest();
        $book->load($request->post(), '');
        if ($request->isPost) {
            if ($book->validate()) {
                if ($book->save()) {
                    return $this->asJson((['success' => true, 'message' => 'sucessfully added book to db']));
                } else {
                    return $this->asJson(['success' => false, 'message' => 'unable to add book to db']);
                }
            } else {
                return $this->asJson(['success' => false, 'error' => $book->errors]);
            }
        } else {
            return $this->asJson(['success' => false, 'message' => 'invalid method']);
        }
    }

    // -----------------------update book--------------------

    public function actionUpdatebook($id)
    {
        $book = Books::findOne($id);
        $request = yii::$app->getRequest();
        if ($request->isPost) {
            $updateData = $request->post();
            $book->attributes = $updateData;
            if ($book->save()) {
                return $this->asJson(['success' => true, 'message' => 'successfully updated the data']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'unable to update the data']);
            }
        } else {
            return $this->asJson(['success' => false, 'message' => 'invalid method']);
        }
    }

    public function actionDeletebook($id)
    {
        $request = yii::$app->getRequest();
        if ($request->isDelete) {
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
        } else {
            return $this->asJson(['success' => 'false', 'message' => 'invalid method']);
        }
    }
}
