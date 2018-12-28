<?php 
	global $_W,$_GPC;
	model_user::initUser();
	$_GPC['op'] = in_array($_GPC['op'],array('self','share','reset')) ? $_GPC['op'] : 'app';

	$start = strtotime( $this->module['config']['start'] );
	$end = strtotime( $this->module['config']['end'] );

	$status = 0; // 可进入
	if( $_GPC['op'] == 'app' ) { // 来自公众号

		if( empty( $_W['openid'] ) ) $status = 1; // 需关注
		$wish = model_wish::getWish( $_W['openid'] );
		$open = $_W['openid'];

		if( !empty( $wish ) && empty( $_GPC['reset'] ) ) {
			header( 'location: ' .$this->createMobileUrl('index',array('op'=>'self')) );die;
		}

	}elseif( in_array($_GPC['op'],array('self','share')) ){ // 来自分享 或预览

		if( $_GPC['op'] == 'self' ) {
			if( empty( $_W['openid'] ) ) $status = 1; // 需关注
			$open = $_W['openid'];
			$wish = model_wish::getWish( $_W['openid'] );
			
		if( empty( $wish ) ) {
			header( 'location: ' .$this->createMobileUrl('index') );die;
		}

		}elseif( $_GPC['op'] == 'share' ){

			if( empty( $_GPC['id'] ) ) $status = 1; // 需关注

			$wish = model_wish::getWish( $_GPC['id'] );
			if( empty( $wish ) ) $status = 1; // 需关注
			$open = $_GPC['id'];
		}

	}else{
		die;
	}
	
	// 开启点赞
	if( $this->module['config']['iszan'] == 1 && empty( $this->module['config']['isrank'] ) ){
		if( $_GPC['op'] == 'app' || $_GPC['op'] == 'self' ) {
			$myrank = model_user::getMyRanK( $open );
			//$mywish = model_wish::getWish( $open );
			$ranknum = $this->module['config']['ranknum'] > 0 ? $this->module['config']['ranknum'] : 100;
			$allrank = model_user::getAllRanK( $ranknum );
		}


		$prizerule = pdo_get('zofui_chickenwish_prizerule',array('uniacid'=>$_W['uniacid']));
		if( !empty( $prizerule ) ) {
			$prizerule['kefuqr'] = tomedia( $prizerule['kefuqr'] );
		}

		if( $prizerule['isprize'] == 1 ) {
			$allprize = model_prize::getAllPrize();
			$stock = 0;
			if( !empty( $allprize ) ) {
				foreach ($allprize as $v) {
					$stock += $v['stock'];
				}
			}

			if( $_GPC['op'] == 'app' || $_GPC['op'] == 'self' ) {
				$myprize = pdo_get('zofui_chickenwish_prizelog',array('uniacid'=>$_W['uniacid'],'openid'=>$open));
				if( !empty( $myprize ) && $prizerule['getedtype'] == 0 ) {
					$myprize['info'] = pdo_get('zofui_chickenwish_prize',array('id'=>$myprize['pid']));
					$myprize['info']['img'] = tomedia( $myprize['info']['img'] );
				}
			}
		}


	}


	$explain = model_wish::getExplain();

	$this->module['config']['advert'] = iunserializer( $this->module['config']['advert'] );
	$this->module['config']['bgimg'] = iunserializer( $this->module['config']['bgimg'] );
	$settings = $this->module['config'];

	
	$sharetitle = str_replace(array('{nick}','{name}'), array($_W['fans']['tag']['nickname'],$wish['title']), $this->module['config']['sharetitle']);
	$sharedesc = str_replace(array('{nick}','{name}'), array($_W['fans']['tag']['nickname'],$wish['title']), $this->module['config']['sharedesc']);

	if( !empty( $wish['pic'][1] ) ) $wishhead = tomedia( $wish['pic'][1] );
	if( !empty( $wish['pic'][0] ) ) $wishhead = tomedia( $wish['pic'][0] );
	if( !empty( $wish['pic'][2] ) ) $wishhead = tomedia( $wish['pic'][2] );

	$shareimg = empty( $this->module['config']['sharetype'] ) ? tomedia($this->module['config']['shareimg']) : $wishhead;

	$settings['sharetitle'] = $sharetitle;
	$settings['sharedesc'] = $sharedesc;
	$settings['shareimg'] = $shareimg;
	$settings['sharelink'] = Util::createModuleUrl('index',array('op'=>'share','id'=>$open));
	
	$settings['op'] = $_GPC['op'];
	$settings['id'] = $open;
	$settings['wish'] = $wish;
	$settings['taketype'] = $prizerule['taketype'];

	if( $_GPC['op'] == 'self' || $_GPC['op'] == 'app' ) {
		$settings['prizerule'] = $prizerule;
		//$settings['wish'] = $mywish;
	}


	$music = array(
		array('name'=>'尝试一切','url'=>'https://cdn.s.shangjiadao.cn/source/mp3/common/wangluo/fengkuangdongwucheng.mp3'),
		array('name'=>'新年汪汪','url'=>'http://res1.eqh5.com/2866717c3415445ea9c2e0ee9a7d2d70.mp3'),
		array('name'=>'恭喜发财','url'=>'http://res1.eqh5.com/ccaa45a4343f46d9b29c8092f00949cf.mp3'),
		array('name'=>'喜气洋洋','url'=>'http://res1.eqh5.com/7a2a0ecc351641b2ba54e68bccd8d842.mp3'),
		array('name'=>'吴郡','url'=>'http://res1.eqh5.com/f05c8cbe1a5948b0b48fa4397a0e9d0d.mp3'),
		array('name'=>'新年好','url'=>'http://res1.eqh5.com/6ac8ec7aa5024bfd9f9ebe95088f1ace.mp3'),
		array('name'=>'蝶飞花舞','url'=>'http://res1.eqh5.com/c2bd090eb3f94b7f909b2366724ccd2f.mp3'),
		array('name'=>'新年歌','url'=>'http://res1.eqh5.com/d1991f2f4b264e8088d19db3be012b69.mp3'),
		array('name'=>'新年快乐','url'=>'http://res1.eqh5.com/8254beea85404b49be94dcdd6777bcd2.mp3'),
		array('name'=>'新年锣鼓','url'=>'http://res1.eqh5.com/874c4b225a774f13851caee19f9f7fb0.mp3'),
		array('name'=>'欢迎新年','url'=>'http://res1.eqh5.com/5c029c1cb2f548a7a3a7dcf1ab2f702a.mp3'),
		array('name'=>'喜洋洋','url'=>'http://res1.eqh5.com/1f84645f95cc4ad5b7725b5d092f1874.mp3'),
		array('name'=>'新春乐','url'=>'http://res1.eqh5.com/e694f86889fe481581ad3c53c1888ab3.mp3'),
		array('name'=>'纯音乐1','url'=>'http://res1.eqh5.com/store/6d8857880777f0519433642992262296.mp3'),
		array('name'=>'纯音乐2','url'=>'http://res1.eqh5.com/e8d4ecfc33b54cc18ab7870a70f34111.mp3'),
		array('name'=>'纯音乐3','url'=>'http://res1.eqh5.com/5a2af58b15954357a18ae1f9f85cff30.mp3'),
		array('name'=>'祝福你','url'=>'http://res1.eqh5.com/3c7199325d804e38b7c5af6d73e7f68e.mp3'),
		array('name'=>'keep on going','url'=>'http://res1.eqh5.com/store/ab7fc0401c1d9e317f0aeda3600b5030.mp3'),	
		array('name'=>'popular song','url'=>'http://res1.eqh5.com/0c60dee967da4d79b3b28d8a07ff4d36.mp3'),
		array('name'=>'Fulfillment','url'=>'http://res1.eqh5.com/store/fee781fefde9b5eac01c4d52130351f6.mp3'),
		array('name'=>'hometown','url'=>'http://res1.eqh5.com/8770062197db4cbcae419b4adfb688d5.mp3'),
		array('name'=>'Worth It','url'=>'http://cdn.s.shangjiadao.cn/source/mp3/common/wangluo/worthit.mp3'),
		array('name'=>'Zunea-Zunea','url'=>'http://cdn.s.shangjiadao.cn/source/mp3/common/hot/zunea.mp3'),
		array('name'=>'H.N.Y','url'=>$_W['siteroot'].'addons/zofui_chickenwish/public/images/500_9wUytfmppLaNQOj.mp3'),
	);

		

	include $this->template ('index');
