<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Flow;

/**
 * FlowSearch represents the model behind the search form about `backend\models\Flow`.
 */
class FlowSearch extends Flow
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_true'], 'integer'],
            [['fu_flow', 'zi_flow', 'fu_audit'], 'safe'],
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
        $query = Flow::find();

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
            'is_true' => $this->is_true,
        ]);

        $query->andFilterWhere(['like', 'fu_flow', $this->fu_flow])
            ->andFilterWhere(['like', 'zi_flow', $this->zi_flow])
            ->andFilterWhere(['like', 'fu_audit', $this->fu_audit]);

        return $dataProvider;
    }
}
