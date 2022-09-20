<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

/* @var $this yii\web\View */
/* @var $model DemonDogSL\translateManager\models\Language */

$this->title = Yii::t('language', 'Update {modelClass}: ', [
    'modelClass' => 'Language',
]) . ' ' . $model->name;
?>

<div class="language-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>