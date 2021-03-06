<?php
namespace app\controllers;
use yii\web\Controller;
use yii\helpers\Html;
use Yii;

use yii\web\NotFoundHttpException;

use app\models\Examiner;
use app\models\Gstexm;
use app\models\Share;
use app\models\Recruit;
use app\models\Setgroup;

class ExaminerController extends BaseController{
	
	public function actionListInfo(){
		$request = Yii::$app->request;
		
		$recID = $request->post('recID');
		$exmName = $request->post('exmName');
		$exmType = $request->post('exmType');
		$type = intval($request->post('type'));
		
		if($type == 1){
			$condition = ['AND',['exmAttr'=>1,'recID'=>$recID]];
		}elseif($type == 2){
			$condition = ['AND',['exmAttr'=>2,'recID'=>$recID]];
		}else{
			$condition = ['AND',['recID'=>$recID]];
		}
		
		if($exmName != ""){
			$condition[] = ['AND',['like','exmName',$exmName]];
		}
		if($exmType != ""){
			$condition[] = ['AND',['exmType'=>$exmType]];
		}
		
		$sort = $request->post("sort"); 
        $order = $request->post("order","asc"); 
		
        if($sort){
	        $orderInfo = $sort.' '.$order;
        }else{
        	$orderInfo = 'exmID asc';
        }
		
		$result = [];
		
		$tab1 = Examiner::find()->where(['exmAttr'=>1,'recID'=>$recID])->count();
		$tab2 = Examiner::find()->where(['exmAttr'=>2,'recID'=>$recID])->count();
		$tab3 = Examiner::find()->where(['recID'=>$recID])->count();
		$result["headInfo"] = ['tab1'=>$tab1,'tab2'=>$tab2,'tab3'=>$tab3];
		
		$dataInfos = Examiner::find()->where($condition)->orderby($orderInfo)->asArray()->all();
		
		$result["rows"] = $dataInfos;
		
		$total = Examiner::find()->where($condition)->orderby($orderInfo)->count();;
		
		$result["total"] = $total;
		
		$result['exportInfo'] = ['condition'=>$condition];
		
		return $this->jsonReturn($result);
	}

	public function actionExaminerSave(){
		$request = Yii::$app->request;
		$db = Yii::$app->db->createCommand();
		
		$exmID = $request->post('exmID');
		$data = $request->post()['Examiner'];
		
		if($exmID == ""){
			$flag = $db	->	insert(Examiner::tableName(),$data)->execute();
			if($flag){
				$result = ['result'=>1,'msg'=>'保存成功'];
			}else{
				$result = ['result'=>0,'msg'=>'保存失败'];
			}
		}else{
			$flag = $db	->	update(Examiner::tableName(),$data, ['exmID'=>$exmID])->execute();
			if($flag !== false){
				if(!$flag){
					$result = ['result'=>0,'msg'=>'数据没有修改，不需要保存'];
				}else{
					$result = ['result'=>1,'msg'=>'保存成功'];
				}
			}else{
				$result = ['result'=>0,'msg'=>'保存失败'];
			}
		}
		
		return $this->jsonReturn($result);
	}
	
	public function actionExaminerDel(){
		$request = Yii::$app->request;
		
		$exmIDs = $request->post('exmIDs');
		$recID = $request->post('recID');
		
		$flag = Gstexm::find()->where(['recID'=>$recID,'exmID'=>$exmIDs])->asArray()->count();
		if($flag > 0){
			$result = ['result'=>0,'msg'=>'勾选的考官中存在已被安排的考官，不能删除！'];
		}else{
			if(Examiner::deleteAll(['exmID'=>$exmIDs])){
				$result = ['result'=>1,'msg'=>'删除成功'];
			}else{
				$result = ['result'=>0,'msg'=>'删除失败'];
			}
		}
		return $this->jsonReturn($result);
	}
	
