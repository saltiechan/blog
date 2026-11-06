<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Post */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Posts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$isAdmin = Yii::$app->user->can('post/viewAll');
$isOwner = !Yii::$app->user->isGuest &&
    $model->username === Yii::$app->user->identity->username;
?>

<div class="post-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-default']) ?>

        <?php if ($isOwner && in_array($model->status, ['draft', 'rejected'])): ?>
            <?= Html::a('Update', ['update', 'id' => $model->idpost], [
                'class' => 'btn btn-primary',
            ]) ?>
        <?php endif; ?>

        <?php if ($isOwner && in_array($model->status, ['draft', 'rejected'])): ?>
            <?= Html::a('Submit Review', ['submit-review', 'id' => $model->idpost], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Are you sure you want to submit this article for review?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>

        <?php if ($isAdmin && $model->status === 'pending'): ?>
            <?= Html::a('Approve', ['approve', 'id' => $model->idpost], [
                'class' => 'btn btn-success',
                'data' => [
                    'confirm' => 'Are you sure you want to approve this article?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>

        <?php if ($isAdmin && $model->status === 'pending'): ?>
            <?= Html::beginForm(['reject', 'id' => $model->idpost], 'post') ?>

            <div class="form-group" style="margin-top: 15px;">
                <?= Html::label('Rejection Reason', 'rejection_reason') ?>

                <?= Html::textarea('rejection_reason', '', [
                    'id' => 'rejection_reason',
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Enter the reason for rejecting this article...',
                    'required' => true,
                ]) ?>
            </div>

            <?= Html::submitButton('Reject Article', [
                'class' => 'btn btn-danger',
                'data-confirm' => 'Are you sure you want to reject this article?',
            ]) ?>

            <?= Html::endForm() ?>
        <?php endif; ?>

    </p>

    <?php if ($model->status === 'rejected' && $isOwner): ?>
        <div class="alert alert-danger">
            <strong>Article Rejected</strong>
            <p><?= nl2br(Html::encode($model->rejection_reason)) ?></p>
            <p>Please revise your article and submit it again.</p>
        </div>
    <?php endif; ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title',
            [
            'attribute' => 'content',
            'format' => 'raw',
            'contentOptions' => [
                'style' => '
                max-height: 400px;
                overflow-y: auto;
                overflow-x: auto;
                display: block;
                overflow-wrap: anywhere;
                white-space: normal;
                ',
                ],
            ],
            'date',
            'username',
            [
                'attribute' => 'status',
                'value' => ucfirst($model->status),
            ],
        ],
    ]) ?>

</div>
