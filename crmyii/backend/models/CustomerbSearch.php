<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Customerb;

/**
 * CustomerbSearch represents the model behind the search form about `backend\models\Customerb`.
 */
class CustomerbSearch extends Customerb
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'submituser', 'ctime', 'r_time', 'is_claim', 'audit', 'audit_u', 'type', 'ytime', 'is_xu'], 'integer'],
            [['advertiser', 'industry', 'website', 'appname', 'city', 'site'], 'safe'],
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
        $query = Customerb::find();

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
            'id' => $this->id,
            'submituser' => $this->submituser,
            'ctime' => $this->ctime,
            'r_time' => $this->r_time,
            'is_claim' => $this->is_claim,
            'audit' => $this->audit,
            'audit_u' => $this->audit_u,
            'type' => $this->type,
            'ytime' => $this->ytime,
            'is_xu' => $this->is_xu,
        ]);

        $query->andFilterWhere(['like', 'advertiser', $this->advertiser])
            ->andFilterWhere(['like', 'industry', $this->industry])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'appname', $this->appname])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'site', $this->site]);

        return $dataProvider;
    }
}