	public function actionExaminerExport(){
		$request = Yii::$app->request;
		$conditionEN = $request->post('condition');
		$flag = $request->post('flag');
		$recID = $request->post('recID');
		$condition = Share::object_to_array(json_decode($conditionEN));
		
		$infos = Examiner::find()->where($condition)->asArray()->all();
		
		$dataJson = [];
		foreach($infos as $info){
			$codes = [['exmType','KGLB'],['exmAttr','KGSX']];
			$mainCode = Share::codeValue($codes,$info);
			$dataJson [] = array_merge($info,$mainCode);
		}
		
		$fileInfo = [];
		
		switch($flag){
			case '1' :
				$fileInfo = ['fileName'=>'公务员考官信息'];
				break;
			case '2' :
				$fileInfo = ['fileName'=>'其他考官信息'];
				break;
			case '3' :
				$fileInfo = ['fileName'=>'所有考官信息'];
				break;
			default :
				$fileInfo = ['fileName'=>''];
				break;
		}
		Share::exportCommonExcel(['sheet0'=>['data'=>$dataJson],'key'=>'flow4_step2','fileInfo'=>$fileInfo]);
	}
	
	public function actionExaminerImportmb(){
		$reader = \PHPExcel_IOFactory::createReader('Excel5');
		$PHPExcel = $reader->load('../web/mbfile/flow4_step_importmb.xls'); 
		ob_end_clean();
		$filename = iconv("utf-8","gb2312",'考官导入模板');
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename='.$filename.'.xls');
		header ( 'Cache-Control: max-age=0' );
		$objWriter = \PHPExcel_IOFactory::createWriter ($PHPExcel,'Excel5' );
		$objWriter->save ( 'php://output' );
		exit;
	}
	
	public function actionExaminerUpexcel(){
		$file = $_FILES['file'];
		$timeNow = date('Y-m-d H:i:s',time());
		
		$recInfo = Recruit::find()->where(['recDefault'=>1])->asArray()->one();
		$timeNowMonth = $recInfo['recYear'].$recInfo['recBatch'];
		
		$fileName = time().'.xls';
		
		if($_FILES['file']['size'] > 2*1024*1024){
			return ['code'=>'1','msg'=>'文件大小不能大于2M','data'=>['src'=>'']];
		}
		
		$createDir = './uploadfile/upexcel/'.$timeNowMonth;
		
		Share::mkdirs($createDir);
		
		move_uploaded_file($_FILES['file']['tmp_name'], $createDir."/".$fileName);
		
		$resultFile = $createDir."/".$fileName;
		
		return $this->jsonReturn(['code'=>0,'msg'=>'','data'=>['src'=>$createDir."/".$fileName]]);
	}
	
