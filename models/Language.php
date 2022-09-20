<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\models;

use Yii;
use yii\data\ArrayDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * @property string $language_id
 * @property string $language
 * @property string $country
 * @property string $name
 * @property string $name_ascii
 * @property int $status

 * @property LanguageTranslate $languageTranslate
 * @property LanguageSource[] $languageSources
 */

class Language extends ActiveRecord {

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BETA = 2;
    public $newTranslationsArray = [];

    /**
     * @var array
     * @translate
     */
    private static $_CONDITIONS = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_BETA => 'Beta',
    ];

    public static function tableName() {
        return 'language';
    }

    public function rules() {
        return [
            [['language_id', 'language', 'country', 'name', 'name_ascii', 'status'], 'required'],
            [['status'], 'integer'],
            [['language_id'], 'string', 'max' => 5],
            [['language', 'country'], 'string', 'max' => 3],
            [['name', 'name_ascii'], 'string', 'max' => 32],
            [['language_id'], 'unique'],
        ];
    }

    public function attributeLabels() {
        return [
            'language_id' => Yii::t('core_model', 'ID') . '*',
            'language' => Yii::t('core_model', 'Language') . '*',
            'country' => Yii::t('core_model', 'Country') . '*',
            'name' => Yii::t('core_model', 'Name') . '*',
            'name_ascii' => Yii::t('core_model', 'Name Ascii') . '*',
            'status' => Yii::t('core_model', 'Status') . '*',
        ];
    }

    public static function getPreferredLanguages() {
        $temp_list = self::find()->indexBy('language_id')->where(['status' => [1, 2]])->all();
        return ArrayHelper::map($temp_list, 'language_id', 'language_id');
    }

    /**
     * @param bool $active True/False according to the status of the language.
     * @return array
     * @deprecated since version 1.5.2
     */
    public static function getLanguageNames($active = false) {
        $languageNames = [];
        foreach (self::getLanguages($active, true) as $language) {
            $languageNames[$language['language_id']] = $language['name'];
        }
        return $languageNames;
    }

    /**
     * @param bool $active True/False according to the status of the language.
     * @param bool $asArray Return the languages as language object or as 'flat' array
     * @return Language|array
     * @deprecated since version 1.5.2
     */
    public static function getLanguages($active = true, $asArray = false) {
        if ($active) {
            return self::find()->where(['status' => static::STATUS_ACTIVE])->asArray($asArray)->all();
        } else {
            return self::find()->asArray($asArray)->all();
        }
    }

    /**
     * @return string
     */
    public function getStatusName() {
        return Yii::t('array', self::$_CONDITIONS[$this->status]);
    }

    /**
     * @return array
     */
    public static function getStatusNames() {
        return \DemonDogSL\translateManager\helpers\Language::a(self::$_CONDITIONS);
    }

    /**
     * @return int
     */
    public function getGridStatistic() {
        static $statistics;
        if (!$statistics) {
            $count = LanguageSource::find()->count();
            if ($count == 0) {
                return 0;
            }
            $languageTranslates = LanguageTranslate::find()
                ->select(['language', 'COUNT(*) AS cnt'])
                ->andWhere('translation IS NOT NULL')
                ->groupBy(['language'])
                ->all();
            foreach ($languageTranslates as $languageTranslate) {
                $statistics[$languageTranslate->language] = floor(($languageTranslate->cnt / $count) * 100);
            }
        }
        return isset($statistics[$this->language_id]) ? $statistics[$this->language_id] : 0;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageTranslate() {
        return $this->hasOne(LanguageTranslate::className(), ['language' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @deprecated since version 1.4.5
     */
    public function getIds() {
        return $this->hasMany(LanguageSource::className(), ['id' => 'id'])->viaTable(LanguageTranslate::tableName(), ['language' => 'language_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageSources() {
        return $this->hasMany(LanguageSource::className(), ['id' => 'id'])->viaTable(LanguageTranslate::tableName(), ['language' => 'language_id']);
    }

    // NEW ITEMS BY SCAN
    public static function newItemsSourceDataProvider() {
        $newTranslations = TranslateManagerTemp::findOne(['setting' =>'newTranslations']);
        $data = [];
        if (isset($newTranslations)) {
            $valueDecoded = json_decode($newTranslations->value);
            if (!empty($valueDecoded)) {
                foreach ($valueDecoded as $key => $value) {
                    $languageSource = LanguageSource::findOne($value);
                    if (isset($languageSource)) {
                        $data[] = [
                            'id' => $languageSource->id,
                            'category' => $languageSource->category,
                            'message' => $languageSource->message,
                        ];
                    }
                }
            }
        }
        return new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 1000,
                'route' => 'translateManager/language/scan',
            ],
        ]);
    }

    // REMOVE ITEMS BY SCAN
    public static function removeItemsSourceDataProvider($optimizer = null) {
        $existTranslations = TranslateManagerTemp::findOne(['setting' =>'existTranslations']);
        $data = [];
        if (isset($existTranslations)) {
            $valueDecoded = json_decode($existTranslations->value);
            if (!empty($valueDecoded)) {
                $languageSources = LanguageSource::find()->all();
                $array = [];
                if (!empty($languageSources)) {
                    foreach ($languageSources as $language) {
                        $array[] = $language->id;
                    }
                    $finalArray = array_diff($array, $valueDecoded);
                    if (!empty($finalArray)) {
                        foreach ($finalArray as $language) {
                            $languageSource = LanguageSource::findOne($language);
                            if (isset($languageSource)) {
                                if (isset($optimizer) && $optimizer === 'optimizer') {
                                    $data[] = $languageSource->id;
                                } else {
                                    $languages = [];
                                    if (!empty($languageSource->languageTranslates)) {
                                        foreach ($languageSource->languageTranslates as $languageTranslate) {
                                            $languages[] = $languageTranslate->language;
                                        }
                                    }
                                    $data[] = [
                                        'id' => $languageSource->id,
                                        'category' => $languageSource->category,
                                        'message' => $languageSource->message,
                                        'languages' => implode(', ', $languages),
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
        if (isset($optimizer)) {
            if ($optimizer === 'optimizer') {
                return $data;
            } else {
                return new ArrayDataProvider([
                    'allModels' => $data,
                    'pagination' => [
                        'pageSize' => 99999,
                    ],
                ]);
            }
        }
        return new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 1000,
                'route' => 'translateManager/language/scan',
            ],
        ]);
    }

}