<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

/* @var $this yii\web\View */
/* @var $languageSource DemonDogSL\translateManager\models\LanguageSource */
/* @var $languageTranslate DemonDogSL\translateManager\models\LanguageTranslate */

use yii\bootstrap5\ActiveForm;

?>
<div id="translate-manager-dialog">
    <div>
        <?php
        $form = ActiveForm::begin([
            'id' => 'transslate-manager-change-source-form',
            'action' => ['message'],
        ]);
        ?>
        <?=$form->field($languageTranslate, 'id', ['enableLabel' => false])->hiddenInput(['name' => 'id', 'id' => 'language-source-id'])?>
        <h5><?=Yii::t('language', 'Choosing the language of translation')?></h5>
        <small><?=$form->field($languageTranslate, 'language')->dropDownList(array_merge([
                '' => Yii::t('language', 'Original'),
            ], $languageTranslate->getTranslatedLanguageNames()), [
                'name' => 'language_id',
                'id' => 'translate-manager-language-source',
            ])->label(false)?></small>
        <br>
        <?php
        ActiveForm::end();
        ?>
        <h5><?=Yii::t('language', 'Text to be translated')?></h5>
        <small id="translate-manager-message"><?=$languageSource->message?></small>
    </div>
    <br>
    <div>
        <?php
        $form = ActiveForm::begin([
            'id' => 'transslate-manager-translation-form',
            'method' => 'POST',
            'action' => ['save'],
        ]);
        ?>
        <?=$form->field($languageTranslate, 'id', ['enableLabel' => false])->hiddenInput(['name' => 'id'])?>
        <?=$form->field($languageTranslate, 'language', ['enableLabel' => false])->hiddenInput(['name' => 'language_id'])?>
        <h5><?=Yii::$app->language . ' ' . Yii::t('language', 'Translation')?></h5>
        <small><?=$form->field($languageTranslate, 'translation')->textarea(['name' => 'translation', 'class' => $languageTranslate->language . ' form-control'])->label(false)?></small>
        <?php ActiveForm::end(); ?>
    </div>
</div>