	public function actionExaminerUpexcelSure(){
		$request = Yii::$app->request;
		$filePath = $request->post('filePath');
		$recID = $request->post('recID');
        $reader = \PHPExcel_IOFactory::createReader('Excel5');
        $PHPExcel = $reader->load($filePath); 
        $sheet = $PHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = $sheet->getHighestColumn(); // 取得总列数
        
        $highestColumm= \PHPExcel_Cell::columnIndexFromString($highestColumm);
		
        if($highestColumm != 9){
            return $this->jsonReturn(['result'=>0,'msg'=>'模版不正确！']);
        }
		
        $keys = ['exmName','exmAttr','exmCom','exmType','exmPost','exmPhone','exmCertNo','exmTime'];
       
        $datas = [];
        $temp = 0;
        
        for ($row = 2; $row <= $highestRow; $row++){
            $temp = 0;
            $datatemp =[];
			$tempStr = "";
            for ($column = 1; $column < $highestColumm; $column++) {
            	$datatemp[$keys[$temp]] = $sheet->getCellByColumnAndRow($column, $row)->getValue();
				$tempStr .=$sheet->getCellByColumnAndRow($column, $row)->getValue();
				
                if($temp == 8){
                    break;
                }
                $temp++;
            }
			
			if($tempStr==""){
				break;
			}
            $datas[] = $datatemp;
        }
        //检测数据完整性
        if(empty($datas)){
            return $this->jsonReturn(['result'=>0,'msg'=>'导入数据为空！']);
        }
		
        $index = 2;
        $errorInfo = '';
        $personIDdata = [];
		$postTemp = [];
		$numTemp = [];
		
        foreach($datas as $per){
        	if($per['exmName'] == ''&&$per['exmAttr'] == ''&&$per['exmCom'] == ''&&$per['exmType'] == ''&&$per['exmPost'] == ''&&$per['exmCertNo'] == ''&&$per['exmPhone'] == ''&&$per['exmTime'] == ''){
        		break;
        	}
			if($per['exmName'] == ''){
				$errorInfo .= '第'.$index.'行考官姓名未填写！<br/>';
			}
			if($per['exmAttr'] == ''){
				$errorInfo .= '第'.$index.'行考官属性未填写！<br/>';
			}
			if($per['exmCom'] == ''){
				$errorInfo .= '第'.$index.'行考官所在单位未填写！<br/>';
			}
			if($per['exmType'] == ''){
				$errorInfo .= '第'.$index.'行考官分类未填写！<br/>';
			}
			if($per['exmPost'] == ''){
				$errorInfo .= '第'.$index.'行考官职务未填写！<br/>';
			}
			if($per['exmCertNo'] == ''){
				$errorInfo .= '第'.$index.'行考官证书编号未填写！<br/>';
			}
			if($per['exmPhone'] == ''){
				$errorInfo .= '第'.$index.'行考官手机号未填写！<br/>';
			}
			if($per['exmPhone'] != ''){
				if(!preg_match("/^[\d]{11}$/", $per['exmPhone'])){
					$errorInfo .= '第'.$index.'行手机号填写不正确！<br/>';
				}
			}
			
			if($per['exmTime'] != ''){
				if(!preg_match("/^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-))$/", $per['exmTime'])){
					$errorInfo .= '第'.$index.'行到岗时间填写不正确！<br/>';
				}
			}
            $index++;
        }
		
		if($errorInfo != ''){
			return $this->jsonReturn(['result'=>0,'msg'=>$errorInfo]);
		}else{
			foreach($datas as $per){
				$examiner = new Examiner();
				$examiner->exmName = $per["exmName"];
				$exmTypeArr = explode('=', $per["exmType"]);
				$examiner->exmType = $exmTypeArr[0];
				$examiner->exmCom = $per["exmCom"];
				$examiner->exmPost = $per["exmPost"];
				$examiner->exmPhone = $per["exmPhone"];
				$examiner->exmCertNo = $per["exmCertNo"];
				
				if($per["exmTime"] != ""){
					$tA = explode("-", $per["exmTime"]);
					$a1 = $tA[0];
					if(strlen($tA[1])==1){
						$a1 .= "-0".$tA[1];
					}else{
						$a1 .="-".$tA[1];
					}
					if(strlen($tA[2])==1){
						$a1 .= "-0".$tA[2];
					}else{
						$a1 .="-".$tA[2];
					}
					$examiner->exmTime = $a1;
				}else{
					$examiner->exmTime = '';
				}
				
				$examiner->recID = $recID;
				$exmAttrArr = explode('=', $per["exmAttr"]);
				$examiner->exmAttr = $exmAttrArr[0];
				if($examiner->save()){
					
				}else{
					throw new NotFoundHttpException();
				}
			}
			return $this->jsonReturn(['result'=>1,'msg'=>'导入成功！']);	
		}
	}
	
