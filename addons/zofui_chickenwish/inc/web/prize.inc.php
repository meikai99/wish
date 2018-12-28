<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'prize';
	$_GPC['op'] = empty($_GPC['op'])? 'list' : $_GPC['op'];
	
	$prizerule = pdo_get('zofui_chickenwish_prizerule',array('uniacid'=>$_W['uniacid']));

	// 创建新活动
	if(checkSubmit('setrule')){
		$_GPC = Util::trimWithArray($_GPC);
		
		$data = array(
			'isprize' => intval($_GPC['isprize']),
			'uniacid' => $_W['uniacid'],
			'getedtype' => intval($_GPC['getedtype']),
			'getedvalue' => intval($_GPC['getedvalue']),
			'getmem' => intval($_GPC['getmem']),
			'rule' => $_GPC['rule'],
			'taketype' => intval($_GPC['taketype']),
			'takeinfo' => $_GPC['takeinfo'],
			'kefuqr' => $_GPC['kefuqr'],
		);
		


		if( !empty( $prizerule['id'] ) ){
			$res = pdo_update('zofui_chickenwish_prizerule',$data);
		}else{
			$res = pdo_insert('zofui_chickenwish_prizerule',$data);
		}
		if($res){
			message('操作成功','referer','success');
		}else{
			message('操作失败','referer','error');
		}
	}
 
	// 批量删除
	if( checkSubmit('deleteallprize') ){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_chickenwish_prize');
		if($res){
			message('删除成功','referer','success');
		}else{
			message('删除失败','referer','error');
		}
	}

	// 批量删除
	if( checkSubmit('deleteallprizelog') ){
		$res = WebCommon::deleteDataInWeb($_GPC['checkall'],'zofui_chickenwish_prizelog');
		if($res){
			message('删除成功','referer','success');
		}else{
			message('删除失败','referer','error');
		}
	}
	



	if($_GPC['op'] == 'edit'){
		$prize = pdo_get('zofui_chickenwish_prize',array('id'=>intval($_GPC['id']),'uniacid'=>$_W['uniacid']));
	}
	
	// 列表
	elseif($_GPC['op'] == 'list'){
		$where = array('uniacid'=>$_W['uniacid']);
		if($_GPC['status'] == 1) {
			$where['status'] = 0;
		}

		$info = Util::getAllDataInSingleTable('zofui_chickenwish_prize',$where,$_GPC['page'],10,$order='`id` DESC');
		$list = $info[0];
		$pager = $info[1];
	}

	elseif( $_GPC['op'] == 'geted' ){

		$topbar = topbal::prizelogList();

		$allprize = model_prize::getAllPrize();

		$page = empty( $_GPC['down'] ) ? intval($_GPC['page']) : 1;
		$num = empty( $_GPC['down'] ) ? 10 : 99999;
		$where['uniacid'] = $_W['uniacid'];
		if( $_GPC['status'] == 1 ) $where['status'] = 0;
		if( $_GPC['status'] == 2 ) $where['status'] = 1;
		if( $_GPC['status'] == 3 ) $where['status'] = 2;

		if( !empty( $_GPC['for'] ) ) $where['code'] = $_GPC['for'];	

		$order = ' `id` DESC ';

		$data = Util::getAllDataInSingleTable('zofui_chickenwish_prizelog',$where,$page,$num,$order);
		$list = $data[0];
		$pager = $data[1];

		if( !empty( $list ) ){
			foreach ($list as  &$v) {
				$v['wish'] = model_wish::getWish( $v['openid'] );

				if( !empty( $allprize ) ) {
					foreach ( $allprize as $vv ) {
						if( $vv['id'] == $v['pid'] ) {
							$v['prize'] = $vv;
						}
					}
				}
			}
		}

		if( !empty( $_GPC['down'] ) ) {
			downLoadDrawLog($list);
		}

	}


	// 删除单个奖品
	elseif($_GPC['op'] == 'deleteprize'){
		$id = intval($_GPC['id']);
		$res = WebCommon::deleteSingleData($id,'zofui_chickenwish_prize');
		if($res){
			message('删除成功','referer','success');
		}else{
			message('删除失败','referer','error');
		}
	}
	
	// 删除单个奖品
	elseif($_GPC['op'] == 'deleteprizelog'){
		$id = intval($_GPC['id']);
		$res = WebCommon::deleteSingleData($id,'zofui_chickenwish_prizelog');
		if($res){
			message('删除成功','referer','success');
		}else{
			message('删除失败','referer','error');
		}
	
	// 发奖
	}elseif( $_GPC['op'] == 'sendprize' ){

		$prizelog = pdo_get('zofui_chickenwish_prizelog',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));

		if( empty( $prizelog ) ) message('未找到领奖记录','referer','error');

		pdo_update('zofui_chickenwish_prizelog',array('status'=>1),array('id'=>$prizelog['id']));
		message('已发奖','referer','success');
	
	}elseif( $_GPC['op'] == 'refuse' ){

		$prizelog = pdo_get('zofui_chickenwish_prizelog',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));

		if( empty( $prizelog ) ) message('未找到领奖记录','referer','error');

		pdo_update('zofui_chickenwish_prizelog',array('status'=>2),array('id'=>$prizelog['id']));
		message('已设为拒绝发奖','referer','success');
		
	}elseif( $_GPC['op'] == 'recover' ){

		$prizelog = pdo_get('zofui_chickenwish_prizelog',array('id'=>$_GPC['id'],'uniacid'=>$_W['uniacid']));

		if( empty( $prizelog ) ) message('未找到领奖记录','referer','error');

		pdo_update('zofui_chickenwish_prizelog',array('status'=>0),array('id'=>$prizelog['id']));
		message('已恢复','referer','success');
		
	}


	//下载表格
	function downLoadDrawLog($list){
		set_time_limit(0);

		/* 输入到CSV文件 */
		$html = "\xEF\xBB\xBF".$html; //添加BOM
		/* 输出表头 */		
		$html .= '编号' . "\t,";		
		$html .= '姓名' . "\t,";
		$html .= '点赞值' . "\t,";		
		$html .= '奖品' . "\t,";
		$html .= '兑奖码' . "\t,";		
		$html .= '状态' . "\t,";	
		$html .= '收件人' . "\t,";
		$html .= '收件电话' . "\t,";
		$html .= '收件地址' . "\t,";
		$html .= "\n";
		$num = 0;
 		foreach((array)$list as $k => $v){	

 			$addres = iunserializer( $v['add'] );
 			$status = $v['status'] == 0 ? '待兑奖' : ( $v['status'] == 1 ? '已发奖' : '已拒绝' );

 			$html .= $v['id'] . "\t,";
			$html .= $v['wish']['title'] . "\t,";
			$html .= $v['wish']['zan'] . "\t,";
			$html .= $v['prize']['name'] . "\t,";
			$html .= $v['code'] . "\t,";
			$html .= $status . "\t,";
			$html .= $addres['name'] . "\t,";
			$html .= $addres['tel'] . "\t,";
			$html .= $addres['add'] . "\t,";			
			$html .= "\n"; 
			
		}
		/* 输出CSV文件 */
		header("Content-type:text/csv");
		header("Content-Disposition:attachment; filename=领奖记录.csv");
		echo $html;
		exit;
		
	}	

	include $this->template('web/prize');