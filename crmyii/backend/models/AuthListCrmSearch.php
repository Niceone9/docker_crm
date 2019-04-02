<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\AuthListCrm;

/**
 * AuthListCrmSearch represents the model behind the search form about `backend\models\AuthListCrm`.
 */
class AuthListCrmSearch extends AuthListCrm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['route','role'], 'safe'],
            [['type', 'id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AuthListCrm::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'role' => $this->role,
            'type' => $this->type,
            'id' => $this->id,
        ]);


        $query->andFilterWhere(['like', 'route', $this->route]);

        return $dataProvider;
    }
}
