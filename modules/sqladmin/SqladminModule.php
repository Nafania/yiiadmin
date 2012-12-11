<?php

class SqladminModule extends YiiadminModule
{    
	public function init()
	{
		$this->setImport(array(
			'sqladmin.models.*',
		));

	}

	public static function getModuleCategory () {
	    return YiiadminModule::t('Утилиты');
	}
}
