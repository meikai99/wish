<?php 
	global $_W,$_GPC;
	

	if( $_GPC['op'] == 'check' ){
		if( empty( $_SESSION['inga'] ) ) die;

		$geted = pdo_get('zofui_chickenwish_prizelog',array('uniacid'=>$_W['uniacid'],'code'=>$_GPC['code']));

		if( empty( $geted ) ) Util::echoResult(201,'没有找到兑奖记录');
		if( $geted['status'] == 1 ) Util::echoResult(201,'此兑奖已经领取过了');
		if( $geted['status'] == 2 ) Util::echoResult(201,'此兑奖已经失效了');

		$res = pdo_update('zofui_chickenwish_prizelog',array('status'=>1),array('id'=>$geted['id']));
		if( $res ){
			Util::echoResult(200,'成功核销奖品');
		}
		Util::echoResult(201,'核销奖品失败');
		
	}

	if( !empty( $_GPC['code'] ) ){
		
		$geted = pdo_get('zofui_chickenwish_prizelog',array('code'=>$_GPC['code'],'uniacid'=>$_W['uniacid']));
		if( empty( $geted ) ) message('没有找到兑奖记录');

		$prize = pdo_get('zofui_chickenwish_prize',array('id'=>$geted['pid'],'uniacid'=>$_W['uniacid']));
		
		$wish = model_wish::getWish( $geted['openid'] );

		$prizerule = pdo_get('zofui_chickenwish_prizerule',array('uniacid'=>$_W['uniacid']));

		$rank = model_user::getMyRanK($wish['openid']);

	}

	$_SESSION['inga'] = 1;
	// 分享
	$sharetitle = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharetitle']);
	$sharedesc = str_replace('{nick}', $userinfo['nickname'], $this->module['config']['sharedesc']);
	$settings = array(
		'sharetitle' =>'核销奖品',
		'sharedesc' => '核销奖品',
		'shareimg' => tomedia($this->module['config']['shareimg']),
		'sharelink' => Util::createModuleUrl('hexiao'),
		'do' => 'hexiao',
		'title' => '核销奖品',
		'code' => $join['code'],
	);	
	
	include $this->template ('hexiao');
