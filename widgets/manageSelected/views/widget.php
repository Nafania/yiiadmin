<div class="module grid-view">
    <ul class="submit-row" style="margin-left: 10px">
        <li class="left submit-button-container"><a class="selectAll cancel-link"><?php echo Yii::t('main','Select All')?></a></li>
        <li class="left submit-button-container"><a class="selectNone cancel-link"><?php echo Yii::t('main','Select None')?></a></li>
	<?php if( isset($buttons['delete_btn']))  : ?><li class="left submit-button-container"><a id="deleteSelected" class="delete-link" href="<?php echo Yii::app()->controller->createUrl($controller."/deleteSelected/")?>"><?php echo Yii::t('main','Delete Selected')?></a></li><?php endif; ?>
	<?php if( isset($buttons['publish_btn'])) : ?><li><a class="button" id="publishSelected" href="<?php echo Yii::app()->controller->createUrl($controller."/publishSelected/")?>"><?php echo Yii::t('main','Publish Selected')?></a></li><?php endif; ?>
	<?php if( isset($buttons['unpublish_btn'])) : ?><li><a class="button" id="unpublishSelected" href="<?php echo Yii::app()->controller->createUrl($controller."/unpublishSelected/")?>"><?php echo Yii::t('main','Unpublish Selected')?></a></li><?php endif; ?>	
    </ul>	
    <br clear="all" />
</div>