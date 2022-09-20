<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

use DemonDogSL\translateManager\models\ExportForm;
use DemonDogSL\translateManager\models\Language;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/* @var $this yii\web\View */
/* @var $model ExportForm */

$this->title = Yii::t('language', 'Export');
?>
<div class="language-export col-sm-6">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'exportLanguages')->listBox(ArrayHelper::map(Language::find()->all(), 'language_id', 'name_ascii'), [
        'multiple' => true,
        'size' => 15,
    ]) ?>
    <?= $form->field($model, 'format')->radioList([
        Response::FORMAT_JSON => Response::FORMAT_JSON,
        Response::FORMAT_XML => Response::FORMAT_XML,
    ]) ?>
    <div class="form-group mb-3">
        <?= Html::submitButton(Yii::t('language', 'Export'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>