	public function actionExaminerGroupList(){
		$request = Yii::$app->request;
		
		$recID = $request->post('recID');
		$sort = $request->post("sort"); 
        $order = $request->post("order","asc");
        
        if($sort == 'gstStartEnd'){
        	$sort = 'gstItvStartTime';
        }
        if($sort){
	        $orderInfo = $sort.' '.$order;
        }else{
        	$orderInfo = 'gstItvStartTime asc,gstGroup asc';
        }
		$result = [];
		
		$total = Setgroup::find()->where(['recID'=>$recID,'gstType'=>1])->asArray()->count();
		$dataInfos = Setgroup::find()->where(['recID'=>$recID,'gstType'=>1])->orderby($orderInfo)->asArray()->all();
		
		$examiner_num = Examiner::find()->where(['recID'=>$recID])->asArray()->count();
		
		$examiner_num_deal = Gstexm::find()->groupBy(['exmID'])->where(['recID'=>$recID])->count();
		
		$result["examiner_num"] = $examiner_num;
		$result["examiner_num_deal"] = $examiner_num_deal;
		$result["examiner_num_nodeal"] = intval($examiner_num)-intval($examiner_num_deal);
		
		$tempData = [];
		if(!empty($dataInfos)){
			$index = 0;
			foreach($dataInfos as $info){
				$codes = [['gstGroup','ZBMC']];
				$mainCode = Share::codeValue($codes,$info);
				
				$infoData = Yii::$app->db->createCommand('select examiner.exmName from gstexm left join examiner  on examiner.exmID=gstexm.exmID where gstexm.gstID=:gstID and gstexm.recID=:recID')
								           ->bindValue(':gstID', $info['gstID'])
								           ->bindValue(':recID', $recID)
								           ->queryAll();
				
				$str = "";
				if(!empty($infoData)){
					$count_num = count($infoData); 
					for($i = 0 ; $i < $count_num ; $i++){
						if($i == $count_num - 1){
							$str .=$infoData[$i]['exmName'];
						}else{
							$str .=$infoData[$i]['exmName']."、";
						}
					}
				}	
				$tempData[$index]['gstID'] = $info['gstID'];
				$tempData[$index]['gstStartEnd'] = $info['gstStartEnd'];
				$tempData[$index]['gstGroup'] = $mainCode['gstGroup'];
				$tempData[$index]['gstItvPlace'] = $info['gstItvPlace'];
				$tempData[$index]['exmNames'] = $str;
				$index++;			
			}
		}
		
		$result["rows"] = $tempData;
		$result["total"] = $total;
		
		return $this->jsonReturn($result);
	}

	public function actionExaminerDownload(){
		$recID = Yii::$app->request->get('recID');
		
		$recInfo = Recruit::find()->where(['recID'=>$recID])->asArray()->one();
		$codeInfo = Share::codeValue([['recBatch','PC']],$recInfo);
		
		$fileTitle = $recInfo['recYear']."年".$codeInfo['recBatch']."XXXXXX人才招聘签到表（考官）";
		
		$infos = (new \yii\db\Query())
    				->select("setgroup.gstGroup, setgroup.gstStartEnd,examiner.exmType,examiner.exmName")
    				->from('gstexm')
					->leftJoin('examiner', 'examiner.exmID = gstexm.exmID')
					->leftJoin('setgroup','setgroup.gstID = gstexm.gstID')
					->where(['gstexm.recID'=>$recID])
					->orderBy('setgroup.gstGroup asc,setgroup.gstStartEnd asc,examiner.exmType asc')
					->all();
					
		$dataJson = [];
		foreach($infos as $info){
			$codes = [['exmType','KGLB'],['gstGroup','ZBMC']];
			$mainCode = Share::codeValue($codes,$info);
			$dataJson [] = array_merge($info,$mainCode);
		}
		
		@ini_set('memory_limit', '2048M');
		set_time_limit(0);
		error_reporting(E_ALL);
		date_default_timezone_set('PRC');
		$fileName = $fileTitle.date('Y-m-d',time()).time();
		$excelInfo = Share::getKeyInfo('flow4_step3_mb');
		
		$objPHPExcel = \PHPExcel_IOFactory::createReader("Excel5")->load($excelInfo['tempExcel']);
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle($excelInfo['keys'][0]['sheetName']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $fileTitle);
		
		$index = 0;
		foreach($excelInfo['keys'] as $v){
			$objPHPExcel -> getSheet($v['index']) -> setTitle($v['sheetName']);
			$dataInfos = $dataJson;
			$num = $v['num'];
			$keys = $v['key'];
			foreach($dataInfos as $info){
				$column = count($keys);
				$temp = 0;
				for($n = 0; $n < $column; $n++){
					if($temp == $column){
						break;
					}else{
						$pcoordinate = \PHPExcel_Cell::stringFromColumnIndex($n).''.$num;
						if($keys[$temp] == 'id'){
							$objPHPExcel->setActiveSheetIndex($v['index'])->setCellValue($pcoordinate,  ($num-2) );
						}else{
							$objPHPExcel->setActiveSheetIndex($v['index'])->setCellValue($pcoordinate, ' ' . $info[$keys[$temp]] . ' ');
						}
			            $temp++;
					}
					//$objPHPExcel->getActiveSheet(0)->getStyle($pcoordinate)->applyFromArray(Share::ExcelStyleArrayInfoSet(3));
				}
				$num++;
			}
			$index++;
		}

		ob_end_clean();
		$fileName = iconv("utf-8","gb2312",$fileName);
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="'.$fileName.'.xls"'); 
		header ( 'Cache-Control: max-age=0' );
		$objWriter = \PHPExcel_IOFactory::createWriter ($objPHPExcel,'Excel5'); 
		$objWriter->save ( 'php://output' );
		exit;
	}

