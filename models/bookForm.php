<?php

namespace app\models;

use yii;
use yii\base\Model;


class bookForm extends Model
{
    public $ISBN;
    public $Book_Title;
    public $Author;
    public $Pulication_year;
    public $Language;
    public $No_Of_Copies_Actual;
    public $No_Of_Copies_Current;
    public $Availabel;
    public $Price;
    public $File_path;
    public $Status;


    public function rules()
    {
        return [
            [[
                'ISBN', 'Book_Title', 'Author', 'Pulication_year', 'Language', 'No_Of_Copies_Actual', 'No_Of_Copies_Current',
                'Availabel', 'Price'
            ], 'required'],

        ];
    }
}
