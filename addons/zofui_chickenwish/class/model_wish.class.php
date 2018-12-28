<?php

class model_wish {


	static function getWish( $openid ){
		global $_W;
		if( empty( $openid ) ) return false;
		$cache = Util::getCache( 'w',$openid );
		if(empty($cache)){
			$cache = pdo_get('zofui_chickenwish_wish',array('openid'=>$openid,'uniacid'=>$_W['uniacid']));
			if( !empty( $cache ) ){
				if( !empty( $cache['pic'] ) ){
					$cache['pic'] = iunserializer( $cache['pic'] );
				}
				$cache['bgimg'] = tomedia( $cache['bgimg'] );

				Util::setCache('w',$openid,$cache);
			}
		}
		return $cache;  //需删除缓存
	}

	static function getExplain(){
		global $_W;
		
		$cache = Util::getCache( 'explain','all' );
		if(empty($cache)){
			$cache = pdo_get('zofui_chickenwish_explain',array('uniacid'=>$_W['uniacid']));
			if( !empty( $cache ) ){
				Util::setCache('explain','all',$cache);
			}
		}
		return $cache;  //需删除缓存
	}

	

}