	public function actionExaminerExportStep3(){
		$request = Yii::$app->request;
		$recID = $request->get('recID');
		
		$dataInfos = Setgroup::find()->where(['recID'=>$recID,'gstType'=>1])->orderby('gstItvStartTime asc,gstGroup asc')->asArray()->all();
		
		$tempData = [];
		if(!empty($dataInfos)){
			$index = 0;
			foreach($dataInfos as $info){
				$codes = [['gstGroup','ZBMC']];
				$mainCode = Share::codeValue($codes,$info);
				
				$infoData = Yii::$app->db->createCommand('select examiner.exmName from gstexm left join examiner  on examiner.exmID=gstexm.exmID where gstexm.gstID=:gstID and gstexm.recID=:recID')
								           ->bindValue(':gstID', $info['gstID'])
								           ->bindValue(':recID', $recID)
								           ->queryAll();
				
				$str = "";
				if(!empty($infoData)){
					$count_num = count($infoData); 
					for($i = 0 ; $i < $count_num ; $i++){
						if($i == $count_num - 1){
							$str .=$infoData[$i]['exmName'];
						}else{
							$str .=$infoData[$i]['exmName']."、";
						}
					}
				}	
				$tempData[$index]['id'] = $info['gstID'];
				$tempData[$index]['gstStartEnd'] = $info['gstStartEnd'];
				$tempData[$index]['gstGroup'] = $mainCode['gstGroup'];
				$tempData[$index]['gstItvPlace'] = $info['gstItvPlace'];
				$tempData[$index]['exmNames'] = $str;
				$index++;			
			}
			Share::exportCommonExcel(['sheet0'=>['data'=>$tempData],'key'=>'flow4_step3_export','fileInfo'=>['fileName'=>'考官分组安排表']]);
		}
	}

