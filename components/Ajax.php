<?php
class Ajax extends CComponent {
	static function send ( $status, $message, $data = array() ) {
		echo CJSON::encode( array(
			'status' => $status,
			'message' => $message,
			'data' => $data,
		));

		Yii::app()->end();
	}

	static function renderAjax ( $view, $data ) {
		if ( Yii::app()->getRequest()->getIsAjaxRequest() ) {
			Yii::app()->getController()->renderPartial( $view, $data );
		}
		else {
			Yii::app()->getController()->render( $view, $data );
		}
	}
}