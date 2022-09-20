<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model DemonDogSL\translateManager\models\Language */

$this->title = $model->name;
?>

<div class="language-view col-sm-6">
    <p>
        <?= Html::a(Yii::t('language', 'Update'), ['update', 'id' => $model->language_id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a(Yii::t('language', 'Delete'), ['delete', 'id' => $model->language_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('language', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
    </p>
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'language_id',
            'language',
            'country',
            'name',
            'name_ascii',
            [
                'label' => Yii::t('language', 'Status'),
                'value' => $model->getStatusName(),
            ],
            [
                'label' => Yii::t('language', 'Translation status'),
                'value' => $model->getGridStatistic() . '%',
            ],
        ],
    ])
    ?>
</div>