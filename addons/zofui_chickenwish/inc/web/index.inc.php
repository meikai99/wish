<?php 
	global $_W,$_GPC;
	$_W['set'] = $this->module['config'];
	
	
	include $this->template('web/index');