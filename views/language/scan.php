<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

/* @var $this yii\web\View */
/* @var $newDataProvider \yii\data\ArrayDataProvider */
/* @var $oldDataProvider \yii\data\ArrayDataProvider */

use DemonDogSL\translateManager\bundles\ScanPluginAsset;

$this->title = Yii::t('language', 'Scanning project');

ScanPluginAsset::register($this);
?>

<style>
    li > a, li > a:hover {
        text-decoration: none;
        color: white;
    }
</style>

<div id="w2-info" style="padding: 1em; border-radius: 15px; margin-bottom: 1em; --bs-bg-opacity: 0.3;" class="bg-info text-dark">
    <?= Yii::t('language', '{n, plural, =0{No new entries} =1{One new entry} other{# new entries}} were added!', ['n' => $newDataProvider->totalCount]) ?>
</div>
<?= $this->render('__scanNew', [
    'newDataProvider' => $newDataProvider,
]) ?>
<div id="w2-danger" style="padding: 1em; border-radius: 15px; margin-bottom: 1em; --bs-bg-opacity: 0.3;" class="bg-danger text-dange">
    <?= Yii::t('language', '{n, plural, =0{No entries} =1{One entry} other{# entries}} remove!', ['n' => $oldDataProvider->totalCount]) ?>
</div>
<?= $this->render('__scanOld', [
    'oldDataProvider' => $oldDataProvider,
]) ?>