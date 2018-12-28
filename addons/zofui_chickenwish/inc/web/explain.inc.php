<?php 
	global $_W,$_GPC;
	$_GPC['do'] = 'explain';
	$_GPC['op'] = 'create';
	

	
	if(checkSubmit('create')){
		$_GPC = Util::trimWithArray($_GPC);
		
		$data = array(
			'uniacid' => $_W['uniacid'],
			'content' => $_GPC['content']
		);

		$info = pdo_get('zofui_chickenwish_explain',array('uniacid'=>$_W['uniacid']));
		if( !empty($info) ){
			$res = pdo_update('zofui_chickenwish_explain',$data,array('uniacid'=>$_W['uniacid']));
			
		}else{
			$res = pdo_insert('zofui_chickenwish_explain',$data);
		}
		if($res){
			Util::deleteCache('rule','rule'); // 删除缓存
			Util::deleteCache('explain','all'); // 删除缓存
			message('操作成功','referer','success');
		}else{
			message('操作失败','referer','error');
		}
	}
 	
	$info = pdo_get('zofui_chickenwish_explain',array('uniacid'=>$_W['uniacid']));

	if(empty($info)){
		$info['uniacid'] = $_W['uniacid'];
		$info['content'] = <<<div
			<p>
			    <br/>
			</p>
			<p>
			    1.点击开始制作。<br/>
			</p>
			<p>
			    2.点击人物头像上传有您头像的照片。
			</p>
			<p>
			    3.填写您的姓名。
			</p>
			<p>
			    4.选择背景。
			</p>
			<p>
			    5.点击完成即可到达您的拜年页面。
			</p>
			<p>
			    6.点击页面右上角发送给您想拜年的对象，他打开你发送的页面就可以看到您的拜年祝福啦。
			</p>
			<p>
			    <br/>
			</p>
			<p>
			    <br/>
			</p>
div;
	
		pdo_insert('zofui_chickenwish_explain',$info);

	}
	
	
	include $this->template('web/explain');