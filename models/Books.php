<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string $ISBN
 * @property string $Book_Title
 * @property string $Author
 * @property string $Publication_Year
 * @property string $Language
 * @property int $No_Of_Copies_Actual
 * @property int $No_Of_Copies_Current
 * @property int $Available
 * @property int $Price
 * @property int|null $Category_Type
 * 
 * @property int $Status
 *
 * @property Categories $categoryType
 * 
 */
class Books extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile
     */
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'ISBN',
                    'Book_Title',
                    'Author',
                    'Publication_Year',
                    'Language',
                    'No_Of_Copies_Actual',
                    'No_Of_Copies_Current',
                    'Available',
                    'Price',
                    'Status',
                    'Category_Type',
                    'File_Path'
                ],
                'required'
            ],
            [['Publication_Year'], 'date', 'format' => 'y-m-d'],
            [['No_Of_Copies_Actual', 'No_Of_Copies_Current', 'Available', 'Price', 'Category_Type', 'Status'], 'integer'],
            [['ISBN', 'Book_Title', 'Author', 'Language', 'File_Path'], 'string', 'max' => 255],
            [
                ['Category_Type'],
                'exist',
                'targetClass' => Categories::class,
                'targetAttribute' => ['Category_Type' => 'id']
            ],

        ];
    }



    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ISBN' => 'Isbn',
            'Book_Title' => 'Book Title',
            'Author' => 'Author',
            'Publication_Year' => 'Publication Year',
            'Language' => 'Language',
            'No_Of_Copies_Actual' => 'No Of Copies Actual',
            'No_Of_Copies_Current' => 'No Of Copies Current',
            'Available' => 'Available',
            'Price' => 'Price',
            'Category_Type' => 'Category Type',
            'File_Path' => 'File Path',
            'Status' => 'Status',
        ];
    }

    /**
     * Gets query for [[CategoryType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryType()
    {
        return $this->hasOne(Categories::class, ['id' => 'Category_Type']);
    }

    /**
     * Gets query for [[Purchases]].
     *
     * @return \yii\db\ActiveQuery
     */

}