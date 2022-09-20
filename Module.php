<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class Module extends \yii\base\Module {

    const SESSION_KEY_ENABLE_TRANSLATE = 'frontendTranslation_EnableTranslate';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'DemonDogSL\translateManager\controllers';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'language/list';
    public $layout = 'language';
    public $allowedIPs = ['127.0.0.1',];
    public $roles = [];
    public $ignoredCategories = ['yii'];
    public $onlyCategories = [];
    public $ignoredItems = ['.svn', '.git', '.gitignore', '.gitkeep', '.hgignore', '.hgkeep', '/messages', '/BaseYii.php', 'runtime', 'bower', 'nikic'];
    public $root = '@app';
    public $scanRootParentDirectory = false;
    public $tmpDir = '@runtime';
    public $patterns = ['*.php', '*.js'];
    public $subDir = '/translate/';
    public $patternPhp = '#::t\s*\(\s*(?P<category>\'[\w\d\s_-]+?(?<!\\\\)\'|"[\w\d\s_-]+?(?<!\\\\)"?)\s*,\s*(?P<text>\'.*?(?<!\\\\)\'|".*?(?<!\\\\)"?)\s*[,\)]#s';
    public $patternArray = "#\@translate[^\$]+\$(?P<text>.+?)[\]\)];#smui";
    public $patternArrayRecursive = '#(?P<category>)(\[|\(|>|,|)\s*(?P<text>\'.*?(?<!\\\\)\'|".*?(?<!\\\\)"?)\s*(,|$)#s';
    public $patternJs = '#ddt\.t\s*\(\s*(?P<text>\'.*?(?<!\\\\)\'|".*?(?<!\\\\)"?)\s*[,\)]#s';
    public $phpTranslators = ['::t'];
    public $jsTranslators = ['ddt.t'];
    public $patternArrayTranslator = '#\@translate[^\$]+(?P<translator>[\w\d\s_]+[^\(\[]+)#s';
    public $scanTimeLimit = null;
    public $tables;
    public $languageTable = '{{%language}}';
    public $searchEmptyCommand = '!';
    public $defaultExportStatus = 1;
    public $defaultExportFormat = Response::FORMAT_JSON;
    public $connection = 'db';
    public $scanners = [
        '\DemonDogSL\translateManager\services\scanners\ScannerPhpFunction',
        '\DemonDogSL\translateManager\services\scanners\ScannerPhpArray',
        '\DemonDogSL\translateManager\services\scanners\ScannerJavaScriptFunction',
        '\DemonDogSL\translateManager\services\scanners\ScannerDatabase',
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init() {
        parent::init();
        if ($this->onlyCategories && $this->ignoredCategories) {
            throw new InvalidConfigException("Please configure either 'ignoredCategories', or 'onlyCategories'!");
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        if ($this->checkAccess()) {
            return parent::beforeAction($action);
        } else {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
    }

    public function checkAccess() {
        $ip = Yii::$app->request->getUserIP();
        foreach ($this->allowedIPs as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }
        Yii::warning('Access to Translate is denied due to IP address restriction. The requested IP is ' . $ip, __METHOD__);
        return false;
    }

    public function getLanguageItemsDirPath() {
        return Yii::getAlias($this->tmpDir) . $this->subDir;
    }

}