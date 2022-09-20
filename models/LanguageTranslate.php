<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\models;

use Yii;

/**
 * @property string $id
 * @property string $language
 * @property string $translation

 * @property LanguageSource $LanguageSource
 * @property Language $language0
 */

class LanguageTranslate extends \yii\db\ActiveRecord {

    public $cnt;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'language_translate';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'language'], 'required'],
            [['id'], 'integer'],
            [['id'], 'exist', 'targetClass' => '\DemonDogSL\translateManager\models\LanguageSource'],
            [['language'], 'exist', 'targetClass' => '\DemonDogSL\translateManager\models\Language', 'targetAttribute' => 'language_id'],
            [['translation'], 'string'],
            [['language'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('core_model', 'ID'),
            'language' => Yii::t('core_model', 'Language'),
            'translation' => Yii::t('core_model', 'Translation'),
        ];
    }

    /**
     * @param int $id LanguageSource id
     * @param string $languageId Language language_id
     * @return LanguageTranslate
     * @deprecated since version 1.2.7
     */
    public static function getLanguageTranslateByIdAndLanguageId($id, $languageId) {
        $languageTranslate = self::findOne(['id' => $id, 'language' => $languageId]);
        if (!$languageTranslate) {
            $languageTranslate = new self([
                'id' => $id,
                'language' => $languageId,
            ]);
        }
        return $languageTranslate;
    }

    /**
     * @return array The name of languages the language element has been translated into.
     */
    public function getTranslatedLanguageNames() {
        $translatedLanguages = $this->getTranslatedLanguages();
        $data = [];
        foreach ($translatedLanguages as $languageTranslate) {
            $data[$languageTranslate->language] = $languageTranslate->getLanguageName();
        }
        return $data;
    }

    /**
     * @return LanguageTranslate[]
     */
    public function getTranslatedLanguages() {
        return static::find()->where('id = :id AND language != :language', [':id' => $this->id, 'language' => $this->language])->all();
    }

    /**
     * @staticvar array $language_names caching the list of languages.
     * @return string
     */
    public function getLanguageName() {
        static $language_names;
        if (!$language_names || empty($language_names[$this->language])) {
            $language_names = Language::getLanguageNames();
        }
        return empty($language_names[$this->language]) ? $this->language : $language_names[$this->language];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @deprecated since version 1.4.5
     */
    public function getId0() {
        return $this->hasOne(LanguageSource::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageSource() {
        return $this->hasOne(LanguageSource::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage0() {
        return $this->hasOne(Language::className(), ['language_id' => 'language']);
    }

}