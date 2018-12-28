<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'data';
	$_GPC['op'] = !isset($_GPC['op']) ? 'wish' : $_GPC['op'];

			
	if( checkSubmit('deleteuser') ){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_chickenwish_wish');
		if($res){
			message('删除成功','referer','success');
		}else{
			message('删除失败','referer','error');
		}
	}
	
	if($_GPC['op'] == 'wish'){
		$topbar = topbal::wishList();

		$page = intval($_GPC['page']);
		$num = 10;
		$where['uniacid'] = $_W['uniacid'];
		if( !empty( $_GPC['wid'] ) ) $where['id'] = $_GPC['wid'];

		$order = ' `id` DESC ';
		if( $_GPC['order'] == 1 ) $order = ' `zan` DESC ';

		$data = Util::getAllDataInSingleTable('zofui_chickenwish_wish',$where,$page,$num,$order);
		$list = $data[0];
		$pager = $data[1];

		if( !empty( $list ) ){
			foreach ($list as  &$v) {
				$v['pic'] = iunserializer( $v['pic'] );
			}
		}

		$tody = strtotime(date('Y-m-d',time()));
		$tomorrow = $tody - 24*3600;

		$where = array('uniacid'=>$_W['uniacid'],'time>'=>$tomorrow);
		$twodays = Util::countDataNumber('zofui_chickenwish_wish',$where);

		$where = array('uniacid'=>$_W['uniacid'],'time>'=>$tody);
		$todays = Util::countDataNumber('zofui_chickenwish_wish',$where);

		$tomorrows = $twodays - $todays;

	}




	elseif($_GPC['op'] == 'delete'){
		$wish = pdo_get('zofui_chickenwish_wish',array('id'=>$_GPC['id']),array('openid'));

		pdo_delete('zofui_chickenwish_wish',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));
		Util::deleteCache( 'w',$wish['openid'] );
		message('删除成功','referer','success');
	}	

	include $this->template('web/data');