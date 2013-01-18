<?php
$this->pageTitle = $title;
?>

<ul class="tools">
    <li> 
       <?php echo CHtml::link( YiiadminModule::t( 'Создать' ) . ' ' . $this->module->getObjectPluralName( $model, 0 ), $this->createUrl( 'manageModel/create', array( 'model_name' => get_class( $model ) ) ), array( 'class' => 'add-handler focus' ) ); ?>

    </li> 
    </ul>

<?php

$this->widget( 'zii.widgets.grid.CGridView',
	$listData
);

$this->widget( 'application.modules.' . $this->module->name . '.widgets.manageSelected.manageSelected',
	array(
		'path' => $this->module->name,
		'model' => $model,
		'gridId' => 'objects-grid',
	)
);
?>
