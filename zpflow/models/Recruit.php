<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "recruit".
 *
 * @property integer $recID
 * @property integer $recYear
 * @property string $recBatch
 * @property integer $recDefault
 * @property string $recStart
 * @property string $recEnd
 * @property string $recViewPlace
 * @property string $recHealthPlace
 */
class Recruit extends \yii\db\ActiveRecord
{
	const SCENARIO_ADD = 'add';
	const SCENARIO_MOD = 'mod';
	public function scenarios(){
		$scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_ADD] = ['recYear', 'recStart','recEnd','recBatch'];
	    $scenarios[self::SCENARIO_MOD] = ['recYear', 'recStart','recEnd','recBatch'];
	    return $scenarios;
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recruit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			['recYear', 'required','message'=>'招聘年度不能为空', 'on' => [self::SCENARIO_ADD,self::SCENARIO_MOD]],
			['recYear', 'validateYearStart', 'on' => [self::SCENARIO_ADD,self::SCENARIO_MOD]],
			['recYear', 'validateYearBatchUnique', 'on' => [self::SCENARIO_ADD]],
			['recYear', 'validateYearBatchUniqueExceptSelf', 'on' => [self::SCENARIO_MOD]],
			['recStart', 'required','message'=>'招聘起始时间不能为空', 'on' => [self::SCENARIO_ADD,self::SCENARIO_MOD]],
			['recStart', 'validateStartEnd', 'on' => [self::SCENARIO_ADD,self::SCENARIO_MOD]],
            ['recEnd', 'required','message'=>'招聘结束时间不能为空', 'on' => [self::SCENARIO_ADD,self::SCENARIO_MOD]],
            ['recEnd', 'validateEndCurrent', 'on' => [self::SCENARIO_ADD,self::SCENARIO_MOD]],
			['recBatch', 'required','message'=>'招聘批次不能为空', 'on' => [self::SCENARIO_ADD,self::SCENARIO_MOD]],
        ];
    }
	
	public function attributeLabels()
    {
        return [
            'recID' => '招聘ID',
            'recYear' => '招聘年度',
            'recBatch' => '招聘批次',
            'recStart' => '报名期限-起始时间',
            'recEnd' => '报名期限-终止时间',
            'recViewPlace' => '面试地点',
            'recHealthPlace' => '体检地点',
            'recDefault'	=>	'是否进行中',
            'recBack'	=>	'是否归档',
        ];
    }
	
	public function validateYearStart($attribute, $params){
		if ($this->recYear != "" && $this->recStart !="" && $this->recYear != substr($this->recStart,0,4)){
			$this->addError($attribute, "招聘年度与起始时间不一致");
		}
	}
	
	public function validateStartEnd($attribute, $params){
		if ($this->recStart != "" && $this->recEnd !="" && $this->recStart > $this->recEnd ){
			$this->addError($attribute, "起始时间不能大于结束时间");
		}
	}
	
	public function validateYearBatchUnique($attribute, $params){
		if ($this->recYear != "" && $this->recBatch !=""){
			$num = self::find()->where(['recYear'=>$this->recYear,'recBatch'=>$this->recBatch])->count();
			if($num > 0){
				$this->addError($attribute, "该年度的招聘批次已经存在了");
			}
		}
	}
	
	public function validateYearBatchUniqueExceptSelf($attribute, $params){
		if ($this->recYear != "" && $this->recBatch !=""){
			$info = self::find()->where(['recYear'=>$this->recYear,'recBatch'=>$this->recBatch])->one();
			if(!empty($info) && $info->recID != $this->recID){
				$this->addError($attribute, "该年度的招聘批次已经存在了");
			}
		}
	}
	
	public function validateEndCurrent($attribute, $params){
		date_default_timezone_set('PRC');
		$date = date('Y-m-d H:i:s',time());
		if($this->recEnd != "" && $this->recEnd <= $date){
			$this->addError($attribute, "招聘结束时间不能小于当前时间".$date);
		}
	}
	
	public static function getListInfo($offset,$rows,$orderInfo){
		$rows = self::find()->select(['recID','recYear','recBatch','recDefault','recStart','recEnd','recViewPlace','recHealthPlace','recBack'])
							->orderby($orderInfo)
							->offset($offset)
							->limit($rows)
							->asArray()
							->all();
							
		$total = self::find()->count();
		
		return ['rows'=>$rows,'total'=>$total];
	}
	
	public static function getCount($where = []){
		return self::find()->where($where)->count();
	}
	
	public static function getOverRecBatch(){
		$infos = self::find()->select(['recID','recYear','recBatch','recDefault','recEnd','recViewPlace'])
							->where(['!=', 'recDefault', 0])
							->orderby('recDefault asc,recYear desc,recBatch desc')
							->asArray()
							->all();
		$jsonData = [];
		foreach($infos as $info){
			$codes = [['recBatch','PC']];
			$codeName = Share::codeValue($codes,$info);
			$jsonData[] = [
				'id'		=>	$info['recID'],
				'code'		=>	$info['recDefault'] == 1 ? 1 : 0,
				'value'		=>	$info['recYear']."年".$codeName['recBatch'].($info['recDefault'] == '2' ? '【已结束】' : '【进行中】'),
				'recend'	=>	$info['recEnd'],
				'recViewPlace' => $info['recViewPlace'],
			];
		}
		return $jsonData;
	}
	
	public static function getHistoryRecInfo(){
		$recInfo = self::find()->where(['recDefault'=>2,'recBack'=>1])->orderby('recYear desc,recBatch asc')->asArray()->all();
		$jsonData = [];
		if(!empty($recInfo)){
			$codes = [['recBatch','PC']];
			foreach($recInfo as $info){
				$codeName = Share::codeValue($codes,$info);
				$jsonData[] = array_merge($info,$codeName);
			}
		}
		return $jsonData;
	}
	
	public static function insertData($data = []){
		$flag = Yii::$app->db->createCommand()->insert(self::tableName(),$data)->execute();
		if($flag){
			$result = ['result'=>1,'msg'=>'保存成功'];
		}else{
			$result = ['result'=>0,'msg'=>'保存失败'];
		}
		return $result;
	}
	
	public static function updateData($data = [],$primary = []){
		$flag = Yii::$app->db->createCommand()->update(self::tableName(),$data,$primary)->execute();
		if($flag !== false){
			if(!$flag){
				$result = ['result'=>0,'msg'=>'数据没有修改，不需要保存'];
			}else{
				$result = ['result'=>1,'msg'=>'保存成功'];
			}
		}else{
			$result = ['result'=>0,'msg'=>'保存失败'];
		}
		return $result;
	}
}
