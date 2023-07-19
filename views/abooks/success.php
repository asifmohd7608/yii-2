<?php

use yii\helpers\Html;
?>
<h3>Book Details Entered</h3>

<p><?= Html::encode($model->ISBN) ?></p>
<p><?= Html::encode($model->Book_Title) ?></p>
<p><?= Html::encode($model->Author) ?></p>
<p><?= Html::encode($model->Pulication_year) ?></p>
<p><?= Html::encode($model->Language) ?></p>
<p><?= Html::encode($model->No_Of_Copies_Actual) ?></p>
<p><?= Html::encode($model->No_Of_Copies_Current) ?></p>
<p><?= Html::encode($model->Availabel) ?></p>
<p><?= Html::encode($model->Price) ?></p>