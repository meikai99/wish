<?php

function foodAutoLoad($classname)
{
	$file = MODULE_ROOT . "class/" . $classname . ".class.php";
	if (file_exists($file)) {
		require_once $file;
	}
}
spl_autoload_register("foodAutoLoad");
class Data
{
	static function webMenu()
	{
		global $_W;
		if (function_exists("buildframes")) {
			$myframes = buildframes("account");
			$seturl = $myframes["section"]["platform_module_common"]["menu"]["platform_module_settings"]["url"];
		}
		if (empty($seturl)) {
			$seturl = $_W["siteroot"] . "web/index.php?c=profile&a=module&do=setting&op=set&m=" . MODULE;
		}
		$data = array("setting" => array("name" => "参数设置", "icon" => "https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_statistics.png", "list" => array(array("op" => "wish", "name" => "参数设置", "url" => $seturl))), "data" => array("name" => "拜年记录", "icon" => "https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_management.png", "list" => array(array("op" => "wish", "name" => "拜年记录", "url" => Util::webUrl("data", array("op" => "wish")))), "toplist" => array("wish")), "prize" => array("name" => "奖品管理", "icon" => "https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_management.png", "list" => array(array("op" => "set", "name" => "获奖规则", "url" => Util::webUrl("prize", array("op" => "set"))), array("op" => "list", "name" => "奖品管理", "url" => Util::webUrl("prize", array("op" => "list"))), array("op" => "geted", "name" => "领奖记录", "url" => Util::webUrl("prize", array("op" => "geted")))), "toplist" => array("set", "geted", "list")), "explain" => array("name" => "活动说明", "icon" => "https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_management.png", "list" => array(array("op" => "create", "name" => "活动说明", "url" => Util::webUrl("explain", array("op" => "create")))), "toplist" => array("create")), "index" => array("name" => "模块使用说明", "icon" => "https://res.wx.qq.com/mpres/htmledition/images/icon/menu/icon_menu_ad.png", "url" => Util::webUrl("index")));
		$set = Util::getModuleConfig();
		if (empty($set["iszan"])) {
			unset($data["prize"]);
		}
		return $data;
	}
}
class topbal
{
	static function wishList()
	{
		global $_W;
		return array("order" => array(array("value" => '', "name" => "排序方式", "url" => WebCommon::logUrl("order", '')), array("value" => 1, "name" => "点赞值排序", "url" => WebCommon::logUrl("order", "1"))));
	}
	static function prizelogList()
	{
		global $_W;
		return array("status" => array(array("value" => '', "name" => "状态筛选", "url" => WebCommon::logUrl("status", '')), array("value" => 1, "name" => "待发奖", "url" => WebCommon::logUrl("status", "1")), array("value" => 2, "name" => "已发奖", "url" => WebCommon::logUrl("status", "2")), array("value" => 3, "name" => "已拒绝", "url" => WebCommon::logUrl("status", "3"))), "search" => array("op" => "code", "placeholder" => "输入兑奖编码"));
	}
	static function drawlogList()
	{
		global $_W;
		return array("order" => array(array("value" => '', "name" => "排序方式", "url" => WebCommon::logUrl("order", '')), array("value" => 1, "name" => "金额排序", "url" => WebCommon::logUrl("order", "1"))), "by" => array(array("value" => '', "name" => "倒序", "url" => WebCommon::logUrl("by", '')), array("value" => 1, "name" => "正序", "url" => WebCommon::logUrl("by", "1"))));
	}
	static function userList()
	{
		global $_W;
		return array("order" => array(array("value" => '', "name" => "排序方式", "url" => WebCommon::logUrl("order", '')), array("value" => 1, "name" => "心愿值排序", "url" => WebCommon::logUrl("order", "1"))), "by" => array(array("value" => '', "name" => "倒序", "url" => WebCommon::logUrl("by", '')), array("value" => 1, "name" => "正序", "url" => WebCommon::logUrl("by", "1"))), "search" => array("op" => "user", "placeholder" => "输入会员昵称"));
	}
}