<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\controllers;

use DemonDogSL\translateManager\services\Optimizer;
use DemonDogSL\translateManager\services\Scanner;
use yii\console\Controller;
use yii\helpers\Console;

class ConsoleController extends Controller {

    /**
     * @inheritdoc
     */
    public $defaultAction = 'help';

    public function actionHelp() {
        $this->run('/help', [$this->id]);
    }

    public function actionScan() {
        $this->stdout("Scanning translations...\n", Console::BOLD);
        $scanner = new Scanner();
        $items = $scanner->run();
        $this->stdout("{$items} new item(s) inserted into database.\n");
    }

    public function actionOptimize() {
        $this->stdout("Optimizing translations...\n", Console::BOLD);
        $optimizer = new Optimizer();
        $items = $optimizer->run();
        $this->stdout("{$items} removed from database.\n");
    }

}