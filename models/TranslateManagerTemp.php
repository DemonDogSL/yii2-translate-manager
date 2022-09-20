<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $setting
 * @property string $value
 */

class TranslateManagerTemp extends ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'translate_manager_temp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['setting', 'value'], 'required'],
            [['value'], 'string'],
            [['setting'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('language', 'ID'),
            'setting' => Yii::t('language', 'Setting'),
            'value' => Yii::t('language', 'Value'),
        ];
    }

}