	public function actionExaminerSendmsgList(){
		$request = Yii::$app->request;
		$recID = $request->get('recID');
		
		$infos = (new \yii\db\Query())
    				->select("gstexm.exmID,examiner.exmName,examiner.exmType,examiner.exmAttr,examiner.exmPhone")
    				->from('gstexm')
					->leftJoin('examiner', 'examiner.exmID = gstexm.exmID')
					->where(['gstexm.recID'=>$recID])
					->groupBy(['gstexm.exmID'])
					->orderBy('examiner.exmAttr')
					->all();
		$index = 0;
		foreach($infos as $info){
			$codes = [['exmType','KGLB'],['exmAttr','KGSX']];
			$mainCode = Share::codeValue($codes,$info);
			
			$infoData = (new \yii\db\Query())
	    				->select("setgroup.gstStartEnd,setgroup.gstGroup,setgroup.gstItvPlace")
	    				->from('gstexm')
						->leftJoin('setgroup', 'setgroup.gstID = gstexm.gstID')
						->where(['gstexm.recID'=>$recID,'gstexm.exmID'=>$info['exmID']])
						->orderBy('setgroup.gstStartEnd')
						->all();
			
			
			
			$str = "";
			if(!empty($infoData)){
				foreach($infoData as $df){
					$code_temp = [['gstGroup','ZBMC']];
					$codes_info = Share::codeValue($code_temp,$df);
					$str .= '【考试组别：'.$codes_info['gstGroup'].'、考试时间：'.$df['gstStartEnd'].'、考试地点：'.$df['gstItvPlace'].'】';
				}
			}	
			$tempData[$index]['exmID'] = $info['exmID'];
			$tempData[$index]['exmName'] = $info['exmName'];
			$tempData[$index]['exmType'] = $mainCode['exmType'];
			$tempData[$index]['exmAttr'] = $mainCode['exmAttr'];
			$tempData[$index]['exmPhone'] = $info['exmPhone'];
			$tempData[$index]['exmContent'] = $str;
			$index++;
		}
		return $this->jsonReturn(['rows'=>$tempData]);
	}

	public function actionExaminerSendmsgDo(){
		$request = Yii::$app->request;
		$phones = $request->post('exmPhones');
		$exmContents = $request->post('exmContents');
		$content = $request->post('content');
		
		$juheKey = Yii::$app->params['juhe_key'];//您申请的APPKEY
		$tpl_id = Yii::$app->params['juhe_tpl_id'];//您申请的短信模板ID，根据实际情况修改
		$tpl_value = '#content#='.$content;//您设置的模板变量，根据实际情况修改
		
		$len = count($phones);
		$flag = 0;
		$msg_error = '失败发送：<br/>';
		$msg_success = '发送成功：<br/>';
		$_msg_success_temp = '发送成功：<br/>';
		for($i = 0; $i < $len ; $i++){
			$smsConf = [
			    'key'   => $juheKey, 
			    'mobile'    => $phones[$i], 
			    'tpl_id'    => $tpl_id, 
			    'tpl_value' =>$tpl_value.'@@@'.$exmContents[$i] 
			];
			$responseContent = Share::juhecurl($smsConf,1); //请求发送短信
			if($responseContent){
				$result = json_decode($responseContent,true);
    			$error_code = $result['error_code'];
				if($error_code != 0){
					$flag++;
					$msg_error .= "手机号码=".$phones[$i]."；失败原因：". $result['reason']."<br/>";
				}else{
					$msg_success .= "手机号码=".$phones[$i]."<br/>";
				}
			}else{
				$msg_error .= "手机号码=".$phones[$i]."；失败原因：请求失败<br/>";
				$flag++;
			}
		}
		if($flag){
			$msg = "";
			if($_msg_success_temp == $msg_success){
				$msg = $msg_error;
			}else{
				$msg = $msg_error.'<br/>'.$msg_success;
			}
			$result = ['result'=>0,'msg'=>$msg];
		}else{
			$result = ['result'=>1,'msg'=>'发送成功'];
		}
		return $this->jsonReturn($result);
	}
	
