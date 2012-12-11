<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'grid',
    'dataProvider' => $dataProvider,
    'filter' => $filtersForm,
    
    'columns' => $columns,
));
?>

<div class="submit-row" style="width:200px;">
<?php
echo CHtml::link(Yii::t('CachemanageModule', 'Сбросить весь кэш'), $this->createUrl('default/flush'), array('class' => 'delete-link'));
?>
</div>