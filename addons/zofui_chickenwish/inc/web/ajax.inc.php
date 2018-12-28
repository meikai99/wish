<?php 
	global $_W,$_GPC;

	if($_GPC['op'] == 'deletecache'){ 

		if( $_GPC['type'] == 'cache' ){
			$res = cache_clean();
			
			Util::echoResult(200,'已删除');
		
		}elseif( $_GPC['type'] == 'delete' ){

			pdo_delete('zofui_chickenwish_wish',array('uniacid'=>$_W['uniacid']));
			pdo_delete('zofui_chickenwish_prizelog',array('uniacid'=>$_W['uniacid']));
			pdo_delete('zofui_chickenwish_zanlog',array('uniacid'=>$_W['uniacid']));

			cache_clean();
			Util::echoResult(200,'已清空');
		}
		
		
	
	}
	
	elseif( $_GPC['op'] == 'addprize' ){

		$id = intval( $_GPC['fid'] );

		$data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $_GPC['name'],
			'stock' => intval( $_GPC['stock'] ),
			'type' => intval( $_GPC['type'] ),
			'img' => $_GPC['img'],
			'money' => sprintf('%.2f',$_GPC['money']),
		);

		if( $data['money'] < 1 && empty($data['type']) ) Util::echoResult(201,'红包金额不能小于1');
		if( empty( $data['name'] ) ) Util::echoResult(201,'请填写名称');
		if( empty( $data['img'] ) ) Util::echoResult(201,'请设置图片');	

		if( $id > 0 ) {
			$res = pdo_update( 'zofui_chickenwish_prize',$data ,array('id'=>$id,'uniacid'=>$_W['uniacid']));
		}else{
			$res = pdo_insert('zofui_chickenwish_prize',$data);
		}

		if( $res ) {
			Util::deleteCache('prize','all');
			Util::deleteCache('prize',$id);
			Util::echoResult(200,'已保存');
		}

		Util::echoResult(201,'保存失败');


	// 编辑
	}elseif( $_GPC['op'] == 'findprize' ){

		$temp = pdo_get('zofui_chickenwish_prize',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['fid']));
		if( empty( $temp ) ) Util::echoResult(201,'没有找到奖品');

		$temp['showimg'] = tomedia( $temp['img'] );
		
		Util::echoResult(200,'好',$temp);

	// 发奖
	}elseif( $_GPC['op'] == 'sendprize' ){

		$prizelog = pdo_get('zofui_chickenwish_prizelog',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));

		if( empty( $prizelog ) ) Util::echoResult(201,'未找到领奖记录');

		pdo_update('zofui_chickenwish_prizelog',array('exname'=>$_GPC['name'],'exnum'=>$_GPC['num'],'status'=>1),array('id'=>$prizelog['id']));

		Util::echoResult(200,'已发奖');
	
	// 修改值
	}elseif( $_GPC['op'] == 'editzan' ){

		$wish = pdo_get('zofui_chickenwish_wish',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
		if( empty( $wish ) ) Util::echoResult(201,'未找到拜年记录');

		$num = $wish['zan'] + intval( $_GPC['num'] );

		if( $num <= 0 ) $num = 0;

		pdo_update('zofui_chickenwish_wish',array('zan'=>$num),array('id'=>$wish['id']));
		Util::deletecache('w',$wish['openid']);
		Util::echoResult(200,'已修改');
	}





