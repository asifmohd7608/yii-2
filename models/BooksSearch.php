<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Books;

/**
 * BooksSearch represents the model behind the search form of `app\models\Books`.
 */
class BooksSearch extends Books
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'No_Of_Copies_Actual', 'No_Of_Copies_Current', 'Available', 'Price', 'Category_Type', 'Status'], 'integer'],
            [['ISBN', 'Book_Title', 'Author', 'Publication_Year', 'Language', 'File_Path'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Books::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'Publication_Year' => $this->Publication_Year,
            'No_Of_Copies_Actual' => $this->No_Of_Copies_Actual,
            'No_Of_Copies_Current' => $this->No_Of_Copies_Current,
            'Available' => $this->Available,
            'Price' => $this->Price,
            'Category_Type' => $this->Category_Type,
            'Status' => $this->Status,
        ]);

        $query->andFilterWhere(['like', 'ISBN', $this->ISBN])
            ->andFilterWhere(['like', 'Book_Title', $this->Book_Title])
            ->andFilterWhere(['like', 'Author', $this->Author])
            ->andFilterWhere(['like', 'Language', $this->Language])
            ->andFilterWhere(['like', 'File_Path', $this->File_Path]);

        return $dataProvider;
    }
}