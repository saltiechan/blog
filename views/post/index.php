<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;

$isAdmin = Yii::$app->user->can('post/viewAll');
$currentUser = Yii::$app->user->identity->username;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Article', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title',
            'date',
            'username',

            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    $colors = [
                        'draft' => 'default',
                        'pending' => 'warning',
                        'published' => 'success',
                        'rejected' => 'danger',
                    ];

                    $status = $model->status;
                    $color = isset($colors[$status]) ? $colors[$status] : 'default';

                    return Html::tag(
                        'span',
                        ucfirst($status),
                        ['class' => 'label label-' . $color]
                    );
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            $url,
                            ['title' => 'View']
                        );
                    },

                    'update' => function ($url, $model) use ($currentUser) {
                        if (
                            $model->username === $currentUser &&
                            in_array($model->status, ['draft', 'rejected'])
                        ) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                $url,
                                ['title' => 'Update']
                            );
                        }

                        return '';
                    },

                    'delete' => function ($url, $model) {
                        if (Yii::$app->user->can('post/delete')) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-trash"></span>',
                                $url,
                                [
                                    'title' => 'Delete',
                                    'data-confirm' => 'Are you sure you want to delete this article?',
                                    'data-method' => 'post',
                                ]
                            );
                        }

                        return '';
                    },
                ],
            ],
        ],
    ]); ?>

</div>
