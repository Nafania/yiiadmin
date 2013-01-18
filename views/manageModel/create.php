<?php
$this->pageTitle = $title;
?>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => get_class($model) . '-id-form',
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data'
    )
));
?>

<div class="container-flexible">
    <?php if ($model->hasErrors()): ?>
    <p class="errornote"><?php echo YiiadminModule::t('Пожалуйста, исправьте ошибки, указанные ниже.'); ?></p>
    <?php endif; ?>

    <div class="">
        <div class="column span-16">
            <h3><?php echo Yii::t(get_class($model), get_class($model)) ?></h3>
            <fieldset class="module">
                <?php
                foreach ($attributes as $attribute):
                    ?>
                    <div class="row <?php if ($model->getError($attribute)) echo 'errors'; ?>">
                        <div class="column span-4"><?php echo $form->labelEx($model, $attribute); ?></div>
                        <div class="column span-12 span-flexible">
                            <?php echo $this->module->createWidget($form, $model, $attribute); ?>
                            <ul class="errorlist">
                                <li><?php echo $form->error($model, $attribute); ?></li>
                            </ul>
                        </div>
                    </div>
                    <?php endforeach; ?>
            </fieldset>

        </div>

        <div class="column span-8 last">

            <?php
            foreach ($relatedModels AS $relationName => $relatedModelsAry) {
                echo '<h3>' . Yii::t(ucfirst($relationName), $relationName) . '</h3>';
                $i = 0;

                foreach ($relatedModelsAry AS $relatedModel) {
                    $relData = $relatedModel->relations(get_class($model));
                    ?>
                    <fieldset class="module model<?php echo get_class($relatedModel) ?>">
                        <div class="row">
                            <ul class="actions tools">
                                <?php
                                $relModelData = $model->relations(get_class($relatedModel));
                                if ($relModelData[key($relModelData)][0] == 'CHasManyRelation') {
                                    echo '<li>' . CHtml::link(YiiadminModule::t('Добавить'), '#', array('class' => 'add-handler icon')) . '</li>';
                                }
                                echo '<li>' . CHtml::link(YiiadminModule::t('Удалить'), Yii::app()->createUrl('yiiadmin/manageModel/delete', array('model_name' => get_class($relatedModel), 'pk' => $relatedModel->getPrimaryKey())), array('class' => 'delete-handler delete icon')) . '</li>';
                                ?>
                            </ul>
                        </div>
                        <?php
                        $rAttributes = YiiadminModule::getModelAttributes($relatedModel);
                        foreach ($rAttributes AS $rAttribute) {
                            if ($relData[key($relData)][2] == $rAttribute) {
                                continue;
                            }
                            ?>
                            <div class="row <?php if ($relatedModel->getError($rAttribute)) echo 'errors'; ?>">
                                <div class="column span-4"><?php echo $form->labelEx($relatedModel, $rAttribute); ?></div>
                                <div class="column span-flexible">
                                    <?php echo $this->module->createWidget($form, $relatedModel, "[$i]" . $rAttribute); ?>
                                    <ul class="errorlist">
                                        <li><?php echo $form->error($relatedModel, $rAttribute); ?></li>
                                    </ul>
                                </div>
                            </div>
                            <?php } ?>
                    </fieldset>

                    <?php
                    ++$i;
                }
                ?>

                <?php
            }
            ?>

        </div>

        <div class="module footer">
            <ul class="submit-row">
                <?php if (!$model->isNewRecord): ?>
                <li class="left delete-link-container">
                    <?php echo CHtml::link(YiiadminModule::t('Удалить'), $this->createUrl('manageModel/delete', array(
                        'model_name' => get_class($model),
                        'pk' => $model->primaryKey,
                    )),
                    array(
                        'class' => 'delete-link',
                        'confirm' => YiiadminModule::t('Удалить запись ID ') . $model->primaryKey . '?',
                    )); ?>
                </li>
                <?php endif; ?>
                <li class="submit-button-container">
                    <input type="submit" value="<?php echo YiiadminModule::t('Сохранить');?>" class="default"
                           name="_save"/>
                </li>
                <li class="submit-button-container">
                    <input type="submit" value="<?php echo YiiadminModule::t('Сохранить и создать новую запись');?>"
                           name="_addanother"/>
                </li>
                <li class="submit-button-container">
                    <input type="submit" value="<?php echo YiiadminModule::t('Сохранить и редактировать');?>"
                           name="_continue"/>
                </li>
            </ul>
            <br clear="all">
        </div>

    </div>
</div>
<?php
$this->endWidget();

Yii::app()->getClientScript()->registerScript('deleteHandler', "$('a.delete').live('click',function() {
	if ( confirm('" . YiiadminModule::t('Вы уверены, что хотите удалить данный элемент?') . "') ) {
		var fieldsetClass = $(this).parents('fieldset').attr('class');
		fieldsetClass = '.' + fieldsetClass.replace(' ', '.');
		if ( $(fieldsetClass).length > 1 ) {
			$(this).parents('fieldset').remove();
		}
		else {
			$(this).parents('fieldset').find('input,textarea,select').val('');
		}
	}
	return false;});"
);

Yii::app()->getClientScript()->registerScript('addHandler', "$('a.add-handler').live('click',function() {
	var fieldset = $(this).parents('fieldset');
	var clone = fieldset.clone();
	$(clone).insertAfter(fieldset);
	clone.find('input,textarea').val('');
	return false;});"
);

