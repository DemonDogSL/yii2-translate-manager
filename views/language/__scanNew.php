<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

/* @var $this \yii\web\View */
/* @var $newDataProvider \yii\data\ArrayDataProvider */

use yii\grid\GridView;

?>

<?php if ($newDataProvider->totalCount > 0) : ?>
    <?=
    GridView::widget([
        'id' => 'added-source',
        'dataProvider' => $newDataProvider,
        'pager' => [
            'options' => [''],
            'prevPageLabel' => 'Previous',
            'nextPageLabel' => 'Next',
            'nextPageCssClass' => 'btn btn-sm btn-primary',
            'prevPageCssClass' => 'btn btn-sm btn-primary',
            'maxButtonCount' => 0,
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'category',
            'message',
        ],
    ]);
    ?>
<?php endif ?>