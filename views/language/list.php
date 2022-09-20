<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

use DemonDogSL\translateManager\bundles\LanguageAsset;
use DemonDogSL\translateManager\bundles\LanguageItemPluginAsset;
use DemonDogSL\translateManager\bundles\LanguagePluginAsset;
use DemonDogSL\translateManager\models\Language;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this \yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel DemonDogSL\translateManager\models\LanguageSearch */

$this->title = Yii::t('language', 'List of languages');

LanguageAsset::register($this);
LanguageItemPluginAsset::register($this);
LanguagePluginAsset::register($this);
?>

<style>
    li > a, li > a:hover {
        text-decoration: none;
        color: white;
    }
</style>

<div id="languages">
    <a href="create" class="btn btn-primary offset-10">Create Language</a>
    <?php
    Pjax::begin([
        'id' => 'languages',
    ]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'options' => [''],
            'prevPageLabel' => 'Previous',
            'nextPageLabel' => 'Next',
            'firstPageLabel' => 'First',
            'lastPageLabel' => 'Last',
            'nextPageCssClass' => 'btn btn-sm btn-primary',
            'prevPageCssClass' => 'btn btn-sm btn-primary',
            'firstPageCssClass' => 'btn btn-sm btn-dark',
            'lastPageCssClass' => 'btn btn-sm btn-dark',
            'maxButtonCount' => 0,
        ],
        'columns' => [
            [
                'attribute' => 'language_id',
                'label' => Yii::t('language', 'ID'),
                'enableSorting' => false,
            ],
            [
                'attribute' => 'name_ascii',
                'label' => Yii::t('language', 'Name Ascii'),
                'enableSorting' => false,
            ],
            [
                'format' => 'raw',
                'filter' => Language::getStatusNames(),
                'attribute' => 'status',
                'enableSorting' => false,
                'filterInputOptions' => ['class' => 'form-control', 'id' => 'status'],
                'label' => Yii::t('language', 'Status'),
                'content' => function ($language) {
                    return Html::activeDropDownList($language, 'status', Language::getStatusNames(), ['class' => 'status', 'id' => $language->language_id, 'data-url' => 'change-status']);
                },
            ],
            [
                'format' => 'raw',
                'attribute' => Yii::t('language', 'Statistic'),
                'content' => function ($language) {
                    return '<span class="statistic" style="height: 25px"><span style="width:' . $language->gridStatistic . '%; height: 25px"></span><i>' . $language->gridStatistic . '%</i></span>';
                },
            ],
            [
                'attribute' => '',
                'format' => 'raw',
                'filter' => false,
                'value' => function($model) {
                    return Html::a('<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1.125em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M573 241C518 136 411 64 288 64S58 136 3 241a32 32 0 000 30c55 105 162 177 285 177s230-72 285-177a32 32 0 000-30zM288 400a144 144 0 11144-144 144 144 0 01-144 144zm0-240a95 95 0 00-25 4 48 48 0 01-67 67 96 96 0 1092-71z"></path></svg>', ['view', 'id' => $model->language_id], [
                        'title' => Yii::t('language', 'View')]) . ' ' . Html::a('<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M498 142l-46 46c-5 5-13 5-17 0L324 77c-5-5-5-12 0-17l46-46c19-19 49-19 68 0l60 60c19 19 19 49 0 68zm-214-42L22 362 0 484c-3 16 12 30 28 28l122-22 262-262c5-5 5-13 0-17L301 100c-4-5-12-5-17 0zM124 340c-5-6-5-14 0-20l154-154c6-5 14-5 20 0s5 14 0 20L144 340c-6 5-14 5-20 0zm-36 84h48v36l-64 12-32-31 12-65h36v48z"></path></svg>', ['translate', 'language_id' => $model->language_id], [
                        'title' => Yii::t('language', 'Translate')]);
                },
                'contentOptions' => ['style' => 'padding-right: 0'],
            ],
        ],
    ]);
    Pjax::end();
    ?>
</div>