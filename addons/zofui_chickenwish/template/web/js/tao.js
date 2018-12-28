$(function(){

	common();



	// 拖拽
	$( ".multi-img-details" ).sortable();
	$( ".multi-img-details" ).disableSelection();	


	// 默认背景音乐
	$('#select_bgimg').click(function(){
		$('.my_model[music]').show();
	});
	$('.s_music').click(function(){
		var url = $(this).attr('url');
		$('input[name="audio"]').val(url);
		$('.my_model[music]').hide();
	});
	

	$('.play_music').click(function(){

		var status = $(this).attr('status');
		var src = $(this).next().attr('url');
		var audio = document.getElementById("bgMusic");

		$('.play_music').text('播放');
		$('.play_music').attr('status',0);
		if( status == 0 ){
			audio.src = src;
			audio.play();

			$(this).text('停止');
			$(this).attr('status',1);
		}else if( status == 1 ){
			audio.pause();
			audio.currentTime = 0;
			$('.play_music').text('播放');
			$(this).attr('status',0);
		}
		return false;
	});

	// 复制链接
	require(['jquery.zclip'], function(){
		$('.copy_url').zclip({
			path: './resource/components/zclip/ZeroClipboard.swf',
			copy: function(){
				return $(this).attr('data-href');
			},
			afterCopy: function(){
				webAlert('复制成功');
			}
		});
	});	
	// 清理缓存
	$('.deletecache').click(function(){
		var type = $(this).attr('type');
		var str = '确定删除吗？';
		if( type == 'delete' ) str = '确定清空所有数据吗？';

		if(confirm( str )){
			Http('post','json','deletecache',{type:type},function(data){
				webAlert(data.res);
			},true);
		}
	});


	// 导入数据 改变显示文字
	$('input[name=inputfile]').change(function(){
		var v = $(this).val();
		$(this).prev().text(v);
	});


	// 编辑排序
	var nowvalue;
	$('.edit_number_input').focus(function(){
		$(this).addClass('edit_number_input_act');
		nowvalue = $(this).val();
	});
	$('.edit_number_input').blur(function(){
		$(this).removeClass('edit_number_input_act');
		if(nowvalue == $(this).val()) return false;
		var data = {
			type : $(this).attr('inputtype'),
			value : $(this).val(),
			name : $(this).attr('inputname'),
			id : $(this).attr('id')
		};
		Http('post','html','editvalue',data,function(data){},true);
	});


	// 搜索
	$('.js_search').click(function(){
		$(this).parents('form').submit();
	});

	// 拉黑和恢复
	$('.edit_user').click(function(){
		var data = {
			type : $(this).attr('type'),
			id : $(this).attr('id')
		};
		if(confirm('确定执行操作吗？')){
			Http('post','html','edituser',data,function(data){
				if(data == 1){
					alert('操作完成');
					location.href = "";
				}else{
					alert('操作失败');
				}
			},true);
		}
	});


	// 发货
	$('.confirm_send').click(function(){
		var data = {
			id : $(this).attr('id'),
			name : $(this).parents('.dropdown_data_list').find('input[name="name"]').val(),
			num : $(this).parents('.dropdown_data_list').find('input[name="num"]').val(),
		};
		if(confirm('确定发奖吗？')){
			Http('post','json','sendprize',data,function(res){
				if(res.status == 200){
					webAlert('操作完成');
				}else{
					webAlert(res.res);
				}
			},true);
		}
	});

	// 修改点赞值
	$('.confirm_editzan').click(function(){
		var data = {
			id : $(this).attr('id'),
			num : $(this).parents('.dropdown_data_list').find('input[name="num"]').val(),
		};
		if(confirm('确定修改吗？')){
			Http('post','json','editzan',data,function(res){
				if(res.status == 200){
					webAlert('操作完成');
					setTimeout(function(){
						location.href = "";
					},800);
				}else{
					webAlert(res.res);
				}
			},true);
		}
	});

	// 切换参数设置
	$('.js_top').click(function(){
		$('.js_top').removeClass('selected');
		var thisclass = $(this).attr('showme');
		$(this).addClass('selected');
		$('.settings_group').each(function(){
			if($(this).hasClass(thisclass)){
				$(this).show();
			}else{
				$(this).hide();
			}
		})
	})


})

	function common(){

		$('.topbar_jsbtn').on('click',function(){
			var type = $(this).attr('js');
			$('.my_model['+type+']').show();
		});

		$('.model_close').click(function(){
			$(this).parents('.my_model').hide();
		});

		//点击相应位置隐藏筛选/下拉
		$('body').on('click',function(e) {
			if($(e.target).parents('.dropdown_topbar').length <= 0){
				$('.dropdown_menu').each(function(){
					var $this = $(this);
					if($this.hasClass('open')) $this.removeClass('open');
				})
			}
		});	

		// table内编辑框
		$('.drop_down_editbtn').click(function(){
			$('.jsDropdownsList').hide();
			$(this).next().find('.jsDropdownsList').toggle();
		})
		$('body').on('click','.dropdown_edit_cancel',function(){
			$(this).parents('.jsDropdownsList').hide();
		});

		// 自动选择单选框
		$('.frm_radio').each(function(){
			var $this = $(this);

			if($this.attr('checked')){
				$this.parents('.frm_radio_label').addClass('selected');
			}
		});

		// checkbox
		$('body').on('click','.frm_radio_label',function(e){
			e.preventDefault();
			
			var $this = $(this);
			var name = $this.find('input[type=radio]').prop('name');
			
			$('input[name='+name+']').each(function(){
				$(this).prop('checked',false);
				$(this).parents('.frm_radio_label').removeClass('selected');
			})
			$this.addClass('selected').find('input').prop('checked',true);
		});


		// 复选框 包括全选
		$('body').on('click','.frm_checkbox_label',function(e){
			var checkbox = $(this).find('input[type=checkbox]');
			var isall = $(this).prop('for') == 'selectAll';
			if(checkbox.prop("checked")){
				checkbox.prop("checked",false);
				$(this).removeClass('selected');
				if(isall){
					$('tbody input[type=checkbox]').each(function(){
						$(this).parents('.frm_checkbox_label').removeClass('selected');
						$(this).prop("checked",false);
					})
				}
			}else{
				checkbox.prop("checked",true);
				$(this).addClass('selected');
				if(isall){
					$('tbody input[type=checkbox]').each(function(){					
						$(this).parents('.frm_checkbox_label').addClass('selected');
						$(this).prop("checked",true);
					})
				}
			}
			e.preventDefault();
		});


		// 下拉
		$('body').on('click','.dropdown_menu',function(e){
			var $this = nowdropdown = $(this);
			if($this.hasClass('open')){
				$this.removeClass('open');
			}else{
				$this.addClass('open');
				$('.dropdown_menu').not(this).each(function(){
					if( $(this).hasClass('open') ) $(this).removeClass('open');
				})
			}
		});

		$('body').on('click','.hide_item',function(){
			var elem = $(this).attr('hideitem');
			if( elem ){
				var arr = elem.split(",");
				for (var i = 0; i < arr.length; i++) {
					$(arr[i]).hide();
				}
			}
		});
		$('body').on('click','.show_item',function(){
			var elem = $(this).attr('showitem');
			if( elem ){
				var arr = elem.split(",");
				for (var i = 0; i < arr.length; i++) {
					$(arr[i]).show();
				}
			}
		});
		
		// select
		$('.select_item').click(function(){
			var id = $(this).attr('id');
			var text = $(this).text();
			var parent = $(this).parents('.dropdown_menu');
			parent.find('input').val(id);
			parent.find('.jsBtLabel').text(text);
		})

	};


	function webAlert(str){
		if($('#webalert').length > 0){
			$('#webalert .alertcontent').text(str);
			alertShow();
		}else{
			var div = '<div id="webalert" style="position:fixed;z-index:99999;top:20px;left:50%;width:500px;height:35px;margin-left:-250px;background:red;">\
					<div class="alertcontent" style="font-size: 16px;color:#fff;text-align:center;line-height: 35px;">'+str+'</div></div>';
			$('body').append(div);
			alertShow();
		}
	};

	function alertShow() {
		$('#webalert').show('fast',function(){
			setTimeout(function(){$('#webalert').hide();},3000);
		})
	};

	//http请求
	 function Http(type,datatype,op,data,successCall,isloading,beforeCall,comCall){
		$.ajax({
			type: type,
			url: ajaxUrl(op),
			dataType: datatype,
			data : data,
			beforeSend:function(){
				if(beforeCall) beforeCall();
				if(isloading) loading();
			},
			success: function(data){
				if(successCall) successCall(data);
			},
			complete:function(){
				if(comCall) comCall();
				if(isloading) loaded();
			},				
			error: function(xhr, type){
				console.log(xhr);
			}
		});	
	};

	function ajaxUrl(op){
		return window.sysinfo.siteroot + 'web/index.php?c=site&a=entry&op='+op+'&do=ajax&m=zofui_chickenwish';
	};
	
	function loading(){
		var html = 
			'<div id="loading" class="loading">'+
			'<div class="load_mask"></div>'+
			'<div class="modal-loading">'+
			'	<div class="modal-loading-in">'+
			'		<img style="width:48px; height:48px;" src="../attachment/images/global/loading.gif"><p>处理中，勿关闭本页</p>'+
			'	</div>'+
			'</div>'+
			'</div>';
		$(document.body).append(html);
	};
	
	function loaded(){
		$('.loading').remove();
	};


	/***地区选择***/
	function areaSelect (element) {
		//if(typeof areaSelect.areaobj === 'object') return areaSelect.areaobj;
		this.elem = element;
		//areaSelect.areaobj = this;
		this.bindEvent();
	};

	areaSelect.prototype.init = function(){
		var self = this;
		if ($('.delivery_privince .js_dd_list').html() == ''){		
			var province = '';
			for(var i=0;i<citydata.length;i++){
				province += '<dd>'
								+'<a href="javascript:;" class="jsLevel father_menu jsLevel1" data-name="'+citydata[i].text+'">'
									+'<span class="item_name">'+citydata[i].text+'</span>'
								+'</a>'
							+'</dd>';
			}
			$('.delivery_privince .js_dd_list').html(province);
		}
		
		var provincevalue = self.elem.find('.area_province_input').val();

		if(provincevalue != ''){
			var cityvalue = self.elem.find('.area_city_input').val();
			var countyvalue = self.elem.find('.area_county_input').val();
			
			$('.jsLevel1').each(function(){	
				if($(this).attr('data-name') == provincevalue) $(this).addClass('selected');
			});
			
			self.appendCityStr(provincevalue,cityvalue); //城市

			countyvalue = countyvalue.replace(/,$/,'');
			countyarray = countyvalue.split(","); //数组
			
			self.appendCountyStr(provincevalue,cityvalue,countyarray); //区县
		}else{
			$('.delivery_city .js_dd_list').empty();
			$('.delivery_county .js_dd_list').empty();
		}

	};

	areaSelect.prototype.bindEvent = function(){
		var self = this;
		//点击一级展开二级
		$('body').off('click','.delivery_box .jsLevel1').on('click','.delivery_box .jsLevel1',function(){
			var province = $(this).attr('data-name');
			$('.delivery_privince .selected').removeClass('selected');
			$(this).addClass('selected');
			self.appendCityStr(province,'');
		});		
		//点击二级展开三级
		$('body').off('click','.delivery_box .jsLevel2').on('click','.delivery_box .jsLevel2',function(){
			var province = $(this).attr('data-province'),
				city = $(this).attr('data-name');
			$('.delivery_city .selected').removeClass('selected');
			$(this).addClass('selected');
			self.appendCountyStr(province,city,[]);
		});	

		//选择区县
		$('body').off('click','.delivery_box .jsLevel3').on('click','.delivery_box .jsLevel3',function(){

			if($(this).hasClass('disabled')) return false;
			/*var province = $(this).attr('data-province'),
				city = $(this).attr('data-city'),
				county = $(this).attr('data-name');*/

			if($(this).hasClass('selected')){
				$(this).removeClass('selected');
			}else{
				$(this).addClass('selected');
			}
		});	
	
		//确定选择
		$('#confirm_indelivery').off('click').on('click',function(){

			var province = '',
				city = '',
				county = '';
			$('.delivery_county .selected').each(function(){
				province = $(this).attr('data-province');
				city = $(this).attr('data-city');
				county += $(this).attr('data-name') + ',';
			});
		
			self.elem.find('.area_province_input').val(province).next().val(city).next().val(county);
			self.elem.find('.delivery_item_province').text(province).next().text(city).next().text(county);
			self.hideDeliveryTable();
		});
		
		
		//关闭操作框
		$('.delivery_close').off('click').on('click',function(){
			self.hideDeliveryTable();
		});			
		
	};
	
	//插入城市数据
	areaSelect.prototype.appendCityStr = function (province,city){
		
		for(var i=0;i<citydata.length;i++){
			if(citydata[i].text == province){
				var citystr = '';
				for(var j=0;j<citydata[i].children.length;j++){
					var selectstr = '';
					if(city == citydata[i].children[j].text) selectstr = 'selected';
					citystr += '<dd>'
									+'<a href="javascript:;" class="jsLevel father_menu jsLevel2 '+selectstr+'" data-province="'+province+'" data-name="'+citydata[i].children[j].text+'">'
										+'<span class="item_name">'+citydata[i].children[j].text+'</span>'
									+'</a>'
								+'</dd>';
					
				}
				$('.delivery_city .js_dd_list').html(citystr);
				$('.delivery_county .js_dd_list').empty();
				return false;
			}
		}		
		
	};
	
	areaSelect.prototype.appendCountyStr = function (province,city,countyarray){
		//已经选择了的地区，
		var selectedcountystr = '';
		$('.area_county_input').each(function(){
			selectedcountystr += $(this).val() + ',';
		});
		selectedcountystr = selectedcountystr.replace(/,$/,'');
		selectedcountystr = selectedcountystr.split(","); //数组
		
		for(var i=0;i<citydata.length;i++){
			if(citydata[i].text == province){
				for(var j=0;j<citydata[i].children.length;j++){			
					if(citydata[i].children[j].text == city){
						var county = '';
						for(var k=0;k<citydata[i].children[j].children.length;k++){
							if($.inArray(citydata[i].children[j].children[k], countyarray) >= 0  || $.inArray(citydata[i].children[j].children[k], selectedcountystr) < 0 ){
								var selectedstr = '';
								if( $.inArray(citydata[i].children[j].children[k], countyarray) >= 0 ) selectedstr = 'selected';
								
								county += '<dd>'
												+'<a href="javascript:;" class="jsLevel father_menu jsLevel3 '+selectedstr+'" data-province="'+province+'" data-city="'+city+'" data-name="'+citydata[i].children[j].children[k]+'">'
													+'<span class="item_name">'+citydata[i].children[j].children[k]+'</span>'
												+'</a>'
											+'</dd>';								
							}
						}
						
						$('.delivery_county .js_dd_list').html(county);
						return false;						
					}
				}
			}
		}
	};
	
	areaSelect.prototype.hideDeliveryTable = function (){
		$('.delivery_box .selected').removeClass('selected');
		$('.ui-draggable').hide();
		self = null;
	};
