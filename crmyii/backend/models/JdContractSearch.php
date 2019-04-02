<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\JdContract;

/**
 * JdContractSearch represents the model behind the search form about `backend\models\JdContract`.
 */
class JdContractSearch extends JdContract
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'advertiser', 'agent_company', 'product_line', 'type', 'contract_start', 'contract_end', 'payment_type', 'payment_time', 'state', 'the_archive', 'submituser', 'ctime', 'isxufei', 'isguidang', 'users2', 'xf_contractid', 'iszuofei', 'is_meijie', 'parent_id', 'mht_id', 'contract_state', 'market', 'zhouqi', 'is_copy'], 'integer'],
            [['contract_no', 'rebates_proportion', 'show_money', 'contract_money', 'note', 'fk_money', 'margin', 'xf_hetonghao', 'appname', 'account', 'market', 'dl_fandian', 'title'], 'safe'],
            [['yu_e', 'huikuan', 'bukuan', 'invoice', 'dakuan'], 'number'],
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
        $query = JdContract::find();

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
            'advertiser' => $this->advertiser,
            'agent_company' => $this->agent_company,
            'product_line' => $this->product_line,
            'type' => $this->type,
            'contract_start' => $this->contract_start,
            'contract_end' => $this->contract_end,
            'payment_type' => $this->payment_type,
            'payment_time' => $this->payment_time,
            'state' => $this->state,
            'the_archive' => $this->the_archive,
            'submituser' => $this->submituser,
            'ctime' => $this->ctime,
            'isxufei' => $this->isxufei,
            'isguidang' => $this->isguidang,
            'users2' => $this->users2,

            'xf_contractid' => $this->xf_contractid,
            'yu_e' => $this->yu_e,
            'huikuan' => $this->huikuan,
            'bukuan' => $this->bukuan,
            'invoice' => $this->invoice,
            'iszuofei' => $this->iszuofei,
            'is_meijie' => $this->is_meijie,
            'parent_id' => $this->parent_id,
            'mht_id' => $this->mht_id,
            'contract_state' => $this->contract_state,
            'market' => $this->market,
            'dakuan' => $this->dakuan,
            'zhouqi' => $this->zhouqi,
            'is_copy' => $this->is_copy,
        ]);

        $query->andFilterWhere(['like', 'contract_no', $this->contract_no])
            ->andFilterWhere(['like', 'rebates_proportion', $this->rebates_proportion])
            ->andFilterWhere(['like', 'show_money', $this->show_money])
            ->andFilterWhere(['like', 'contract_money', $this->contract_money])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'fk_money', $this->fk_money])
            ->andFilterWhere(['like', 'margin', $this->margin])
            ->andFilterWhere(['like', 'xf_hetonghao', $this->xf_hetonghao])
            ->andFilterWhere(['like', 'appname', $this->appname])
            ->andFilterWhere(['like', 'account', $this->account])
            ->andFilterWhere(['like', 'market', $this->market])
            ->andFilterWhere(['like', 'dl_fandian', $this->dl_fandian])
            ->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