	public function actionExaminerTreeList(){
		$request = Yii::$app->request;
		
		$recID = $request->get('recID');
		$gstID = $request->get('gstID');
		
		$result = [];
		$resultInfo = [];
		
		$examiner_all = Examiner::find()->where(['recID'=>$recID])->asArray()->count();
		$examiner_type1 = Examiner::find()->where(['recID'=>$recID,'exmType'=>1])->asArray()->count();
		$examiner_type2 = Examiner::find()->where(['recID'=>$recID,'exmType'=>1])->asArray()->count();
		$examiner_type3 = Examiner::find()->where(['recID'=>$recID,'exmType'=>3])->asArray()->count();
		
		$type_info1 = Examiner::find()->where(['recID'=>$recID,'exmType'=>1])->asArray()->all();
		$type_info2 = Examiner::find()->where(['recID'=>$recID,'exmType'=>2])->asArray()->all();
		$type_info3 = Examiner::find()->where(['recID'=>$recID,'exmType'=>3])->asArray()->all();
		
		$group_info = Gstexm::find()->where(['recID'=>$recID,'gstID'=>$gstID])->asArray()->all();
		$result['group_info'] = $group_info;
		
		foreach($type_info1 as $data){//主考官
			$str1 = "";
			$infos1 = (new \yii\db\Query())
    				->select("code.codeName")
    				->from('gstexm')
					->leftJoin('setgroup', 'setgroup.gstID = gstexm.gstID')
					->leftJoin('code', "code.codeID = setgroup.gstGroup and code.codeTypeID = 'ZBMC' ")
					->where(['gstexm.recID'=>$recID,'gstexm.exmID'=>$data['exmID']])
					->all();
			if(!empty($infos1)){
				$len1 = count($infos1);
				for($i = 0 ; $i < $len1 ; $i++){
					if($i == $len1 - 1){
						$str1 .= $infos1[$i]['codeName'];
					}else{
						$str1 .= $infos1[$i]['codeName']."、";
					}
				}
			}else{
				$str1 = "未安排";
			}
			
			$resultInfo[] = [
				'id'=>$data['exmID'],
				'name'=>$data['exmName']."（".$str1."）",
				'pId'=>'-2',
				'isChild'=>1,
				'type'=>1
			];
		}
		
		foreach($type_info2 as $data){//其他考官
			$str2 = "";
			$infos2 = (new \yii\db\Query())
    				->select("code.codeName")
    				->from('gstexm')
					->leftJoin('setgroup', 'setgroup.gstID = gstexm.gstID')
					->leftJoin('code', "code.codeID = setgroup.gstGroup and code.codeTypeID = 'ZBMC' ")
					->where(['gstexm.recID'=>$recID,'gstexm.exmID'=>$data['exmID']])
					->all();
			if(!empty($infos2)){
				$len2 = count($infos2);
				for($i = 0 ; $i < $len2 ; $i++){
					if($i == $len2 - 1){
						$str2 .= $infos2[$i]['codeName'];
					}else{
						$str2 .= $infos2[$i]['codeName']."、";
					}
				}
			}else{
				$str2 = "未安排";
			}
			
			$resultInfo[] = [
				'id'=>$data['exmID'],
				'name'=>$data['exmName']."（".$str2."）",
				'pId'=>'-3',
				'isChild'=>1,
				'type'=>1
			];
		}
		
		foreach($type_info3 as $data){//监督员
			$str3 = "";
			$infos3 = (new \yii\db\Query())
    				->select("code.codeName")
    				->from('gstexm')
					->leftJoin('setgroup', 'setgroup.gstID = gstexm.gstID')
					->leftJoin('code', "code.codeID = setgroup.gstGroup and code.codeTypeID = 'ZBMC' ")
					->where(['gstexm.recID'=>$recID,'gstexm.exmID'=>$data['exmID']])
					->all();
			if(!empty($infos3)){
				$len3 = count($infos3);
				for($i = 0 ; $i < $len3 ; $i++){
					if($i == $len3 - 1){
						$str3 .= $infos3[$i]['codeName'];
					}else{
						$str3 .= $infos3[$i]['codeName']."、";
					}
				}
			}else{
				$str3 = "未安排";
			}
			
			$resultInfo[] = [
				'id'=>$data['exmID'],
				'name'=>$data['exmName']."（".$str3."）",
				'pId'=>'-4',
				'isChild'=>1,
				'type'=>1
			];
		}

        $resultInfo[] = ["id" => "-1", "name" => '考官总数（'.$examiner_all.'）', "pId" => "-1", "isParent" => "true", 'isChild'=>0,"type" => "-1"];
		$resultInfo[] = ["id" => "-2", "name" => '主考官（'.$examiner_type1.'）', "pId" => "-1", "isParent" => "true", 'isChild'=>0,"type" => "-2"];
		$resultInfo[] = ["id" => "-3", "name" => '其他考官（'.$examiner_type2.'）', "pId" => "-1", "isParent" => "true", 'isChild'=>0,"type" => "-3"];
		$resultInfo[] = ["id" => "-4", "name" => '监督员（'.$examiner_type3.'）', "pId" => "-1", "isParent" => "true", 'isChild'=>0,"type" => "-4"];
        $result['treeInfo'] = $resultInfo;
        return $this->jsonReturn($result);
	}
	
