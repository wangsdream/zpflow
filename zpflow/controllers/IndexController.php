<?php
namespace app\controllers;
use yii\web\Controller;
use yii\helpers\Html;
use Yii;

use app\models\Recruit;

class IndexController extends BaseController{
	public $enableCsrfValidation = false;
	/*首页*/
	public function actionIndex(){
		return $this->render('index');
	}
	
	public function actionNotice(){
		return $this->render('notice');
	}
	
	public function actionStatistics(){
		return $this->render('statistics');
	}
	
	public function actionSystem(){
		return $this->render('system');
	}
	
	/*人才招聘子页面*/
	public function actionRczp(){
		$index = Html::encode(Yii::$app->request->get('index'));
		$info = [];
		if(intval($index) != 1){
			$info = Recruit::getOverRecBatch();
		}
		return $this->renderPartial('rczp/flow'.Html::decode($index),['pcInfo'=>$info]);
	}
}
