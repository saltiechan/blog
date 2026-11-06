<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">


    <div class="body-content">
    <?php foreach ($data as $row): ?>
        <div class="article-item">
            <h1><?= Html::encode($row->title) ?></h1>

            <p>
                <small>
                    <?= Html::encode($row->username . ' ' . $row->date) ?>
                </small>
            </p>

            <div class="article-content">
                <?= $row->content ?>
            </div>

            <hr>
        </div>
    <?php endforeach; ?>
</div>
</div>
