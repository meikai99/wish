/*******************************
 * Description:图片预加载
 * 使用方法：_PreLoadImg(['img/img1.jpg','img/img2.jpg','img/img3.jpg',...],function(){ //加载完成后执行 });
 *******************************/
function _PreLoadImg(b,e){var c=0,a={},d=0;for(src in b){d++}for(src in b){a[src]=new Image();a[src].onload=function(){if(++c>=d){e(a)}};a[src].src=b[src]}};


/*******************************
 * 字符串去空格
 *******************************/
String.prototype.trim=function(){
	return this.replace(/(^\s*)|(\s*$)/g, "");
}

String.prototype.ltrim=function(){
	return this.replace(/(^\s*)/g,"");
}

String.prototype.rtrim=function(){
	return this.replace(/(\s*$)/g,"");
}

/*
向url中添加参数,如果已存在参数则更新参数值
*/
function updateUrlParam(url,paramName,paramValue) {  
	if (/\?/g.test(url)) {
		var reg = new RegExp("([?&]+)" + paramName + "=([^&]*)","gi");
		var rs = reg.exec(url);
		if(rs){
			url = url.replace(reg,rs[1]+paramName+"="+paramValue);
		}else{
			url += "&" + paramName + "=" + paramValue;
		}
	} else if(paramName)	{
		url += "?" + paramName + "=" + paramValue;
	}
	return url;
}

function isPC()
{  
   var userAgentInfo = navigator.userAgent;  
   var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");  
   var flag = true;  
   for (var v = 0; v < Agents.length; v++) {  
	   if (userAgentInfo.indexOf(Agents[v]) > 0) { flag = false; break; }  
   }  
   return flag;  
}  

function isWeixin() {
	var a = navigator.userAgent.toLowerCase();
	return "micromessenger" == a.match(/MicroMessenger/i) ? !0 : !1
}

/**
	* 获取URL后的参数
	* @param name
	*/
    function getQueryString(name,v) {
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
		var r = window.location.search.substr(1).match(reg);
		if (r != null) return unescape(r[2]); 
		return v ? v : '';
	}