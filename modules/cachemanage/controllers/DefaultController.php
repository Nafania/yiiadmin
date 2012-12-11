<?php

class DefaultController extends YAdminController
{
    public function  init() {
	$this->breadcrumbs[] = CHtml::link(Yii::t('CachemanageModule', 'Управление кешэм'), array('/yiiadmin/cachemanage'));
	parent::init();
    }

	public function actionIndex()
	{
	    $FiltersForm = new FiltersForm;
	    if ( isset($_GET['FiltersForm']) ) {
			$FiltersForm->filters = $_GET['FiltersForm'];
	    }

	    $CacheInfo = new CacheInfo();
	    $cacheData = $CacheInfo->getCacheData();
	    $columns = $CacheInfo->getColumns();
	    $keyField = $CacheInfo->getKeyField();

	    $dataProvider = new CArrayDataProvider($cacheData, array(
		'sort' => array(
		    'attributes' => $columns,
		),
		'keyField' => $keyField,
	    ));
	    
	    $columns = CMap::mergeArray($columns, array(array(
		'class' => 'YiiAdminButtonColumn',
		'buttons' => array(
		    'delete' => array(
			'url' => 'Yii::app()->createUrl("/yiiadmin/cachemanage/default/delete", array("key" => $data["' . $keyField . '"]))',
		    ),
		),
		'template' => '{delete}'
	    )));

	    $this->pageTitle = Yii::t('CachemanageModule', 'Управление кешэм');


            $this->render('index', array(
	        'dataProvider' => $FiltersForm->filter($dataProvider),
	        'filtersForm' => $FiltersForm,
	        'columns' => $columns,
	    ));
	}
	
	public function actionDelete ( $key ) {
	    $CacheInfo = new CacheInfo();
	    $CacheInfo->deleteKey($key);	    
	}

	public function actionFlush() {
	    $CacheInfo = new CacheInfo();
	    $CacheInfo->flush();
	    
	    Yii::app()->user->setFlash('flashMessage', Yii::t('CachemanageModule', 'Кэш сброшен'));
	    $this->redirect('./');
	}
}