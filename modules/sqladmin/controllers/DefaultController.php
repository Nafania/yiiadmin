<?php

class DefaultController extends YAdminController
{
    public function  init() {
	$this->breadcrumbs[] = CHtml::link(Yii::t('SqladminModule', 'Управление базой данных'), array('/yiiadmin/sqladmin'));
	parent::init();
    }

    public function actionIndex() {
	$this->pageTitle = Yii::t('SqladminModule', 'Управление базой данных');
	$this->render('index', array());
    }
    
    public function actionQuery ( ) {
	if ( Yii::app()->request->getIsAjaxRequest() ) {
	    $query = Yii::app()->request->getPost('query', '');
	    
	    if ( !$query ) {
		throw new CHttpException('404');
	    }
	    
	    $queries = explode(';', $query);
	    $queries = array_filter($queries);

	    $connection = Yii::app()->db;
	    
	    foreach ( $queries AS $sql ) {
		echo $sql . '<br />';
		$command = $connection->createCommand($sql);
		$rowCount = $command->execute();
		
		echo 'Rows affected ' . $rowCount . '<br />';
	    }	    
	}
    }
}