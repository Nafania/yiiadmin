<div id="queryResult"></div>

<?php
echo CHtml::form();
echo CHtml::textArea('query') . '<br />';
echo CHtml::ajaxSubmitButton('Submit', Yii::app()->createUrl('/yiiadmin/sqladmin/default/query'), array('type' => 'POST', 'update' => '#queryResult'));
echo CHtml::endForm();
?>