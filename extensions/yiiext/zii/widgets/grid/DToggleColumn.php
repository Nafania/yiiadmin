<?php
/**
 * @author ElisDN <mail@elisdn.ru>
 * @link http://www.elisdn.ru
 */

Yii::import( 'zii.widgets.grid.CGridColumn' );

class DToggleColumn extends CGridColumn {
	/**
	 * @var string the attribute name of the data model.
	 */
	public $name;
	/**
	 * @var boolean a PHP expression if needle.
	 */
	public $value;
	/**
	 * @var string or a PHP expression that will be evaluated url to toggle action.
	 */
	public $linkUrl;
	/**
	 * @var array captions for 'alt' and 'title' attributes of icon.
	 */
	public $titles;
	/**
	 * @var array icon address.
	 */
	public $images;
	/**
	 * @var int icon width and icon height.
	 */
	public $imageSize = 16;
	/**
	 * @var string confirmation text if needle.
	 */
	public $confirmation;
	/**
	 * @var boolean whether the column is sortable.
	 */
	public $sortable = true;
	/**
	 * @var mixed the HTML code representing a filter input.
	 */
	public $filter;

	/**
	 * @var string stores CSS class name for link
	 */
	protected $class;

	/**
	 * @throws CException
	 */
	public function init () {
		parent::init();

		if ( empty( $this->name ) ) {
			$this->sortable = false;
		}

		if ( empty( $this->name ) && empty( $this->value ) ) {
			throw new CException( 'Either "name" or "value" must be specified for DToggleColumn.' );
		}

		list($this->images, $this->titles) = Yii::app()->getModule( 'yiiadmin' )->getToggleImages( $this->grid->dataProvider->data[0], $this->name );

		$this->class = 'toggle_' . preg_replace( '/\./', '_', $this->name );
		$this->registerClientScript();
	}

	/**
	 * Renders the filter cell content.
	 */
	protected function renderFilterCellContent () {
		if ( is_string( $this->filter ) ) {
			echo $this->filter;
		}
		else if ( $this->filter !== false && $this->grid->filter !== null && $this->name !== null && strpos( $this->name, '.' ) === false ) {
			if ( is_array( $this->filter ) ) {
				echo CHtml::activeDropDownList( $this->grid->filter, $this->name, $this->filter, array( 'id' => false, 'prompt' => '' ) );
			}
			else if ( $this->filter === null ) {
				echo CHtml::activeTextField( $this->grid->filter, $this->name, array( 'id' => false ) );
			}
		}
		else {
			parent::renderFilterCellContent();
		}
	}

	/**
	 * Renders the header cell content.
	 * This method will render a link that can trigger the sorting if the column is sortable.
	 */
	protected function renderHeaderCellContent () {
		if ( $this->grid->enableSorting && $this->sortable && $this->name !== null ) {
			echo $this->grid->dataProvider->getSort()->link( $this->name, $this->header, array( 'class' => 'sort-link' ) );
		}
		else if ( $this->name !== null && $this->header === null ) {
			if ( $this->grid->dataProvider instanceof CActiveDataProvider ) {
				echo CHtml::encode( $this->grid->dataProvider->model->getAttributeLabel( $this->name ) );
			}
			else {
				echo CHtml::encode( $this->name );
			}
		}
		else {
			parent::renderHeaderCellContent();
		}
	}

	/**
	 * Registers the client scripts for the column.
	 */
	protected function registerClientScript () {
		if ( is_string( $this->confirmation ) ) {
			$confirmation = "if(!confirm(" . CJavaScript::encode( $this->confirmation ) . ")) return false;";
		}
		else {
			$confirmation = '';
		}

		if ( Yii::app()->request->enableCsrfValidation ) {
			$csrfTokenName = Yii::app()->request->csrfTokenName;
			$csrfToken = Yii::app()->request->csrfToken;
			$csrf = " ,'$csrfTokenName':'$csrfToken'";
		}
		else {
			$csrf = '';
		}

		$js = "
jQuery(document).on('click','#{$this->grid->id} a.{$this->class}', function(){
	$confirmation
	var link = $(this);
	$.ajax({
	    url: '" . Yii::app()->createUrl( 'yiiadmin/manageModel/toggle' ) . "',
	    type: 'POST',
	    data: {attribute: '" . $this->name . "', pk: $(this).data('pk'), model_name: $(this).data('model')$csrf},
	    dataType: 'json',
		success:function(data) {
			link.children('img').attr('src', data.data.image);
			link.children('img').attr('title', data.data.imageTitle);
			link.children('img').attr('alt', data.data.imageTitle);
		}
	});
	return false;
});	";

		$script = CJavaScript::encode( new CJavaScriptExpression( $js ) );
		Yii::app()->clientScript->registerScript( __CLASS__ . '#' . $this->id, $script );
	}

	/**
	 * Renders the data cell content.
	 * @param integer $row the row number (zero-based)
	 * @param mixed $data the data associated with the row
	 */
	protected function renderDataCellContent ( $row, $data ) {
		if ( !empty( $this->value ) ) {
			$value = $this->evaluateExpression( $this->value, array( 'data' => $data, 'row' => $row ) );
		}
		elseif ( !empty( $this->name ) ) {
			$value = CHtml::value( $data, $this->name );
		}

		$src = $value ? $this->images[1] : $this->images[0];
		$title = $this->titles[(int) $value];

		$image = CHtml::image( $src, $title, array( 'title' => $title ) );
		echo CHtml::link( $image, '#', array( 'class' => $this->class, 'data-pk' => $data->primaryKey, 'data-model' => get_class( $data ) ) );
	}
}
