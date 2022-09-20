<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class ImportForm extends Model {

    public $importFile;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [
                ['importFile'],
                'file',
                'skipOnEmpty' => false,
                'mimeTypes' => [
                    'text/xml',
                    'application/xml',
                    'application/json',
                    'text/plain',
                ],
                'enableClientValidation' => false,
            ],
        ];
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function import() {
        $result = [
            'languages' => ['new' => 0, 'updated' => 0],
            'languageSources' => ['new' => 0, 'updated' => 0],
            'languageTranslations' => ['new' => 0, 'updated' => 0],
        ];
        $data = $this->parseImportFile();
        $languages = Language::find()->indexBy('language_id')->all();
        foreach ($data['languages'] as $importedLanguage) {
            if (isset($languages[$importedLanguage['language_id']])) {
                $language = $languages[$importedLanguage['language_id']];
            } else {
                $language = new Language();
            }
            $importedLanguage['status'] = (int) $importedLanguage['status'];
            $language->attributes = $importedLanguage;
            if (count($language->getDirtyAttributes())) {
                $saveType = $language->isNewRecord ? 'new' : 'updated';
                if ($language->save()) {
                    ++$result['languages'][$saveType];
                } else {
                    $this->throwInvalidModelException($language);
                }
            }
        }
        $languageSources = LanguageSource::find()->indexBy('id')->all();
        $languageTranslations = LanguageTranslate::find()->all();
        $languageTranslations = ArrayHelper::map($languageTranslations, 'language', function ($languageTranslation) {
            return $languageTranslation;
        }, 'id');
        $importedLanguageTranslations = ArrayHelper::map($data['languageTranslations'], 'language', function ($languageTranslation) {
            return $languageTranslation;
        }, 'id');
        foreach ($data['languageSources'] as $importedLanguageSource) {
            $languageSource = null;
            if (isset($languageSources[$importedLanguageSource['id']]) &&
                ($languageSources[$importedLanguageSource['id']]->category == $importedLanguageSource['category']) &&
                ($languageSources[$importedLanguageSource['id']]->message == $importedLanguageSource['message'])
            ) {
                $languageSource = $languageSources[$importedLanguageSource['id']];
            }
            if (is_null($languageSource)) {
                foreach ($languageSources as $languageSourceSearch) {
                    if (($languageSourceSearch->category == $importedLanguageSource['category']) &&
                        ($languageSourceSearch->message == $importedLanguageSource['message'])
                    ) {
                        $languageSource = $languageSourceSearch;
                        break;
                    }
                }
            }
            if (is_null($languageSource)) {
                $languageSource = new LanguageSource([
                    'category' => $importedLanguageSource['category'],
                    'message' => $importedLanguageSource['message'],
                ]);
                if ($languageSource->save()) {
                    ++$result['languageSources']['new'];
                } else {
                    $this->throwInvalidModelException($languageSource);
                }
            }
            if (isset($importedLanguageTranslations[$importedLanguageSource['id']])) {
                foreach ($importedLanguageTranslations[$importedLanguageSource['id']] as $importedLanguageTranslation) {
                    $languageTranslate = null;
                    if (isset($languageTranslations[$languageSource->id]) &&
                        isset($languageTranslations[$languageSource->id][$importedLanguageTranslation['language']])
                    ) {
                        $languageTranslate = $languageTranslations[$languageSource->id][$importedLanguageTranslation['language']];
                    }
                    if (is_null($languageTranslate)) {
                        $languageTranslate = new LanguageTranslate();
                    }
                    $languageTranslate->attributes = $importedLanguageTranslation;
                    $languageTranslate->id = $languageSource->id;
                    if (count($languageTranslate->getDirtyAttributes())) {
                        $saveType = $languageTranslate->isNewRecord ? 'new' : 'updated';
                        if ($languageTranslate->save()) {
                            ++$result['languageTranslations'][$saveType];
                        } else {
                            $this->throwInvalidModelException($languageTranslate);
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @return array[]
     * @throws BadRequestHttpException
     */
    protected function parseImportFile() {
        $importFileContent = file_get_contents($this->importFile->tempName);
        if ($this->importFile->extension == Response::FORMAT_JSON) {
            $data = Json::decode($importFileContent);
        } elseif ($this->importFile->extension == Response::FORMAT_XML) {
            $xml = simplexml_load_string($importFileContent);
            $json = json_encode($xml);
            $data = json_decode($json, true);
            foreach ($data as $key => $value) {
                $data[$key] = current($value);
            }
        } else {
            throw new BadRequestHttpException('Only json and xml files are supported.');
        }
        return $data;
    }

    /**
     * @param ActiveRecord $model
     * @throws Exception
     */
    protected function throwInvalidModelException($model) {
        $errorMessage = Yii::t('language', 'Invalid model "{model}":', ['model' => $model->className()]);
        foreach ($model->getErrors() as $attribute => $errors) {
            $errorMessage .= "\n $attribute: " . join(', ', $errors);
        }
        throw new Exception($errorMessage);
    }

}