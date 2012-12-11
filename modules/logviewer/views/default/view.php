<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'itemGrid',
    'dataProvider' => $items,
    'columns' => array(
		'date',
		'eventType',
		'component',
	    array(
	              'name' => 'description',
	  	        'type' => 'raw',
	              'value' => '$data["description"]',

	          ),
		array(
            'name' => 'trace',
	        'type' => 'raw',
            'value' => '$data["trace"]',

        ),
	    'http_referer',
    ),

));
?>