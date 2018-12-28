<?php

defined('IN_IA') or die('Access Denied');
define('MODULE_ROOT', IA_ROOT . '/addons/zofui_chickenwish/');
define('MODULE_URL', $_W['siteroot'] . '/addons/zofui_chickenwish/');
define('MODULE', 'zofui_chickenwish');
require_once MODULE_ROOT . 'class/autoload.php';
class Zofui_chickenwishModule extends WeModule
{
	public function settingsDisplay($settings)
	{
		global $_W, $_GPC;
      	
		$settings['advert'] = iunserializer($settings['advert']);
		$settings['bgimg'] = iunserializer($settings['bgimg']);
		$settings['rangetime']['start'] = $settings['start'];
		$settings['rangetime']['end'] = $settings['end'];
		$music = array(array('name' => '尝试一切', 'url' => 'https://cdn.s.shangjiadao.cn/source/mp3/common/wangluo/fengkuangdongwucheng.mp3'), array('name' => '新年汪汪', 'url' => 'http://res1.eqh5.com/2866717c3415445ea9c2e0ee9a7d2d70.mp3'), array('name' => '恭喜发财', 'url' => 'http://res1.eqh5.com/ccaa45a4343f46d9b29c8092f00949cf.mp3'), array('name' => '喜气洋洋', 'url' => 'http://res1.eqh5.com/7a2a0ecc351641b2ba54e68bccd8d842.mp3'), array('name' => '吴郡', 'url' => 'http://res1.eqh5.com/f05c8cbe1a5948b0b48fa4397a0e9d0d.mp3'), array('name' => '新年好', 'url' => 'http://res1.eqh5.com/6ac8ec7aa5024bfd9f9ebe95088f1ace.mp3'), array('name' => '蝶飞花舞', 'url' => 'http://res1.eqh5.com/c2bd090eb3f94b7f909b2366724ccd2f.mp3'), array('name' => '新年歌', 'url' => 'http://res1.eqh5.com/d1991f2f4b264e8088d19db3be012b69.mp3'), array('name' => '新年快乐', 'url' => 'http://res1.eqh5.com/8254beea85404b49be94dcdd6777bcd2.mp3'), array('name' => '新年锣鼓', 'url' => 'http://res1.eqh5.com/874c4b225a774f13851caee19f9f7fb0.mp3'), array('name' => '欢迎新年', 'url' => 'http://res1.eqh5.com/5c029c1cb2f548a7a3a7dcf1ab2f702a.mp3'), array('name' => '喜洋洋', 'url' => 'http://res1.eqh5.com/1f84645f95cc4ad5b7725b5d092f1874.mp3'), array('name' => '新春乐', 'url' => 'http://res1.eqh5.com/e694f86889fe481581ad3c53c1888ab3.mp3'), array('name' => '纯音乐1', 'url' => 'http://res1.eqh5.com/store/6d8857880777f0519433642992262296.mp3'), array('name' => '纯音乐2', 'url' => 'http://res1.eqh5.com/e8d4ecfc33b54cc18ab7870a70f34111.mp3'), array('name' => '纯音乐3', 'url' => 'http://res1.eqh5.com/5a2af58b15954357a18ae1f9f85cff30.mp3'), array('name' => '祝福你', 'url' => 'http://res1.eqh5.com/3c7199325d804e38b7c5af6d73e7f68e.mp3'), array('name' => 'keep on going', 'url' => 'http://res1.eqh5.com/store/ab7fc0401c1d9e317f0aeda3600b5030.mp3'), array('name' => 'popular song', 'url' => 'http://res1.eqh5.com/0c60dee967da4d79b3b28d8a07ff4d36.mp3'), array('name' => 'Fulfillment', 'url' => 'http://res1.eqh5.com/store/fee781fefde9b5eac01c4d52130351f6.mp3'), array('name' => 'hometown', 'url' => 'http://res1.eqh5.com/8770062197db4cbcae419b4adfb688d5.mp3'), array('name' => 'Worth It', 'url' => 'http://cdn.s.shangjiadao.cn/source/mp3/common/wangluo/worthit.mp3'), array('name' => 'Zunea-Zunea', 'url' => 'http://cdn.s.shangjiadao.cn/source/mp3/common/hot/zunea.mp3'), array('name' => 'H.N.Y', 'url' => $_W['siteroot'] . 'addons/zofui_chickenwish/public/images/500_9wUytfmppLaNQOj.mp3'));
		if (empty($settings['bgimg'])) {
			$settings['bgimg'] = array(MODULE_URL . 'public/images/p2_bg2.jpg', MODULE_URL . 'public/images/p2_bg.jpg');
		}
		if (checksubmit('submit')) {
			$_GPC = Util::trimWithArray($_GPC);
			load()->func("file");
			$r = mkdirs(MODULE_ROOT . '/cert/' . $_W['uniacid']);
			if (!empty($_GPC['cert'])) {
				$ret = file_put_contents(MODULE_ROOT . '/cert/' . $_W['uniacid'] . '/apiclient_cert.pem', trim($_GPC['cert']));
				$r = $r && $ret;
			}
			if (!empty($_GPC['key'])) {
				$ret = file_put_contents(MODULE_ROOT . '/cert/' . $_W['uniacid'] . '/apiclient_key.pem', trim($_GPC['key']));
				$r = $r && $ret;
			}
			if (!empty($_GPC['rootca'])) {
				$ret = file_put_contents(MODULE_ROOT . '/cert/' . $_W['uniacid'] . '/rootca.pem', trim($_GPC['rootca']));
				$r = $r && $ret;
			}
			if (!$r) {
				message('证书保存失败, 请保证 /addons/zofui_chickenwish/cert/ 目录可写，如果无法解决请使用上传工具将证书文件上传至' . MODULE_ROOT . '/cert/' . $_W['uniacid'] . '目录下');
			}
			foreach ((array) $_GPC["advert"] as $k => $v) {
				$_GPC['advert'][$k] = tomedia($v);
			}
			foreach ((array) $_GPC["bgimg"] as $k => $v) {
				$_GPC['bgimg'][$k] = tomedia($v);
			}
			$dat = array('paytype' => intval($_GPC['paytype']), 'appid' => $_GPC['appid'], 'mchid' => $_GPC['mchid'], 'apikey' => $_GPC['apikey'], 'advert' => iserializer($_GPC['advert']), 'bgimg' => iserializer($_GPC['bgimg']), 'title' => $_GPC['title'], 'jointype' => intval($_GPC['jointype']), 'indexbg' => tomedia($_GPC['indexbg']), 'subtitle' => $_GPC['subtitle'], 'sharetitle' => $_GPC['sharetitle'], 'sharedesc' => $_GPC['sharedesc'], 'shareimg' => $_GPC['shareimg'], 'audio' => tomedia($_GPC['audio']), 'leftzhufu' => tomedia($_GPC['leftzhufu']), 'midzhufu' => tomedia($_GPC['midzhufu']), 'rightzhufu' => tomedia($_GPC['rightzhufu']), 'wxcode' => $_GPC['wxcode'], 'ismusic' => intval($_GPC['ismusic']), 'iszan' => intval($_GPC['iszan']), 'isrank' => intval($_GPC['isrank']), 'ranknum' => intval($_GPC['ranknum']), 'start' => $_GPC['rangetime']['start'], 'end' => $_GPC['rangetime']['end'], 'limitnum' => intval($_GPC['limitnum']), 'sharetype' => $_GPC['sharetype'], 'iscusmusic' => intval($_GPC['iscusmusic']));
			if ($this->saveSettings($dat)) {
				message('保存成功', 'refresh');
			}
		}
		/*$api = 'http://123.1.174.176/app/index.php?i=1&c=entry&do=checkapi&m=zofui_tbapi';
		$res = Util::httpPost($api, array("site" => $_W["siteroot"], "module" => MODULE));
		$res = json_decode($res, true);
      	
		if ($res['status'] == 201) {
			die;
		}*/
		if (!pdo_fieldexists('zofui_chickenwish_wish', 'music')) {
			pdo_query('ALTER TABLE ' . tablename('zofui_chickenwish_wish') . ' ADD `music` varchar(500) DEFAULT NULL COMMENT \'音乐文件\';');
		}
      	
		include $this->template("web/setting");
	}
}