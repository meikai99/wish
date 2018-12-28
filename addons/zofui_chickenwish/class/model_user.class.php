<?php 

/*
	用户表类
*/
class model_user 
{	
	
	static function initUser(){
		global $_W,$_GPC;
		
	}

	
	//查询一条用户数据,传入openid
	static function getSingleUser($openid){
		global $_W;
		
		$cache = Util::getCache('u',$openid);
		
		if( empty( $cache['id'] ) ){
			$cache = pdo_get('mc_mapping_fans',array('uniacid'=>$_W['uniacid'],'openid'=>$openid));
			$tag = iunserializer( base64_decode( $fans['tag'] ) );
			
			if( !empty( $fans ) ) {
				$cache['headimgurl'] = $tag['headimgurl'];
				Util::setCache('u',$openid,$cache);
			}
		}
		return $cache;
		
	}
	
	
	static function getMyRanK($openid){
		global $_W;

		$sql = "SELECT count(1) AS rank FROM ".tablename('zofui_chickenwish_wish')." WHERE uniacid = :uniacid AND  openid != :openid AND  `zan` > (SELECT `zan` FROM ".tablename('zofui_chickenwish_wish')." WHERE uniacid = :uniacidi AND openid = :openidi ) ORDER BY `id` ASC ";
		$params = array(':openid'=>$openid,':uniacid'=>$_W['uniacid'],':uniacidi'=>$_W['uniacid'],':openidi'=>$openid);

		return pdo_fetch($sql,$params);
	}



	static function getAllRanK($num){
		global $_W;
		
		$cache = Util::getCache('rank','all');
		
		if( empty( $cache['id'] ) ){
			$where = array('uniacid'=>$_W['uniacid'],'zan>'=>0.1);
			$select = ' pic,title,zan ';

			$data = Util::getAllDataInSingleTable('zofui_chickenwish_wish',$where,1,$num,' zan DESC,`id` ASC ',true,false,$select);
			
			if( !empty( $data[0] ) ) {
				$cache = $data[0];
				foreach ( $cache as &$v ) {
					$v['pic'] = iunserializer( $v['pic'] );
				}
				unset( $v );
				Util::setCache('rank','all',$cache);
			}
		}
		return $cache;
	}

	

	
}
	
	
	
