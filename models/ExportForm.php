<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\models;

use yii\base\Model;

class ExportForm extends Model {

    public $exportLanguages;
    public $format;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['exportLanguages', 'format'], 'required'],
        ];
    }

    /**
     * @param $minimumStatus int The status of the returned language will be equal or larger than this number.
     * @return Language[]
     */
    public function getDefaultExportLanguages($minimumStatus) {
        return Language::find()
            ->select('language_id')
            ->where(['>=', 'status', $minimumStatus])
            ->column();
    }

    /**
     * @return array[] Generate a two dimensional array of the translation data for the exportLanguages:
     */
    public function getExportData() {
        $languages = Language::findAll($this->exportLanguages);
        $languageSources = LanguageSource::find()->all();
        $languageTranslations = LanguageTranslate::findAll(['language' => $this->exportLanguages]);
        $data = [
            'languages' => $languages,
            'languageSources' => $languageSources,
            'languageTranslations' => $languageTranslations,
        ];
        return $data;
    }

}