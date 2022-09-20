<?php
/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

namespace DemonDogSL\translateManager\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class LanguageSearch extends Language {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['language_id', 'language', 'country', 'name', 'name_ascii'], 'safe'],
            [['status'], 'integer'],
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
        $query = Language::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'route' => 'translateManager/language/list'
            ],
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'status' => $this->status,
        ]);
        $query->andFilterWhere(['like', 'language_id', $this->language_id])
            ->andFilterWhere(['like', 'language', $this->language])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'name_ascii', $this->name_ascii]);
        return $dataProvider;
    }

}