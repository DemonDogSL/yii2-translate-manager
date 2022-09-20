<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property string $id
 * @property string $category
 * @property string $message

 * @property string $source
 * @property string $translation
 * @property LanguageTranslate $languageTranslate0
 * @property LanguageTranslate $languageTranslate
 * @property Language[] $languages
 */

class LanguageSource extends ActiveRecord {

    const INSERT_LANGUAGE_ITEMS_LIMIT = 10;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'language_source';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['message'], 'string'],
            [['category'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('core_model', 'ID'),
            'category' => Yii::t('core_model', 'Category'),
            'message' => Yii::t('core_model', 'Message'),
        ];
    }

    /**
     * @return string
     */
    public function getTranslation() {
        return $this->languageTranslate ? $this->languageTranslate->translation : '';
    }

    /**
     * @return string
     */
    public function getSource() {
        if ($this->languageTranslate0 && $this->languageTranslate0->translation) {
            return $this->languageTranslate0->translation;
        } else {
            return $this->message;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     *
     * @deprecated since version 1.5.3
     */
    public function getLanguageTranslateByLanguage() {
        return $this->getLanguageTranslate();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageTranslate0() {
        return $this->getLanguageTranslate();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageTranslate() {
        return $this->hasOne(LanguageTranslate::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageTranslates() {
        return $this->hasMany(LanguageTranslate::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages() {
        return $this->hasMany(Language::className(), ['language_id' => 'language'])
            ->viaTable(LanguageTranslate::tableName(), ['id' => 'id']);
    }

}
