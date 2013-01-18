<?php

/* Manage Selected Rows Widget */

class manageSelected extends CWidget {
	// Controller ID


	// CGridView ID
	public $gridId;

	public $params = array();

	public $model;

	public $buttons = array();

	public $path = '';

	private $ids = array();

	public function run () {
		parent::run();

		$this->path .= '/' . Yii::app()->getController()->getId() . '/';

		$this->_getButtons();

		$this->ids['deleteSelected'] = Yii::t('Site', 'Are you sure you want to delete selected items?');

		$this->buttons[] = array(
			'url' => Yii::app()->createUrl( $this->path . 'deleteSelected' ),
			'title' => Yii::t('Site', 'Delete selected'),
			'class' => 'deleteSelected'
		);

		$this->registerClientScript();

		$this->render( 'widget' );
	}

	private function _getButtons () {

		$gridConfig = $this->model->adminSearch();
		foreach ( $gridConfig['columns'] AS $column ) {
			if ( isset( $column['class'] ) && isset( $column['name'] ) && $column['class'] == 'DToggleColumn' ) {
				list($images, $titles) = Yii::app()->getModule( 'yiiadmin' )->getToggleImages( $this->model, $column['name'] );

				foreach ( $images AS $key => $val ) {
					$this->buttons[] = array(
						'url' => Yii::app()->createUrl( $this->path . 'toggle', array( 'attribute' => $column['name'], 'val' => $key ) ),
						'title' => $titles[$key],
						'class' => $column['name']
					);

					if ( !empty($column['confirmation']) ) {
						$this->ids[$column['name']] = $column['confirmation'];
					}
					else {
						$this->ids[$column['name']] = Yii::t('Site', 'Are you sure?');
					}
				}
			}
		}
	}

	protected function registerClientScript () {
		$params = '';
		if ( Yii::app()->request->enableCsrfValidation ) {
			$this->params[Yii::app()->getRequest()->csrfTokenName] = Yii::app()->getRequest()->getCsrfToken();
		}
		if ( $this->params ) {
			foreach ( $this->params AS $key => $val ) {
				$params .= ', ' . $key . ': \'' . $val . '\'';
			}
		}
		$ids = '';
		$titles = array();
		foreach ( $this->ids AS $id => $title ) {
			$ids .= ( $ids ? ',' : '' ) . 'a.' . $id . '-link';
			$titles[$id . '-link'] = $title;
		}
		$modelName = get_class($this->model);
		$titles = CJavaScript::encode($titles);

		$js = <<<JAVASCRIPT
var id = '{$this->gridId}';
jQuery(document).on('click','$ids',function(e) {
	var titles = $titles;
	var aClass = $(this).attr('class').split(' ')[0];
	e.preventDefault();
	if( confirm(titles[aClass]) ) {
	    var selectedItems = $.fn.yiiGridView.getSelection(id);
	    var requestUrl  = $(this).attr('href') + '/?';

	    $.fn.yiiGridView.update(id, {
		    type:'POST',
            data:({model_name: "$modelName", pk: selectedItems$params }),
		    url:requestUrl,
		    success:function() {
			    $.fn.yiiGridView.update(id);
		    },
	    });
	}
});
$('a.selectAll').on('click', function(){
        $('#' + id + ' tbody > tr').addClass('selected');
    }
);
$('a.selectNone').on('click',function() {
        $('#' + id + ' tbody > tr').removeClass('selected');
    }
);
JAVASCRIPT;

		$cs = Yii::app()->getClientScript();

		$cs->registerScript( __CLASS__ . '#' . $this->id, $js );
	}
}

?>