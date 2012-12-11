<?php

/* Manage Selected Rows Widget */

class manageSelected extends CWidget
{
	// Controller ID
	public $controller;

	// CGridView ID
	public $gridId;

        public $params = array();

	// Buttons
	public $buttons = array('delete','publish','unpublish');

	public function run()
	{
		parent::run();
		$baseDir = dirname(__FILE__);

		$btns = array();

		if(in_array('delete', $this->buttons))
			$btns['delete_btn'] = true;

		if(array_search('publish', $this->buttons))
			$btns['publish_btn'] = true;

		if(array_search('unpublish', $this->buttons))
			$btns['unpublish_btn'] = true;

		$this->render('widget',array('controller'=>$this->controller,'buttons'=>$btns));
	}
	
	public function init()
	{
		 parent::init();

		 $this->registerClientScript();
	}
	
    protected function registerClientScript()
    {
	    $params = '';
        if(Yii::app()->request->enableCsrfValidation) {
			$this->params[Yii::app()->getRequest()->csrfTokenName] = Yii::app()->getRequest()->getCsrfToken();
		}
        if ( $this->params ) {
            foreach ( $this->params AS $key => $val ) {
                $params .= ', ' . $key . ': \'' . $val . '\'';
            }
        }
$js = <<<JAVASCRIPT
function manageSelected(item) {
	var id = '{$this->gridId}';
    var settings = $.fn.yiiGridView.settings[id];
    var keys = $('#'+id+' > div.keys > span');
    var selectedItems = [];
    $('#'+id+' .'+settings.tableClass+' > tbody > tr').each(function(i){
        if($(this).hasClass('selected')) {
            selectedItems.push(keys.eq(i).text());
        }
	});
	//$.fn.yiiGridView.getSelection dont working
	var requestUrl  = item.attr('href') + '/?';

	$.fn.yiiGridView.update(id, {
		type:'POST',
        data:({selectedItems: selectedItems$params }),
		url:requestUrl,
		success:function() {
			$.fn.yiiGridView.update(id);
		},
	});
}
jQuery("[id='deleteSelected']").live('click', function(e) {
	e.preventDefault();
	if(confirm('Are you sure you want to delete selected items?')) manageSelected($(this));
});
jQuery('a.selectAll').live('click',function(){jQuery('#' + id + ' tbody > tr').addClass('selected');});
jQuery('a.selectNone').live('click',function(){jQuery('#' + id + ' tbody > tr').removeClass('selected');});
JAVASCRIPT;

		$cs = Yii::app()->getClientScript();

		$cs->registerScript(__CLASS__.'#'.$this->id,$js);
    }
}

?>