	var tool = {
		size : {
			width : $(window).width(),
			height : $(window).height()
		},
		url : function(doo,opp,obj){
			var str = '';
			if(typeof obj != 'undefined'){
				for (var i in obj ) {
					str += '&'+i+'='+obj[i];
				}
			}
			return window.sysinfo.siteroot+'app/index.php?i='+window.sysinfo.uniacid+'&c=entry&do='+doo+'&op='+opp+'&m=zofui_chickenwish'+str;
		},
		ajax : function(url,type,datatype,data,succall,notModel,comcall){
			$.ajax({
				url : url,
				type : type,
				datatype : datatype,
				data : data,
				success : function(res){
					if( succall ) succall($.parseJSON(res));
				},
				beforeSend : function(){
					if(!notModel) tool.showModal(true);
				},
				complete : function(){
					if(!notModel) tool.showModal(false);
					if(!notModel) if(comcall) comcall();
				}
			})
		},
		showModal : function(bool){
			if(bool){
				if($('#modal').length > 0){
					$('#modal').show();
				}else{
					var div = '<div id="modal" style="position:fixed;z-index:9999;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0)">\
						<img style="position:absolute;top:40%;left:50%;width:1rem;margin-left:-0.5rem;background: #000;padding: 12px;border-radius: 10px;" \
						src="../addons/zofui_chickenwish/public/images/loading.gif"></div>';
					$('body').append(div);
				}
			}else{
				$('#modal').hide();
			}
		},
		alert : function(str,callfun){
			if($('#alert').length > 0){
				$('#alert #alertstr').text(str);
				$('#alert').show();
			}else{
			var div = '<div id="alert" style="position:fixed;top:0;left:0;width:100%;height:100%;background: rgba(0,0,0,0.5);z-index:888;font-size: 26px;">\
					<ul class="animatealert" style="box-shadow:0 2px 16px #000, 0 0 1px #ddd, 0 0 1px #ddd;width:80%;height:auto;background:#fff;border-radius:10px;position:absolute;top: 50%;left:10%;margin-top: -110px;padding:10px;">\
						<li style="height: auto;padding:10px;text-align: center;font-size: 14px;" id="alertstr">'+str+'</li>\
						<li style="text-align:center;border-top:1px solid #C5C5C5;padding:10px;color: #0894ec;font-size: 16px;" id="comfire">确定</li>\
					</ul>\
				</div>';
				$('body').append(div);
			}
			$('#comfire').off('touchstart');
			$('#comfire').on('touchstart',function(e){
				$('#alert').hide();
				if( callfun ) callfun();
				e.preventDefault();
			});
		},
		updateTime : function () {
			var date = new Date();
			var time = date.getTime();  //当前时间距1970年1月1日之间的毫秒数 
			$(".lasttime").each(function(i){
				var endTime = $(this).attr('data-time') + '000'; //结束时间字符串
				var lag = (endTime - time); //当前时间和结束时间之间的秒数	
				if(lag > 0){
					var second = Math.floor(lag/1000%60);     
					var minite = Math.floor(lag/1000/60%60);
					var hour = Math.floor(lag/1000/60/60%24);
					var day = Math.floor(lag/1000/60/60/24);
					second = second.toString().length == 2 ? second : '0'+second;
					minite = minite.toString().length == 2 ? minite : '0'+minite;
					hour = hour.toString().length == 2 ? hour : '0'+hour;
					day = day.toString().length == 2 ? day : '0'+day;
				}else{
					location.href = tool.url('index','index');
				}
				$(this).find('.day').text(day);
				$(this).find('.hour').text(hour);
				$(this).find('.minite').text(minite);
				$(this).find('.second').text(second);				
			});
			setTimeout(function(){tool.updateTime()},1000);
		},
		toTop : function(){
			var html = '<a id="backTop" href="javascript:;" data-title="返回顶部" \
						style="border: 1px solid #fff;border-radius: 50%;display:none;cursor:pointer;position:fixed;text-decoration:none;\
						right:2rem;bottom:2rem;width:4.2rem;height:4.2rem;background: url(../addons/zofui_chickenwish/public/images/top.png) no-repeat 1px 0;background-size:cover;background-color: #fff;"></a>';
			if( $('#backTop').length <= 0 ) $('body').append(html);
				
			$(window).scroll(function(){
				var viewheight = $(window).height();
				var top = $(window).scrollTop();
				if(top >= viewheight*1.5){
					$('#backTop').show();
				}else{
					$('#backTop').hide();
				}
			})

			$('body').off('touchstart','#backTop').on('touchstart','#backTop',function(e){
				$(window).scrollTop(0);
				e.preventDefault();
			})
		},
		regex : {
			tel : /^1[34578]\d{9}/
		},
		random : function(min,max){
			return min + Math.round(Math.random()*max);
		}
	};

	// 分享	
	var wxJs = {};
	wxJs.share = function(shareData){
		wx.ready(function (){
			//分享朋友
			wx.onMenuShareAppMessage({
				title: shareData.sharetitle,
				desc: shareData.sharedesc,
				link: shareData.sharelink,
				imgUrl:shareData.shareimg,
				trigger: function (res) {
				},
				success: function (res) {
				},
				cancel: function (res) {
				},
				fail: function (res) {
					alert(JSON.stringify(res));
					//alert(shareData.title)
				}
			});
			//分享QQ
			wx.onMenuShareQQ({
				title: shareData.sharetitle,
				desc: shareData.sharedesc,
				link: shareData.sharelink,
				imgUrl:shareData.shareimg,
				trigger: function (res) {
				},
				success: function (res) {
				},
				cancel: function (res) {
				},
				fail: function (res) {
					//alert(JSON.stringify(res));
					//alert(shareData.title)
				}
			});
			//分享QQ空间
			wx.onMenuShareQZone({
				title: shareData.sharetitle,
				desc: shareData.sharedesc,
				link: shareData.sharelink,
				imgUrl:shareData.shareimg,
				trigger: function (res) {
				},
				success: function (res) {
				},
				cancel: function (res) {
				},
				fail: function (res) {
					//alert(JSON.stringify(res));
				}
			});	
			//朋友圈
			wx.onMenuShareTimeline({
				title: shareData.sharetitle,
				link: shareData.sharelink,
				imgUrl:shareData.shareimg,
				trigger: function (res) {
				},
				success: function (res) {
				},
				cancel: function (res) {
				},
				fail: function (res) {
					//alert(JSON.stringify(res));
				}
			});
		});
	}

	$(function(){
		wx.ready(function (){
			wxJs.share(settings);
		})		
	})