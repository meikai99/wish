//整数
function isInt(str)	{
//	var reg= /^[0-9]*[1-9][0-9]*$/;//正整数
//	var reg=/^-[0-9]*[1-9][0-9]*$/;//负整数
//	var reg=/^\d+$/;//非负整数（正整数 + 0）
    var reg=/^-?\d+$/;//整数
	return reg.test(str);;
}
//正整数
function isPositiveInt(str)	{
	var reg= /^[0-9]*[1-9][0-9]*$/;//正整数
	return reg.test(str);;
}
//非负整数（正整数 + 0）
function isPositiveZeroInt(str)	{
	var reg=/^\d+$/;//非负整数（正整数 + 0）
	return reg.test(str);;
}
//数字
function isNum(str)	{
	var reg = /^[0-9]+.?[0-9]*$/; 
	return reg.test(str);;
}
//检查文件名
function checkFileName(str){
	var reg = /^[\-.\w]+$/;  //'A-Za-z0-9' '_' '-' '.'
	return reg.test(str);
}
//手机号码验证信息
function isMobil(str)
{
  var reg = /(^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$)|(^0{0,1}1[3|4|5|6|7|8|9][0-9]{9}$)/;
  return reg.test(str);
} 
//检查用户名
function isUserName(str){
  	if(str == '' || str == null || str == undefined){
  		return false;
 	}
  	var reg = /^[a-zA-Z0-9\u4e00-\u9fa5]{2,12}$/;
  	return reg.test(str);
} 


