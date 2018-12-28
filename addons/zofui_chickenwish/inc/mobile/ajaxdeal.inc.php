<?php 
	global $_W,$_GPC;
	//$userinfo = model_user::initUserInfo();
    model_user::initUser();
    $start = strtotime( $this->module['config']['start'] );
    $end = strtotime( $this->module['config']['end'] );

    if( empty( $_W['openid'] ) ) Util::echoResult(210,'请先关注公众号');

	if( $_GPC['op'] == 'create'){

        if( $start > TIMESTAMP ) Util::echoResult(201,'活动还没开始');
        if( $end < TIMESTAMP ) Util::echoResult(201,'活动已经结束');

        if( empty( $_GPC['title'] ) ) Util::echoResult(201,'请填写名称');

        $data['uniacid'] = $_W['uniacid']; 
        $data['title'] = $_GPC['title'];
        $data['openid'] = $_W['openid'];
        $data['music'] = empty( $this->module['config']['ismusic'] ) && $this->module['config']['iscusmusic'] == 1 && !empty( $_GPC['music'] ) ? $_GPC['music'] : '';     
       

		if( empty( $_GPC['bgImageBase64'] ) ){
			$data['bgimg'] = $_GPC['bgImageUrl'];
		}else{
			preg_match('/^(data:\s*image\/(\w+);base64,)/', $_GPC['bgImageBase64'], $resimg);
			$bgcontent = base64_decode (str_replace($resimg[1], '', $_GPC['bgImageBase64']) );
			$bgres = Util::uploadImage( $bgcontent );
            
            if( $bgres['status'] ) {
                $data['bgimg'] = $bgres['url']; 
            }else{
                Util::echoResult(201,'上传失败'.$bgres['message']);
            }
					
		}
		
		$pic = explode('@@', $_GPC['images']);

		$data['pic'] = array();
		foreach ($pic as  $v) {
			if( $v == '0' ){
				$data['pic'][] = 0;
			}else{
				preg_match('/^(data:\s*image\/(\w+);base64,)/', $v, $result);
				$content = base64_decode (str_replace($result[1], '', $v) );
				$res = Util::uploadImage( $content );
                if( $res['status'] ) {
                    $data['pic'][] = $res['url'];
                }else{
                    Util::echoResult(201,'上传失败'.$res['message']);
                }
			}
		}

		$data['pic'] = iserializer( $data['pic'] );

		$wish = model_wish::getWish( $_W['openid'] );

		if( empty( $wish ) ){
            $data['time'] = TIMESTAMP;
            $res = pdo_insert('zofui_chickenwish_wish',$data);
            $id = pdo_insertid();

		}else{
            $res = pdo_update('zofui_chickenwish_wish',$data,array('uniacid'=>$_W['uniacid'],'id'=>$wish['id']));
		}

		if( $res ){
            Util::deleteCache('w',$_W['openid']);
			Util::echoResult(200,'好',array('id'=>$_W['openid']));
		}
		Util::echoResult(201,'上传失败');
		
	}elseif( $_GPC['op'] == 'zan' ){

        if( $start > TIMESTAMP ) Util::echoResult(201,'活动还没开始');
        if( $end < TIMESTAMP ) Util::echoResult(201,'活动已经结束');

        $wish = model_wish::getWish( $_GPC['id'] );
        if( empty( $wish ) ) Util::echoResult(201,'未找到数据');

        $isset = pdo_get('zofui_chickenwish_zanlog',array('wid'=>$wish['id'],'openid'=>$_W['openid']));
        if( !empty( $isset ) ) Util::echoResult(201,'你已经赞过'.$wish['title'].'了');

        if( $wish['openid'] == $_W['openid'] ) Util::echoResult(201,'请点击右上角发给好友点赞');

        if( $this->module['config']['limitnum'] > 0 ) {
            $num = Util::countDataNumber('zofui_chickenwish_zanlog',array('uniacid'=>$_W['uniacid'],'openid'=>$_W['openid']));
            if( $num >= $this->module['config']['limitnum'] ) Util::echoResult(201,'你不能再帮别人点赞了');
        }    

        $data = array(
            'uniacid' => $_W['uniacid'],
            'openid' => $_W['openid'],
            'wid' => $wish['id'],
            'time' => TIMESTAMP,
        );

        $res = pdo_insert('zofui_chickenwish_zanlog',$data);
        if( $res ) {
            Util::addOrMinusOrUpdateData('zofui_chickenwish_wish',array('zan'=>1),$wish['id']);
            Util::deleteCache('w',$wish['openid']);
            Util::echoResult(200,'成功为'.$wish['title'].'点赞');
        }else{
            Util::echoResult(201,'点赞失败');
        }

    // 领奖
    }elseif( $_GPC['op'] == 'takeprize' ){

        $prizerule = pdo_get('zofui_chickenwish_prizerule',array('uniacid'=>$_W['uniacid'])); 

        if( empty( $this->module['config']['iszan']) ) Util::echoResult(201,'未开启点赞功能');
        if( empty( $prizerule ) || empty( $prizerule['isprize'] ) ) Util::echoResult(201,'未开启奖品功能'); 

        $wish = model_wish::getWish( $_W['openid'] );    
        if( empty( $wish ) ) Util::echoResult(201,'请先参与活动');

        // 是否已经领取
        $prizelog = pdo_get('zofui_chickenwish_prizelog',array('uniacid'=>$_W['uniacid'],'openid'=>$_W['openid']));   
        if( !empty( $prizelog ) ) Util::echoResult(201,'您已经领取过了');


        if( empty( $prizerule['getedtype'] ) ) { // 某点即可领取

            $prize = pdo_get('zofui_chickenwish_prize',array('uniacid'=>$_W['uniacid'],'id'=>$_GPC['id']));
            if( empty( $prize ) ) Util::echoResult(201,'奖品不存在');
            if( $prize['stock'] <= 0 ) Util::echoResult(201,'奖品已经被领完了');

            if( $wish['zan'] < $prizerule['getedvalue'] ) Util::echoResult(201,'您的人气值还不够');


            if( empty($prize['type']) ){ // 红包

                $issending = Util::getCache('issend',$_W['openid']);
                if( !empty( $issending ) ) Util::echoResult(201,'稍等会再试');
                Util::setCache( 'issend',$_W['openid'],1 );


                $arr['openid'] = $_W['openid'];
                $arr['fee'] = $prize['money'];
                $pay = new WeixinPay;

                if($this->module['config']['paytype'] == 0){ // 红包发放
                    $arr['hbname'] = '活动奖励';
                    $arr['body'] = '活动奖励';
                    $res = $pay -> sendhongbaoto($arr,$this);
                    $resstr = '领取成功，已发放到你和公众号对话的聊天框中，请查收。';

                }else{
                    $res = $pay -> sendMoneyToUser($arr,$this);
                    $resstr = '领取成功，已发放到您的微信钱包内。';
                }

                if($res['result_code'] != 'SUCCESS'){
                    Util::deleteCache('issend',$_W['openid']);
                    Util::echoResult(201,'领取失败，'.$res['err_code'].$res['err_code_des'].'请联系客服。');
                }

                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'openid' => $_W['openid'],
                    'pid' => $prize['id'],
                    'status' => 1,
                ); 
            }else{ // 其他

                if( empty( $prizerule['taketype'] ) ){
                    if( empty( $_GPC['name'] ) || empty( $_GPC['tel'] ) || empty( $_GPC['add'] ) ) {
                        Util::echoResult(201,'请填写完整的信息');
                    }
                    $add = iserializer( array('name'=>$_GPC['name'],'tel'=>$_GPC['tel'],'add'=>$_GPC['add']) );
                }

                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'openid' => $_W['openid'],
                    'pid' => $prize['id'],
                    'status' => 0,
                    'add' => $add,
                );
            }


        }else{ // 排名领奖

            $myrank = model_user::getMyRanK( $_W['openid'] );
            if( ($myrank['rank'] + 1) > $prizerule['getmem'] ) Util::echoResult(201,'您没有获奖');


            if( empty( $prizerule['taketype'] ) ){
                if( empty( $_GPC['name'] ) || empty( $_GPC['tel'] ) || empty( $_GPC['add'] ) ) {
                    Util::echoResult(201,'请填写完整的信息');
                }
                $add = iserializer( array('name'=>$_GPC['name'],'tel'=>$_GPC['tel'],'add'=>$_GPC['add']) );
            }

            $data = array(
                'uniacid' => $_W['uniacid'],
                'openid' => $_W['openid'],
                'status' => 0,
                'add' => $add,
            );

        }


        Util::deleteCache('issend',$_W['openid']);
        $res = pdo_insert('zofui_chickenwish_prizelog',$data);
        $iid = pdo_insertid();

        if( $res ){
            $code = rand(111,999).$iid;
            pdo_update('zofui_chickenwish_prizelog',array('code'=>$code),array('id'=>$iid));

            Util::addOrMinusOrUpdateData('zofui_chickenwish_prize',array('stock'=>-1),$prize['id']);
            Util::deleteCache('prize','all');
            $rrstr = '已经提交';
            if( !empty( $resstr ) )  $rrstr = $resstr;

            Util::echoResult(200,$rrstr);
        }else{
            Util::echoResult(201,'领取失败');
        }

    }
	

    elseif( $_GPC['op'] == 'uploadimages' ){

        load() -> model('account');
        load()->func('communication');
        
        $access_token = WeAccount::token(); 
        $url = 'https://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.'&media_id='.$_GPC['serverId'];
        $resp = ihttp_get($url);
        
        $res = Util::uploadImageInWeixin($resp);

        if( $res['url'] ) {
            Util::echoResult(200,'好',$res);
        }else{
            Util::echoResult(201,'上传失败');
        }

    }

	elseif($_GPC['op'] == 'atlas'){

		echo <<<div
people.png
size: 2048,2048
format: RGBA8888
filter: Linear,Linear
repeat: none
A01
  rotate: false
  xy: 1945, 1895
  size: 100, 150
  orig: 116, 150
  offset: 7, 0
  index: -1
A02
  rotate: false
  xy: 1414, 1674
  size: 96, 147
  orig: 116, 149
  offset: 8, 2
  index: -1
Body
  rotate: false
  xy: 2, 490
  size: 351, 421
  orig: 353, 423
  offset: 1, 1
  index: -1
Foot_L_01
  rotate: false
  xy: 2, 19
  size: 152, 143
  orig: 154, 145
  offset: 1, 1
  index: -1
Foot_L_02
  rotate: false
  xy: 1803, 1905
  size: 140, 140
  orig: 142, 142
  offset: 1, 1
  index: -1
Foot_L_03
  rotate: false
  xy: 174, 230
  size: 159, 258
  orig: 161, 260
  offset: 1, 1
  index: -1
Foot_L_04
  rotate: false
  xy: 455, 1020
  size: 170, 324
  orig: 172, 326
  offset: 1, 1
  index: -1
Foot_R_01
  rotate: false
  xy: 1501, 1902
  size: 152, 143
  orig: 154, 145
  offset: 1, 1
  index: -1
Foot_R_02
  rotate: false
  xy: 455, 878
  size: 140, 140
  orig: 142, 142
  offset: 1, 1
  index: -1
Foot_R_03
  rotate: false
  xy: 1021, 1567
  size: 159, 258
  orig: 161, 260
  offset: 1, 1
  index: -1
Foot_R_04
  rotate: false
  xy: 2, 164
  size: 170, 324
  orig: 172, 326
  offset: 1, 1
  index: -1
HandNew01
  rotate: false
  xy: 1306, 1887
  size: 193, 158
  orig: 193, 158
  offset: 0, 0
  index: -1
HandNew02
  rotate: false
  xy: 1306, 1887
  size: 193, 158
  orig: 193, 158
  offset: 0, 0
  index: -1
Hand_L_01
  rotate: false
  xy: 444, 702
  size: 116, 174
  orig: 118, 176
  offset: 1, 1
  index: -1
Hand_L_02
  rotate: false
  xy: 1182, 1560
  size: 114, 265
  orig: 114, 265
  offset: 0, 0
  index: -1
Hand_L_03
  rotate: false
  xy: 1298, 1673
  size: 114, 148
  orig: 116, 150
  offset: 1, 1
  index: -1
Hand_R_01
  rotate: false
  xy: 444, 526
  size: 116, 174
  orig: 118, 176
  offset: 1, 1
  index: -1
Hand_R_02
  rotate: false
  xy: 781, 1302
  size: 114, 265
  orig: 114, 265
  offset: 0, 0
  index: -1
Hand_R_03
  rotate: false
  xy: 1298, 1524
  size: 114, 147
  orig: 116, 149
  offset: 1, 1
  index: -1
HeadA02
  rotate: false
  xy: 2, 913
  size: 451, 431
  orig: 451, 431
  offset: 0, 0
  index: -1
HeadA1
  rotate: false
  xy: 2, 1346
  size: 650, 699
  orig: 650, 699
  offset: 0, 0
  index: -1
Neck
  rotate: false
  xy: 174, 150
  size: 123, 78
  orig: 123, 78
  offset: 0, 0
  index: -1
Patch
  rotate: false
  xy: 1306, 1823
  size: 168, 62
  orig: 170, 64
  offset: 1, 1
  index: -1
ZhuFu
  rotate: false
  xy: 654, 1370
  size: 125, 455
  orig: 125, 505
  offset: 0, 16
  index: -1
apple01
  rotate: false
  xy: 1655, 1903
  size: 146, 142
  orig: 150, 144
  offset: 2, 2
  index: -1
chunlianBG
  rotate: false
  xy: 654, 1827
  size: 650, 218
  orig: 650, 218
  offset: 0, 0
  index: -1
chunlianJuan
  rotate: false
  xy: 897, 1311
  size: 37, 256
  orig: 38, 256
  offset: 1, 0
  index: -1
maobi
  rotate: false
  xy: 355, 545
  size: 87, 366
  orig: 87, 366
  offset: 0, 0
  index: -1
smoke01
  rotate: false
  xy: 781, 1569
  size: 238, 256
  orig: 238, 256
  offset: 0, 0
  index: -1
div;

	}