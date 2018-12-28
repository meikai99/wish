/**
 * 微互动统计相关的Javascript API，功能包括：
 *
 * //统计前初始化统计参数  格式、属性值如下：
 * var uuParams ={
 *     tenantId:{tenantId},              //商户id   0-未知
 *     modName:{modName},                //所属模块名 whd-微互动插件 magazine-微杂志 wuye-物业  evip-会员卡二期
 *     aid:{aid},                        //活动id   0-未知
 *     openid:{openid},                  //微信openid
 *     fromWxid:{fromWxid},              //来源微信openid（谁分享的地址）
 *     networkType:{networkType},        //当前网络类型 'wifi'-wifi网络 'edge'-非wifi,包含3G/2G  'fail'-网络断开连接 'wwan'-2g或者3g
 *     attent:{attent},                  //当前openid是否关注公众号  0-未关注  1-关注
 *     srcType:{srcType},                //来源类型 0-直接访问 1-公众号 2-朋友圈 3-朋友 4-扫码 5-摇一摇
 *     shareType:{shareType},            //分享事件才有值 2-分享给朋友圈 3-分享给好友
 *     shareUrl:"{shareUrl},             //分享事件才有值,需要被统计的分享地址
 *     site_module:"{site_module}",	     //插件名作为频道名
 *	   channel:"{channel}",			     //渠道号
 *	   eccid:"{eccid}",				     //ec企业号
 *     ecuid:"{ecuid}",				     //ec员工号
 *     tag_key:{tag_key}                 //标签key 值规则：产品_唯一标识  比如:whd_k8rSfYRcx7rga7fQ(微互动_某活动akey)
 * }
 *
 * 1、页面打开统计        uuStatApi.page();
 * 2、分享给微信好友统计  uuStatApi.sendAppMessage();
 * 3、分享给朋友圈统计    uuStatApi.sendTimeline();
 */

