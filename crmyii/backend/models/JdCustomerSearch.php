<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\JdCustomer;

/**
 * JdCustomerSearch represents the model behind the search form about `backend\models\JdCustomer`.
 */
class JdCustomerSearch extends JdCustomer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'isapp', 'submituser', 'ctime', 'business', 'customer_type', 'yushan_id', 'r_time', 'is_claim', 'audit', 'audit_u'], 'integer'],
            [['advertiser', 'product_line', 'industry', 'website', 'appname', 'tax_identification', 'ticket_address', 'open_account', 'account', 'kp_tel', 'city', 'site', 'post_site'], 'safe'],
            [['yu_e', 'huikuan', 'bukuan', 'undistributed_yu_e', 'dakuan', 'myu_e', 'dakuan_undistributed_yu_e', 'fapiao'], 'number'],
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
        $query = JdCustomer::find();

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
            'type' => $this->type,
            'isapp' => $this->isapp,
            'submituser' => $this->submituser,
            'ctime' => $this->ctime,
            'yu_e' => $this->yu_e,
            'huikuan' => $this->huikuan,
            'bukuan' => $this->bukuan,
            'business' => $this->business,
            'customer_type' => $this->customer_type,
            'yushan_id' => $this->yushan_id,
            'undistributed_yu_e' => $this->undistributed_yu_e,
            'dakuan' => $this->dakuan,
            'myu_e' => $this->myu_e,
            'dakuan_undistributed_yu_e' => $this->dakuan_undistributed_yu_e,
            'fapiao' => $this->fapiao,
            'r_time' => $this->r_time,
            'is_claim' => $this->is_claim,
            'audit' => $this->audit,
            'audit_u' => $this->audit_u,
        ]);

        $query->andFilterWhere(['like', 'advertiser', $this->advertiser])
            ->andFilterWhere(['like', 'product_line', $this->product_line])
            ->andFilterWhere(['like', 'industry', $this->industry])
            ->andFilterWhere(['like', 'website', $this->website])
            ->andFilterWhere(['like', 'appname', $this->appname])
            ->andFilterWhere(['like', 'tax_identification', $this->tax_identification])
            ->andFilterWhere(['like', 'ticket_address', $this->ticket_address])
            ->andFilterWhere(['like', 'open_account', $this->open_account])
            ->andFilterWhere(['like', 'account', $this->account])
            ->andFilterWhere(['like', 'kp_tel', $this->kp_tel])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'site', $this->site])
            ->andFilterWhere(['like', 'post_site', $this->post_site]);

        return $dataProvider;
    }
}
