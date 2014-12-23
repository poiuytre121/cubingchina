<?php

class ResultsController extends Controller {
	protected $logAction = false;

	public function accessRules() {
		return array(
			array(
				'allow',
				'users'=>array('*'),
			),
		);
	}

	protected function beforeAction($action) {
		Yii::import('application.statistics.*');
		if (parent::beforeAction($action)) {
			$this->breadcrumbs = array(
				'Results'=>array('/results/index'),
				ucfirst($this->action->id),
			);
			return true;
		}
	}

	public function actionIndex() {
		$this->breadcrumbs = array(
			'Results',
		);
		$this->title = 'Results';
		$this->pageTitle = array('Results');
		$this->description = Yii::t('statistics', 'Welcome to the Cubing China results page, where you can find the Chinese personal rankings, official records, and fun statistics.');
		$this->setWeiboShareDefaultText('粗饼的官方成绩页面，包含中国魔方选手的个人排名、官方纪录与趣味统计等信息。');
		$this->render('index');
	}

	public function actionRankings() {
		$type = $this->sGet('type', 'single');
		$event = $this->sGet('event', '333');
		$gender = $this->sGet('gender', 'all');
		$page = $this->iGet('page', 1);
		$rankings = Results::getRankings($type, $event, $gender, $page);
		$this->title = 'Personal Rankings';
		$this->pageTitle = array('Personal Rankings');
		$this->description = Yii::t('statistics', 'Chinese personal rankings in each official event are listed, based on the the official WCA rankings.');
		$this->setWeiboShareDefaultText('中国魔方选手在各官方项目的个人成绩排名展示');
		$this->render('rankings', array(
			'rankings'=>$rankings,
			'type'=>$type,
			'event'=>$event,
			'gender'=>$gender,
			'page'=>$page,
		));
	}

	public function actionRecords() {
		$type = $this->sGet('type', 'current');
		$region = $this->sGet('region', 'China');
		$event = $this->sGet('event', '333');
		$records = Results::getRecords($type, $region, $event);
		$this->title = 'Official Records';
		$this->pageTitle = array('Official Records');
		$this->description = Yii::t('statistics', 'World, Asian and Chinese records are displayed on the page, based on the official WCA records.');
		$this->setWeiboShareDefaultText('世界魔方协会（WCA）所有官方项目的纪录展示');
		$this->render('records', array(
			'records'=>$records,
			'type'=>$type,
			'region'=>$region,
			'event'=>$event,
		));
	}

	public function actionStatistics() {
		$name = $this->sGet('name');
		if (in_array(preg_replace_callback('{(\b|-)(\w)}', function($matches) {
			return strtoupper($matches[2]);
		}, $name), array_map(function($statistic) {
			return $statistic['class'];
		}, Statistics::$lists))) {
			echo 1;exit;
		}
		$data = Statistics::getData();
		extract($data);
		$this->pageTitle = array('Fun Statistics');
		$this->title = 'Fun Statistics';
		$this->description = Yii::t('statistics', 'Based on the official WCA competition results, we generated several WCA statistics about Chinese competitions and competitors, which were regularly up-to-date.');
		$this->setWeiboShareDefaultText('关于中国WCA官方比赛及选手成绩的一系列趣味统计', false);
		$this->render('statistics', array(
			'statistics' => $statistics,
			'time' => $time,
		));
	}
}