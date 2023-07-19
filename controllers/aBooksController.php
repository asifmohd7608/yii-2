<?php

namespace app\controllers;

use yii;
use yii\web\Controller;
// use yii\data\Pagination;
use app\models\books;
use yii\db\Query;
use app\models\bookForm;

class BooksController extends Controller
{
    public function actionIndex()
    {
        $query = new Query();
        $query->select(['books.*', 'categories.category_name as Category_Type'])
            ->from('books')
            ->join('LEFT JOIN', 'categories', 'categories.id=books.Category_Type');
        $books = $query->all();
        return $this->render('index', ['books' => $books]);
    }

    public function actionCreate()
    {
        $model = new bookForm();
        if ($model->load(yii::$app->request->post()) && $model->validate()) {
            return $this->render('success', ['model' => $model]);
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }
}
