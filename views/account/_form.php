<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Account */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="account-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput([
        'maxlength' => true,
        'disabled' => !$model->isNewRecord
    ]) ?>

    <?= $form->field($model, 'password')->passwordInput([
        'maxlength' => true,
        'value' => ''
    ]) ?>

    <?= $form->field($model, 'name')->textInput([
        'maxlength' => true
    ]) ?>

    <?= $form->field($model, 'role')->dropDownList([
        'Admin' => 'Admin',
        'Author' => 'Author'
    ], ['prompt' => 'Select Role']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', [
            'class' => 'btn btn-success'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
