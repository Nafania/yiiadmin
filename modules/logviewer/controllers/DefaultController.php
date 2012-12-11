<?php

class DefaultController extends YAdminController
{
    public function init() {
	$this->breadcrumbs[] = CHtml::link(Yii::t('Logviewermodule', 'Просмотр логов'), array('/yiiadmin/logviewer'));
    }

	public function actionIndex()
	{
	    $this->pageTitle = Yii::t('Logviewermodule', 'Просмотр логов');

	    if ( !empty(Yii::app()->log->routes) ) {

		$_routes = Yii::app()->log->routes;

		foreach ( $_routes AS $logClass ) {

		    if ( $logClass instanceof CFileLogRoute ) {
			$this->run('CFileLogRoute');
		    }
		}
	    }
	}

	public function actionCFileLogRoute() {
	    $LogParser = new LogParser();
	    $logFiles = $LogParser->getLogFilesAsArray();

	    $logFilesProvider = new CArrayDataProvider($logFiles, array(
			'sort'=>array(
		        'attributes'=>array(
					'fileName',
					'mtime',
					'fileSize'
		        ),
		        'defaultOrder' => array(
					'mtime' => 'mtime DESC'
		        )
			),
			'keyField'   => 'fileName',
			'pagination' => array(
		        'pageSize' => 20,
		    )
		));


	    $this->render('index', array(
			'logFiles' => $logFilesProvider,
	    ));
	    
	}

	public function actionView ( $logFileName ) {
	    $logFileName = base64_decode($logFileName);

	    $this->breadcrumbs[] = $logFileName;
	    $this->pageTitle = Yii::t('Logviewermodule', $logFileName);

	    $LogParser = new LogParser();

	    $logData = $LogParser->readLogFile($logFileName);

	    $itemsProvider = new CArrayDataProvider($logData, array(
			'sort'=>array(
		        'attributes'=>array(
					'date',
					'eventType',
					'component',
					'description',
					'trace',
			        'http_referer'
		        ),
		        'defaultOrder' => array(
					'date' => 'date DESC'
		        )
			),

			'keyField'   => 'date',
			'pagination' => array(
		        'pageSize' => 20,
		    )
		));


	    $this->render('view', array(
			'items' => $itemsProvider,
	    ));
	}

	public function actionDelete ( $logFileName ) {
	    $logFileName = base64_decode($logFileName);

	    $this->pageTitle = Yii::t('Logviewermodule', $logFileName);

	    $LogParser = new LogParser();
	    $deleted = $LogParser->deleteLogFile($logFileName);

	    if ( $deleted ) {
		$msg = Yii::t('Logviewermodule', 'Файл удален успешно');
	    }
	    else {
		$msg = Yii::t('Logviewermodule', 'При удалении файла произошла ошибка');
	    }

	    Yii::app()->user->setFlash('flashMessage', $msg);
	    $this->redirect($this->createUrl(Yii::app()->getParentModule()->name));
	}
}