<?php
Yii::import('zii.widgets.grid.CGridColumn');

class EIconColumn extends CDataColumn {
	public $imageOptions = array();

	public $emptyText = '—';

	protected function renderDataCellContent ( $row, $data ) {
		$content = $this->emptyText;

		if ( $this->value !== null && $imagePath = $this->evaluateExpression(
			$this->value,
			array(
			     'row' => $row,
			     'data' => $data
			)
		)
		) {
			$this->imageOptions['src'] = Yii::app()->getModule('yiiadmin')->getAssetsUrl(
			) . '/img/icons/icon-' . $imagePath . '.png';
			$content = CHtml::tag('img', $this->imageOptions);
		}

		echo $content;
	}
}
