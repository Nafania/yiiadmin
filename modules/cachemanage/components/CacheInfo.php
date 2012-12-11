<?php
class CacheInfo extends CComponent {
    private $_cacheComponent;
    
    public function __construct () {
		if ( $cache = Yii::app()->getCache() ) {
			$dir = dirname(__FILE__);
		    $alias = md5($dir);
		    Yii::setPathOfAlias($alias, $dir);
			Yii::import($alias . '.CacheInfoComponents.*');

	        $cacheName = get_class($cache);
	        $cacheName = substr($cacheName, 1);
			if ( class_exists($cacheName) ) {
	            $this->_cacheComponent = new $cacheName;
			}
			else {
				$this->_cacheComponent = new DummyCache;
			}
		}
    }
    
    public function getCacheData () {
		return $this->_cacheComponent->getCacheData();
    }
    
    public function deleteKey ( $key ) {
		return $this->_cacheComponent->deleteKey($key);
    }
    
    public function getKeyField () {
		return $this->_cacheComponent->getKeyField();
    }
    
    public function getColumns () {
		$cacheData = $this->_cacheComponent->getCacheData();

	    if ( isset($cacheData[0]) ) {
			return array_keys($cacheData[0]);
	    }
	    else {
		    return array();
	    }
    }
    
    public function flush () {
		Yii::app()->getCache()->flush();
    }
}
?>
