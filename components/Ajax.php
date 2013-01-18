<?php
class Ajax extends CComponent {
	const AJAX_ERROR = 'error';
	const AJAX_NOTICE = 'notice';
	const AJAX_SUCCESS = 'success';
	const AJAX_WARNING = 'warning';

	/**
	 * Send ajax answers
	 *
	 * @param string      $status
	 * @param string      $message
	 * @param array       $data
	 */
	static function send ( $status, $message, $data = array() ) {
		echo CJSON::encode(
			array(
			     'status' => $status,
			     'message' => $message,
			     'data' => $data,
			)
		);

		Yii::app()->end();
	}

	/**
	 * renderPartial if ajax request or render if not
	 *
	 * @param       $view
	 * @param array $data
	 */
	static function renderAjax ( $view, $data ) {
		if ( Yii::app()->getRequest()->getIsAjaxRequest() ) {
			Yii::app()->getController()->renderPartial($view, $data);
		}
		else {
			Yii::app()->getController()->render($view, $data);
		}
	}
}