	public function actionExaminerChooseList(){
		$request = Yii::$app->request;
		
		$recID = $request->get('recID');
		$gstID = $request->get('gstID');
		
		$rows = (new \yii\db\Query())
    				->select("gstexm.gstexmID,code.codeName,examiner.exmName,examiner.exmType")
    				->from('gstexm')
					->leftJoin('setgroup', 'setgroup.gstID = gstexm.gstID')
					->leftJoin('code', "code.codeID = setgroup.gstGroup and code.codeTypeID = 'ZBMC' ")
					->leftJoin('examiner', 'examiner.exmID = gstexm.exmID')
					->where(['gstexm.recID'=>$recID,'gstexm.gstID'=>$gstID])
					->all();
		
		$total = Gstexm::find()->where(['recID'=>$recID,'gstID'=>$gstID])->asArray()->count();
		
		$result['rows'] = $rows;
		$result['total'] = $total;
		
        return $this->jsonReturn($result);
	}
	
	public function actionExaminerChooseDo(){
		$request = Yii::$app->request;
		$recID = $request->post('recID');
		$gstID = $request->post('gstID');
		$gstStartEnd = $request->post('gstStartEnd');
		$exmIDs = $request->post('exmIDs');
		
		$data_exist_info = Setgroup::find()->where(['AND',["recID"=>$recID,"gstStartEnd"=>$gstStartEnd],['not',['gstID'=>$gstID]]])->asArray()->all();
		
		if(!empty($data_exist_info)){
			$gstIDs = [];
			foreach($data_exist_info as $data){
				$gstIDs[] = $data['gstID'];
			}
			
			$num = Gstexm::find()->where(['recID'=>$recID,'gstID'=>$gstIDs,'exmID'=>$exmIDs])->asArray()->count();
			
			if($num > 0){
				return $this->jsonReturn(['result'=>0,'msg'=>'勾选的考官中存在同一时间已被安排']);
			}
		}

		$insertData = [];
		
		$len = count($exmIDs);
		for($i = 0 ; $i < $len ; $i++){
			$tempData = [];
			array_push($tempData,$gstID,$recID,$exmIDs[$i]);
			$insertData[$i] = $tempData;
		}
		
		$flag = Yii::$app->db->createCommand()->batchInsert(Gstexm::tableName(), ['gstID', 'recID','exmID'], $insertData)->execute();
		if($flag){
			$result = ['result'=>1,'msg'=>'安排成功'];
		}else{
			$result = ['result'=>0,'msg'=>'安排失败'];
		}
		
		return $this->jsonReturn($result);
	}
	
	public function actionExaminerChooseDel(){
		$gstexmIDs = Yii::$app->request->post('gstexmIDs');
		if(Gstexm::deleteAll(['gstexmID'=>$gstexmIDs])){
			$result = ['result'=>1,'msg'=>'删除成功'];
		}else{
			$result = ['result'=>0,'msg'=>'删除失败'];
		}
		return $this->jsonReturn($result);
	}
	
	public function actionExaminerChooseHinfo(){
		$recID = Yii::$app->request->post('recID');
		
		$examiner_num = Examiner::find()->where(['recID'=>$recID])->asArray()->count();
		
		$examiner_num_deal = Gstexm::find()->groupBy(['exmID'])->where(['recID'=>$recID])->count();
		
		$result["sy"] = $examiner_num;
		$result["yap"] = $examiner_num_deal;
		$result["wap"] = intval($examiner_num)-intval($examiner_num_deal);
		
		return $this->jsonReturn($result);
	}
}
