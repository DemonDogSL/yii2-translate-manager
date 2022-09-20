<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

use DemonDogSL\translateManager\bundles\TranslateAsset;
use DemonDogSL\translateManager\bundles\TranslatePluginAsset;
use DemonDogSL\translateManager\bundles\TranslationPluginAsset;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use DemonDogSL\translateManager\helpers\Language;
use DemonDogSL\translateManager\models\Language as Lang;

/* @var $this \yii\web\View */
/* @var $language_id string */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel DemonDogSL\translateManager\models\LanguageSourceSearch */
/* @var $searchEmptyCommand string */

TranslateAsset::register($this);
TranslatePluginAsset::register($this);
TranslationPluginAsset::register($this);

$this->title = Yii::t('language', 'Translation into {language_id}', ['language_id' => $language_id]);
?>

<style>
    li > a, li > a:hover {
        text-decoration: none;
        color: white;
    }
</style>

<?= Html::hiddenInput('language_id', $language_id, ['id' => 'language_id', 'data-url' => 'save']); ?>
<div id="translates" class="<?= $language_id ?>">
    <?php
    Pjax::begin([
        'id' => 'translates',
    ]);
    $form = ActiveForm::begin([
        'method' => 'get',
        'id' => 'search-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
    ]);
    echo $form->field($searchModel, 'source')->dropDownList(['' => Yii::t('language', 'Original')] + Lang::getLanguageNames(true))->label(Yii::t('language', 'Source language'));
    ActiveForm::end();
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
                'format' => 'raw',
                'filter' => Language::getCategories(),
                'attribute' => 'category',
                'enableSorting' => false,
                'filterInputOptions' => ['class' => 'form-control', 'id' => 'category'],
            ],
            [
                'format' => 'raw',
                'attribute' => 'message',
                'enableSorting' => false,
                'filterInputOptions' => ['class' => 'form-control', 'id' => 'message'],
                'label' => Yii::t('language', 'Source'),
                'content' => function ($data) {
                    return Html::textarea('LanguageSource[' . $data->id . ']', $data->source, ['class' => 'form-control source', 'readonly' => 'readonly']);
                },
            ],
            [
                'format' => 'raw',
                'attribute' => 'translation',
                'enableSorting' => false,
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'id' => 'translation',
                    'placeholder' => $searchEmptyCommand ? Yii::t('language', 'Enter "{command}" to search for empty translations.', ['command' => $searchEmptyCommand]) : '',
                ],
                'label' => Yii::t('language', 'Translation'),
                'content' => function ($data) {
                    return Html::textarea('LanguageTranslate[' . $data->id . ']', $data->translation, ['class' => 'form-control translation', 'data-id' => $data->id, 'tabindex' => $data->id]);
                },
            ],
            [
                'format' => 'raw',
                'label' => Yii::t('language', 'Action'),
                'content' => function ($data) {
                    return Html::button(Yii::t('language', 'Save'), ['type' => 'button', 'data-id' => $data->id, 'class' => 'btn btn-lg btn-success']);
                },
                'contentOptions' => ['style' => 'padding-right: 0'],
            ],
        ],
    ]);
    Pjax::end();
    ?>
</div>