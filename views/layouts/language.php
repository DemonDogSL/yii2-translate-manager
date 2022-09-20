<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

use DemonDogSL\translateManager\bundles\TranslateManagerAsset;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\widgets\Breadcrumbs;

TranslateManagerAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div>
            <?php
            NavBar::begin([
                'brandLabel' => 'Translate Manager',
                'brandUrl' => '/translateManager/language/list',
            ]);
            $menuItems = [
                ['label' => Yii::t('language', 'Languages'), 'url' => ['list']],
                ['label' => Yii::t('language', 'Scan'), 'url' => ['scan']],
                ['label' => Yii::t('language', 'Optimize'), 'url' => ['optimizer']],
                ['label' => Yii::t('language', 'Im-/Export'), 'items' => [
                    ['label' => Yii::t('language', 'Import'), 'url' => ['import']],
                    ['label' => Yii::t('language', 'Export'), 'url' => ['export']],
                ]],
                ['label' => Yii::t('language', 'Exit'), 'url' => ['/']],
            ];
            echo '<div class="offset-8">' .
                Nav::widget([
                    'options' => ['class' => 'navbar-nav'],
                    'items' => $menuItems,
                ]) .
            '</div>';
            NavBar::end();
            ?>
            <div class="container">
                <?=
                Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ])
                ?>
                <?php
                foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                    echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
                } ?>
                <?= Html::tag('h1', Html::encode($this->title)) ?>
                <?= $content ?>
            </div>
        </div>
        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; Demon Dog SL Translate Manager <?= date('Y') ?></p>
            </div>
        </footer>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>