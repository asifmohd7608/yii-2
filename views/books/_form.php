<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Books $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="books-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ISBN')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Book_Title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Author')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Publication_Year')->textInput() ?>

    <?= $form->field($model, 'Language')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'No_Of_Copies_Actual')->textInput() ?>

    <?= $form->field($model, 'No_Of_Copies_Current')->textInput() ?>

    <?= $form->field($model, 'Available')->textInput() ?>

    <?= $form->field($model, 'Price')->textInput() ?>

    <?= $form->field($model, 'Category_Type')->textInput() ?>

    <?= $form->field($model, 'File_Path')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
