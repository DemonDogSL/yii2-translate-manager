<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class LanguageSourceSearch extends LanguageSource {

    public $translation;
    public $source;
    public $searchEmptyCommand;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['category', 'message', 'translation', 'source'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    /**
     * @param array $params Search conditions.
     * @return ActiveDataProvider
     */
    public function search($params) {
        $translateLanguage = Yii::$app->request->get('language_id', Yii::$app->sourceLanguage);
        $sourceLanguage = $this->_getSourceLanguage();
        $query = LanguageSource::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'route' => '/translateManager/translate',
            ],
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'id',
                'category',
                'message',
                'translation' => [
                    'asc' => ['lt.translation' => SORT_ASC],
                    'desc' => ['lt.translation' => SORT_DESC],
                    'label' => Yii::t('language', 'Translation'),
                ],
            ],
        ]);
        if (!($this->load($params) && $this->validate())) {
            $query->joinWith(['languageTranslate' => function ($query) use ($translateLanguage) {
                $query->from(['lt' => LanguageTranslate::tableName()])->onCondition(['lt.language' => $translateLanguage]);
            }]);
            $query->joinWith(['languageTranslate0' => function ($query) use ($sourceLanguage) {
                $query->from(['ts' => LanguageTranslate::tableName()])->onCondition(['ts.language' => $sourceLanguage]);
            }]);

            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'category' => $this->category,
        ]);
        $query->andFilterWhere([
            'or',
            ['like', 'message', $this->message],
            ['like', 'ts.translation', $this->message],
        ]);

        $query->joinWith(['languageTranslate' => function ($query) use ($translateLanguage) {
            $query->from(['lt' => LanguageTranslate::tableName()])->onCondition(['lt.language' => $translateLanguage]);
            if (!empty($this->searchEmptyCommand) && $this->translation == $this->searchEmptyCommand) {
                $query->andWhere(['or', ['lt.translation' => null], ['lt.translation' => '']]);
            } else {
                $query->andFilterWhere(['like', 'lt.translation', $this->translation]);
            }
        }]);

        $query->joinWith(['languageTranslate0' => function ($query) use ($sourceLanguage) {
            $query->from(['ts' => LanguageTranslate::tableName()])->onCondition(['ts.language' => $sourceLanguage]);
        }]);

        return $dataProvider;
    }

    /**
     * @return string
     */
    private function _getSourceLanguage() {
        $languageSourceSearch = Yii::$app->request->get('LanguageSourceSearch', []);
        return isset($languageSourceSearch['source']) ? $languageSourceSearch['source'] : Yii::$app->sourceLanguage;
    }

}