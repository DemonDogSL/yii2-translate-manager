<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

/* @var $this yii\web\View */
/* @var $model DemonDogSL\translateManager\models\Language */

$this->title = Yii::t('language', 'Create {modelClass}', [
    'modelClass' => 'Language',
]);
?>
<div class="language-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>