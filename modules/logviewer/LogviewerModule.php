<?php

class LogviewerModule extends YiiadminModule
{
	public function init()
	{
		$this->setImport(array(
			'logviewer.components.*',
		));
	}
	
	public static function getModuleCategory () {
	    return YiiadminModule::t('Утилиты');
	}
}
