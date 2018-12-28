<?php

class model_prize {


	static function getAllPrize(){
		global $_W;
		
		$cache = Util::getCache( 'prize','all' );
		if(empty($cache)){
			$cache = pdo_getall('zofui_chickenwish_prize',array('uniacid'=>$_W['uniacid']));
			if( !empty( $cache ) ){
				foreach ($cache as &$v) {
					$v['img'] = tomedia( $v['img'] );
				}
				Util::setCache('prize','all',$cache);
			}
		}
		return $cache;  //需删除缓存
	}
		

}