var uuStatApi = (function () {

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

    /*
     * 页面打开
     *
     */
    function page(){
        uuParams.logType = 'page';
        stat();
    }


    /*
     * 分享给朋友圈
     *
     */
    function sendTimeline(){
        uuParams.logType = 'share';
        uuParams.shareType = 2 ;
        stat();
    }

    /*
     * 分享给好友
     *
     */
    function sendAppMessage(){
        uuParams.logType = 'share';
        uuParams.shareType = 3 ;
        stat();
    }

	/*
     * 访问类型
     *
     */
    function sendEvent(event_name,event_value){
        uuParams.logType = 'event';
        uuParams.event_name = event_name;
        uuParams.event_value = event_value;
        stat();
    }

	function clickBtn() {
		
        var pattern = new RegExp("tel:", i),
            statBtn = document.querySelectorAll(".statBtn"),
            statBtnA = document.querySelectorAll("a");
        if(statBtnA) {
            for(var i = 0; i < statBtnA.length; i++){

                if(pattern.test(statBtnA[i].getAttribute("href"))){
                    statBtnA[i].addEventListener("click", function(e){
                     
                        var hrefStr,
                        	type,
                        	telType;
               			
						getThisDom(e.target);
	                	function getThisDom(dom){
							if(dom.localName.indexOf('a') == -1){
							    getThisDom(dom.parentNode);            		
							} else {
								hrefStr = dom.href;
								type = dom.getAttribute("uu-stat");
								telType = (type && type != "undefined") ? type : "tel";
								
								if(hrefStr){
		                            sendEvent(telType,hrefStr.substring(4));
		                        } 
							}
	                	}

                         
                    
                    });
                } else {
                    statBtnA[i].addEventListener("click", function(e){
                    	var type = e.target.getAttribute("uu-stat"),
                    		linkType = (type && type != "undefined") ? type : "link";
                    	
                    	if(linkType != "no"){
							sendEvent(linkType, 1);
                    	}

                    });
                }
            }
        }
        if(statBtn) {
            for(var b = 0; b < statBtn.length; b++) {
            
                statBtn[b].addEventListener("click", function(e){
                	
                	getThisDom(e.target);
                	function getThisDom(dom){
						if(dom.className.indexOf('statBtn') == -1){
						    getThisDom(dom.parentNode);            		
						} else {
							sendEvent(dom.getAttribute("uu-stat"),1);
						}
                	}	 
                });
            }
        }
    }

    function stat(){ 
        var pageUrl = encodeURIComponent(uuParams.pageUrl ? uuParams.pageUrl : window.location.href),    //当前页面地址
            pageTitle = encodeURIComponent(uuParams.pageTitle ? uuParams.pageTitle : document.title), //当前页面标题
			tenantId = uuParams.tenantId ? uuParams.tenantId : getQueryString("tenantId","0"), 
			modName = uuParams.modName ? uuParams.modName : getQueryString("modName"), 
            aid = uuParams.aid ? uuParams.aid : getQueryString("aid","0"),                 
            openid = uuParams.openid ? uuParams.openid : getQueryString("openid","unknown"),           
            fromWxid = uuParams.fromWxid ? uuParams.fromWxid : getQueryString("fromWxid","unknown"),   
            networkType = uuParams.networkType ? uuParams.networkType : getQueryString("networkType","unknown"),
            attent = uuParams.attent ? uuParams.attent : getQueryString("attent","0"),                    
            srcType = uuParams.srcType ? uuParams.srcType : getQueryString("srcType"),
            srcId = uuParams.srcId ? uuParams.srcId : getQueryString("srcId"),                  
            logType = uuParams.logType ? uuParams.logType : getQueryString("logType",'page'),                 
            shareType = uuParams.shareType ? uuParams.shareType : getQueryString("shareType"),            
            shareUrl = uuParams.shareUrl ? uuParams.shareUrl : getQueryString("shareUrl"),               
	        user_tags = uuParams.user_tags ? uuParams.user_tags : getQueryString("user_tags"),
	        site_module = uuParams.site_module ? uuParams.site_module : getQueryString("site_module"),
	        channel = uuParams.channel ? uuParams.channel : getQueryString("channel"),
            chid = uuParams.chid ? uuParams.chid : getQueryString("chid"),
            chtype = uuParams.chtype ? uuParams.chtype : getQueryString("chtype"),
	        eccid = uuParams.eccid ? uuParams.eccid : getQueryString("eccid"),
	        ecuid = uuParams.ecuid ? uuParams.ecuid : getQueryString("ecuid"),
			ecbid = uuParams.ecbid ? uuParams.ecbid : getQueryString("ecbid"),
            event_name = uuParams.event_name ? uuParams.event_name : getQueryString("event_name"),
            event_value = uuParams.event_value ? uuParams.event_value : getQueryString("event_value"),
			tag_key = uuParams.tag_key ? uuParams.tag_key : getQueryString("tag_key"),
            rnd = new Date().getTime();                                  

        var statUrl = "http://hd.playwx.com/page/stat"
            + "?pageUrl=" + pageUrl
            + "&pageTitle=" + pageTitle
			+ "&tenantId=" + tenantId
			+ "&modName=" + modName
            + "&aid=" + aid
            + "&wxid=" + openid
            + "&fromWxid=" + fromWxid
            + "&networkType=" + networkType
            + "&attent=" + attent
            + "&srcType=" + srcType
            + "&srcId=" + srcId
	        + "&user_tags=" + user_tags
	        + "&site_module=" + site_module
	        + "&channel=" + channel
            + "&chid=" + chid
            + "&chtype=" + chtype
	        + "&eccid=" + eccid
	        + "&ecuid=" + ecuid
		    + "&ecbid=" + ecbid
            + "&logType=" + logType
            + "&event_name=" + event_name
            + "&event_value=" + event_value
			+ "&tag_key=" + tag_key;
            
            //如果是分享事件统计,需要统计分享类型和分享地址
            if(logType!="" && logType=="share"){
                statUrl += "&shareType=" + shareType;
                statUrl += "&shareUrl=" + shareUrl;
            }

        var img = new Image();
            img.onload = img.onerror = function() {};
            img.src = statUrl + "&rnd=" + rnd;
    }

    return {
        page             :page,
        sendTimeline     :sendTimeline,
        sendAppMessage   :sendAppMessage,
		sendEvent  :sendEvent,
		clickBtn  :clickBtn
    };
})();
