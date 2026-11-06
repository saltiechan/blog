<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'My Account';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="account-profile">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="panel panel-default">
        <div class="panel-heading">
            Account Information
        </div>

        <div class="panel-body">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'username',
                    'name',
                    'role',
                ],
            ]) ?>

        </div>
    </div>

</div>