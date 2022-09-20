<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

use yii\grid\GridView;

/* @var $this \yii\web\View */
/* @var $oldDataProvider \yii\data\ArrayDataProvider */

?>
<?php if ($oldDataProvider->totalCount > 0) : ?>
    <?=
    GridView::widget([
        'id' => 'delete-source',
        'dataProvider' => $oldDataProvider,
        'columns' => [
            'id',
            'category',
            'message',
            'languages',
        ],
    ]);
    ?>
<?php endif ?>