<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

use DemonDogSL\translateManager\models\ImportForm;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/* @var $this yii\web\View */
/* @var $model ImportForm */

$this->title = Yii::t('language', 'Import');
?>

<div class="language-export col-sm-6">
    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
        ],
    ]); ?>
    <?= $form->field($model, 'importFile')->fileInput() ?>
    <div class="form-group mb-3">
        <?= Html::submitButton(Yii::t('language', 'Import'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>