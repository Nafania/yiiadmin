------------------------------------------------------------------------
Manage Selected Rows Widget
------------------------------------------------------------------------
Version: 0.2
Created on 30/07/2010
Last Modified on 02/08/2010
Created by Cherashev Feodor aka Mr.Cherry
------------------------------------------------------------------------

Installation:
Copy `extensions` and `controllers` into your `protected directory`

Usage:
<?php 
$grid = $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'content-fields-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'selectableRows'=>2,
	'columns'=>array(
		'id',
		'name',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); 

$this->widget('ext.manageSelected.manageSelected', 
	array(
		'controller'=>$this->id, 
		'gridId'=>$grid->id, 
		'buttons'=>array('delete','publish','unpublish')
	)
);

?>

