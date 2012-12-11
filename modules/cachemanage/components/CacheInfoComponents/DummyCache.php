<?php
class DummyCache {
    
    public function getCacheData () {
		return array();
    }
    
    protected function getKeyInfo ( $val ) {
	    return null;
    }
    
    public function deleteKey ( $key ) {
	    return null;
    }
    
    public function getKeyField () {
	    return null;
    }
}

?>