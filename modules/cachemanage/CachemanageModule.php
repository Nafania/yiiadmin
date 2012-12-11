<?php

class CachemanageModule extends YiiadminModule
{    
	public function init()
	{
		$this->setImport(array(
			'cachemanage.models.*',
			'cachemanage.components.*',
		));

	}

	public static function getModuleCategory () {
	    return YiiadminModule::t('Утилиты');
	}
}
