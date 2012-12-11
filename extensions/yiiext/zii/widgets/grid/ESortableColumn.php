<?php
Yii::import('zii.widgets.grid.CGridColumn');

class ESortableColumn extends CDataColumn
{
    public $ajaxUrl = '/yiiadmin/manageModel/reOrder';
    
    public function init() {
	parent::init();
	
	$this->grid->htmlOptions = CMap::mergeArray($this->grid->htmlOptions, array(
	    'class' => 'ui-sortable'
	));
	
	$cs = Yii::app()->getClientScript();
	$cs->registerCoreScript('jquery.ui');
	
	$this->ajaxUrl = Yii::app()->createUrl($this->ajaxUrl);
	$model = $this->grid->dataProvider->modelClass;
	$gridId = $this->grid->getId();
	
$js = <<<EOD
jQuery.fn.sortableCGrid = function() {
	$(this).sortable({
	axis: "y",
	containment: "parent",
	cursor: "pointer",
	delay: 100,
	distance: 5,
	forcePlaceholderSize: true,
	tolerance: "pointer",
	update: function (event, ui) {
	    var arr = [];
	    var elems = $("#{$gridId} table tbody tr .pk");
	    $.each(elems, function(key) {
		var pk = $(this).attr('id').split('_')[1];
		arr[key] = pk;
	    });
	    $.ajax({
		url: "{$this->ajaxUrl}",
		type: "POST",
		data: ({pk : arr, model: '{$model}', orderField: '{$this->name}'}),
		dataType: "json",
		success: function(msg){
		    $('#{$gridId}').yiiGridView.settings['{$gridId}'].afterAjaxUpdate = function(id, data){\$('#' + id + ' table tbody').sortableCGrid()};
		    $('#{$gridId}').yiiGridView.update('{$gridId}');
		}
	    });
	}
}).disableSelection()};
$("#{$gridId} table tbody").sortableCGrid();
EOD;

	Yii::app()->getClientScript()->registerScript('gridSortable', $js, CClientScript::POS_END);
    }   
    
    public function renderDataCell($row) {
	$data = $this->grid->dataProvider->data[$row];
	$options = $this->htmlOptions;
	$pk = $this->grid->dataProvider->data[$row]->getPrimaryKey();
	if( $this->cssClassExpression !== null ) {
	    $class = $this->evaluateExpression($this->cssClassExpression,array('row'=>$row,'data'=>$data));
	    if( isset($options['class']) ) {
		$options['class'] .= 'pk '. $class;
	    }
	    else {
		$options['class'] = $class;
	    }
	}
	else {
	    $options['class'] = 'pk';
	}
	
	$options['id'] = 'pk_' . $pk;
	
	echo CHtml::openTag('td',$options);
	$this->renderDataCellContent($row,$data);
	echo '</td>';
    }    
}
