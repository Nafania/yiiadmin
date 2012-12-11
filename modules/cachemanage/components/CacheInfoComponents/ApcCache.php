<?php
class ApcCache {
    
    public function getCacheData () {
		$data = apc_cache_info('user');

		$data =  array_map(array($this, 'getKeyInfo'), $data['cache_list']);
	    reset($data);

	    return $data;
    }
    
    protected function getKeyInfo ( $val ) {
		$val['data'] = var_export(apc_fetch($val['info']), true);
		return $val;
    }
    
    public function deleteKey ( $key ) {
		return apc_delete($key);
    }
    
    public function getKeyField () {
		return 'info';
    }
}

?>
