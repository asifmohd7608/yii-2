<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\BooksSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="books-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ISBN') ?>

    <?= $form->field($model, 'Book_Title') ?>

    <?= $form->field($model, 'Author') ?>

    <?= $form->field($model, 'Publication_Year') ?>

    <?php // echo $form->field($model, 'Language') ?>

    <?php // echo $form->field($model, 'No_Of_Copies_Actual') ?>

    <?php // echo $form->field($model, 'No_Of_Copies_Current') ?>

    <?php // echo $form->field($model, 'Available') ?>

    <?php // echo $form->field($model, 'Price') ?>

    <?php // echo $form->field($model, 'Category_Type') ?>

    <?php // echo $form->field($model, 'File_Path') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
