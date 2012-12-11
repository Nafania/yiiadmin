<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'logFilesGrid',
    'dataProvider' => $logFiles,
    'columns' => array(
	'fileName',
	array(
	    'name' => 'mtime',
	    'value' => 'date("Y-m-d H:i:s", $data["mtime"])'
	),
	'fileSize',
        array(
            'class'=>'YiiAdminButtonColumn',
	    'buttons' => array(
		'view' => array(
		    'url' => 'Yii::app()->createUrl("/yiiadmin/logviewer/default/view", array("logFileName" => base64_encode($data["fileName"])))',
		),
		'delete' => array(
		    'url' => 'Yii::app()->createUrl("/yiiadmin/logviewer/default/delete", array("logFileName" => base64_encode($data["fileName"])))',
		),
	    ),
	    'template' => '{view} {delete}'
        ),
    )
));
?>