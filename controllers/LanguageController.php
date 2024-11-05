<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\controllers;

use DemonDogSL\translateManager\models\LanguageSearch;
use DemonDogSL\translateManager\models\ExportForm;
use DemonDogSL\translateManager\models\ImportForm;
use DemonDogSL\translateManager\models\Language;
use DemonDogSL\translateManager\models\LanguageSource;
use DemonDogSL\translateManager\models\LanguageSourceSearch;
use DemonDogSL\translateManager\models\LanguageTranslate;
use DemonDogSL\translateManager\services\Generator;
use DemonDogSL\translateManager\services\Optimizer;
use DemonDogSL\translateManager\services\Scanner;
use Yii;
use yii\bootstrap5\ActiveForm;
use yii\db\Migration;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\JsonResponseFormatter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\XmlResponseFormatter;

class LanguageController extends Controller {

    public $module;
    public $defaultAction = 'list';

    public function beforeAction($action) {
        $this->module = Yii::$app->getModule('translateManager');
        return parent::beforeAction($action);
    }

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'change-status', 'create', 'view', 'update', 'delete', 'translate', 'save', 'dialog', 'message', 'scan', 'delete-source', 'optimizer', 'import', 'export'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'change-status', 'create', 'view', 'update', 'delete', 'translate', 'save', 'dialog', 'message', 'scan', 'delete-source', 'optimizer', 'import', 'export'],
                        'roles' => $this->module->roles,
                    ],
                ],
            ],
        ];
    }

    // RETURN LANGUAGE MODEL
    public function findModel($id) {
        if (($model = Language::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    // LIST PAGE
    public function actionList() {
        $searchModel = new LanguageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $dataProvider->sort = ['defaultOrder' => ['status' => SORT_DESC]];
        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    // ACTION CHANGE STATUS
    public function actionChangeStatus() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $language = Language::findOne(Yii::$app->request->post('language_id', ''));
        if ($language !== null) {
            $language->status = Yii::$app->request->post('status', Language::STATUS_BETA);
            if ($language->validate()) {
                $language->save();
            }
        }
        return $language->getErrors();
    }

    // CREATE PAGE
    public function actionCreate() {
        $model = new Language();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->language_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    // VIEW PAGE
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    // UPDATE PAGE
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/translateManager/view', 'id' => $model->language_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    // ACTION DELETE
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        return $this->redirect(['list']);
    }

    // TRANSLATE PAGE
    public function actionTranslate() {
        $searchModel = new LanguageSourceSearch([
            'searchEmptyCommand' => $this->module->searchEmptyCommand,
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $dataProvider->pagination->route = '/translateManager/language/translate';
        return $this->render('translate', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'searchEmptyCommand' => $this->module->searchEmptyCommand,
            'language_id' => Yii::$app->request->get('language_id', ''),
        ]);
    }

    // ACTION SAVE
    public function actionSave() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id', 0);
        $languageId = Yii::$app->request->post('language_id', Yii::$app->language);
        $languageTranslate = LanguageTranslate::findOne(['id' => $id, 'language' => $languageId]) ?:
            new LanguageTranslate(['id' => $id, 'language' => $languageId]);
        $languageTranslate->translation = Yii::$app->request->post('translation', '');
        if ($languageTranslate->validate() && $languageTranslate->save()) {
            $generator = new Generator($this->module, $languageId);
            $generator->run();
        }
        return $languageTranslate->getErrors();
    }

    // DIALOG PAGE
    public function actionDialog() {
        $languageSource = LanguageSource::find()->where([
            'category' => Yii::$app->request->post('category', ''),
            'MD5(message)' => Yii::$app->request->post('hash', ''),
        ])->one();
        if (!$languageSource) {
            return '<div id="translate-manager-error">' . Yii::t('language', 'Text not found in database! Please run project scan before translating!') . '</div>';
        }
        return $this->renderPartial('dialog', [
            'languageSource' => $languageSource,
            'languageTranslate' => $this->_getTranslation($languageSource),
        ]);
    }

    // ACTION MESSAGE
    public function actionMessage() {
        $languageTranslate = LanguageTranslate::findOne([
            'id' => Yii::$app->request->get('id', 0),
            'language' => Yii::$app->request->get('language_id', ''),
        ]);
        if ($languageTranslate) {
            $translation = $languageTranslate->translation;
        } else {
            $languageSource = LanguageSource::findOne([
                'id' => Yii::$app->request->get('id', 0),
            ]);
            $translation = $languageSource ? $languageSource->message : '';
        }
        return $translation;
    }

    // SCAN PAGE
    public function actionScan() {
        // IMPORTANT: Have to use "/yii/db/Migration" because "$connection->createCommand()" do not Create the table Before Create DataProviders
        // CHECK IF TEMP TABLE EXISTS
        $checkTableExist = Yii::$app->db->schema->getTableSchema('translate_manager_temp');
        $table = new Migration();
        // IMPORTANT: For not show a Message in the View
        $table->compact = true;
        if (isset($checkTableExist)) {
            $table->dropTable('{{%translate_manager_temp}}');
        }
        // CREATE TEMP TABLE
        $table->createTable('{{%translate_manager_temp}}',[
            'id'=> $table->primaryKey(11),
            'setting'=> $table->string(128)->notNull(),
            'value'=> $table->text()->notNull(),
        ], 'ENGINE=InnoDB');
        $scanner = new Scanner();
        $scanner->run();
        $newDataProvider = Language::newItemsSourceDataProvider();
        $oldDataProvider = Language::removeItemsSourceDataProvider();
        // DROP TEMP TABLE
        $checkTableExistAfter = Yii::$app->db->schema->getTableSchema('translate_manager_temp');
        if (isset($checkTableExistAfter)) {
            $table->dropTable('{{%translate_manager_temp}}');
        }
        return $this->render('scan', [
            'newDataProvider' => $newDataProvider,
            'oldDataProvider' => $oldDataProvider,
        ]);
    }

    // ACTION DELETE SOURCE
    public function actionDeleteSource() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ids = Yii::$app->request->post('ids');
        LanguageSource::deleteAll(['id' => (array) $ids]);
        return [];
    }

    // OPTIMIZE PAGE
    public function actionOptimizer() {
        // IMPORTANT: Have to use "/yii/db/Migration" because "$connection->createCommand()" do not Create the table Before Create DataProviders
        // CHECK IF TEMP TABLE EXISTS
        $checkTableExist = Yii::$app->db->schema->getTableSchema('translate_manager_temp');
        $table = new Migration();
        // IMPORTANT: For not show a Message in the View
        $table->compact = true;
        if (isset($checkTableExist)) {
            $table->dropTable('{{%translate_manager_temp}}');
        }
        // CREATE TEMP TABLE
        $table->createTable('{{%translate_manager_temp}}',[
            'id'=> $table->primaryKey(11),
            'setting'=> $table->string(128)->notNull(),
            'value'=> $table->text()->notNull(),
        ], 'ENGINE=InnoDB');
        $optimizer = new Optimizer();
        $optimizer->run();
        $newDataProvider = Language::newItemsSourceDataProvider();
        $oldDataProvider = Language::removeItemsSourceDataProvider('optimizerRemoved');
        // DELETE ROWS
        $removeLanguages = Language::removeItemsSourceDataProvider('optimizer');
        foreach ($removeLanguages as $language) {
            $languageSource = LanguageSource::findOne($language);
            if (isset($languageSource)) {
                $languageSource->delete();
            }
        }
        // DROP TEMP TABLE
        $checkTableExistAfter = Yii::$app->db->schema->getTableSchema('translate_manager_temp');
        if (isset($checkTableExistAfter)) {
            $table->dropTable('{{%translate_manager_temp}}');
        }
        return $this->render('optimizer', [
            'newDataProvider' => $newDataProvider,
            'oldDataProvider' => $oldDataProvider,
        ]);
    }

    // IMPORT PAGE
    public function actionImport() {
        $model = new ImportForm();
        if (Yii::$app->request->isPost) {
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            if ($model->validate()) {
                try {
                    $result = $model->import();
                    $message = Yii::t('language', 'Successfully imported {fileName}', ['fileName' => $model->importFile->name]);
                    $message .= "<br/>\n";
                    foreach ($result as $type => $typeResult) {
                        $message .= "<br/>\n" . Yii::t('language', '{type}: {new} new, {updated} updated', [
                                'type' => $type,
                                'new' => $typeResult['new'],
                                'updated' => $typeResult['updated'],
                            ]);
                    }
                    $languageIds = Language::find()
                        ->select('language_id')
                        ->where(['status' => Language::STATUS_ACTIVE])
                        ->column();
                    foreach ($languageIds as $languageId) {
                        $generator = new Generator($this->module, $languageId);
                        $generator->run();
                    }
                    Yii::$app->getSession()->setFlash('success', $message);
                } catch (\Exception $e) {
                    if (YII_DEBUG) {
                        throw $e;
                    } else {
                        Yii::$app->getSession()->setFlash('danger', str_replace("\n", "<br/>\n", $e->getMessage()));
                    }
                }
            }
        }
        return $this->render('import', [
            'model' => $model,
        ]);
    }

    // EXPORT PAGE
    public function actionExport() {
        $module = $this->module;
        $model = new ExportForm([
            'format' => $module->defaultExportFormat,
        ]);
        if ($model->load(Yii::$app->request->post())) {
            $fileName = Yii::t('language', 'translations') . '.' . $model->format;
            Yii::$app->response->format = $model->format;
            Yii::$app->response->formatters = [
                Response::FORMAT_XML => [
                    'class' => XmlResponseFormatter::className(),
                    'rootTag' => 'translations',
                ],
                Response::FORMAT_JSON => [
                    'class' => JsonResponseFormatter::className(),
                ],
            ];
            Yii::$app->response->setDownloadHeaders($fileName);
            return $model->getExportData();
        } else {
            if (empty($model->languages)) {
                $model->exportLanguages = $model->getDefaultExportLanguages($module->defaultExportStatus);
            }
            return $this->render('export', [
                'model' => $model,
            ]);
        }
    }

    private function _getTranslation($languageSource) {
        $languageId = Yii::$app->request->post('language_id', '');
        $languageTranslate = $languageSource
            ->getLanguageTranslates()
            ->andWhere(['language' => $languageId])
            ->one();
        if (!$languageTranslate) {
            $languageTranslate = new LanguageTranslate([
                'id' => $languageSource->id,
                'language' => $languageId,
            ]);
        }
        return $languageTranslate;
    }

    // ACTION THAT LOAD EVERY ACTION IN THE SYSTEM
    public function actionRegisterAssets() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);
        } else {
            $browserLanguage = "en-GB";
        }
        $checkLanguage = Language::find()->where(['language_id' => $browserLanguage, 'status' => '1'])->one();
        $checkLanguageBeta = Language::find()->where(['language_id' => $browserLanguage, 'status' => '2'])->one();
        $preferredLanguage = isset(Yii::$app->request->cookies['language']) ? (string)Yii::$app->request->cookies['language'] : ($checkLanguage->language_id ?? ($checkLanguageBeta->language_id ?? 'en-GB'));
        if (!Yii::$app->user->isGuest) {
            if (isset(Yii::$app->user->identity->settingsList['language']) && Yii::$app->user->identity->settingsList['language'] !== $preferredLanguage) {
                $preferredLanguage = Yii::$app->user->identity->settingsList['language'];
                $languageCookie = new Cookie([
                    'name' => 'language',
                    'value' => $preferredLanguage,
                    'expire' => time() + 60 * 60 * 24 * 30
                ]);
                Yii::$app->response->cookies->add($languageCookie);
            }
        }
        Yii::$app->language = $preferredLanguage;
        \DemonDogSL\translateManager\helpers\Language::registerAssets();
    }

}