
var registerFun = (function(){
	var attrJson = {}; //需要填写表单json格式  如{"username": "姓名", "phone": "手机"}
				attrJson["username"] = "姓名";
			attrJson["phone"] = "手机";
	function validate(){
		var success = true;
		//验证表单必填
		$('.input-group .required').each(function(){
			var val = $.trim($(this).val()); //去掉前后空格
			var name = $(this).attr("name");
			if(!val){
				alert("请输入"+attrJson[name]);
				success = false;
				return false;
			}
		});
		if(!success)return false;
		//验证用户名正确性
		$('.input-group .ck_username').each(function(){
			var val = $.trim($(this).val());
			var name = $(this).attr("name");
			if(!isUserName(val)){
			    if(typeof attrJson[name] == 'undefined'){
			       alert("请输入正确的姓名");
			    }else{
				   alert("请输入正确的"+attrJson[name]);
			    }
				success = false;
				return false;
			}
		});
		if(!success)return false;
		//验证手机正确性
		$('.input-group .phone').each(function(){
			var val = $.trim($(this).val());
			var name = $(this).attr("name");
			if(!isMobil(val)){
				if(typeof attrJson[name] == 'undefined'){
					alert("请输入正确的手机号");
				}else{
					alert("请输入正确的"+attrJson[name]);
				}
				success = false;
				return false;
			}
		});
		return success;
	}
	function init(btnObj, url, callback){
		var isBegin = false;
		$(btnObj).bind('click', function(){
			if(isBegin) return false;
			if(!validate()) return; //验证表单
	 	    var submitData = {oid:'oFloWwVT6H5xJfGZ7vHzi9xAwMgY'};
	 	    var extInfos = [];
	 	   var form_feibo = {'data':{}};
	 		$('.input-group .form-control').each(function(i){
	 			var name = $(this).attr("name");
	 			var value = $.trim($(this).val());
	 			if(name == 'username'){
	 				submitData['username'] = value; //用户名
	 			} else if(name == 'phone'){
	 				submitData['phone'] = value; //手机号
	 			} else if(name == 'company'){
	 				submitData['company'] = value; //公司
	 			} else if(name == 'sncode'){
	 				submitData['sncode'] = value; //sn验证码
	 			} else if(name == 'smscode'){
	 				submitData['smscode'] = value; //短信验证码
	 			} else{
	 			   var arr ={
	 	 	           "title" : attrJson[name],
	 	 	           "name" : name,
	 	 	           "value" : value
	 	 	       }
	 			   if($(this).attr("type")!="checkbox" || $(this).prop("checked")){ //单、复选框需选中才赋值
		 				extInfos.push(arr);
	 			   }
	 			}
	 			//兔展表单数据
	 			var temp = {};
	 			temp[attrJson[name]] = value;
		 		form_feibo.data['form_'+(i+1)] = temp;
			});
	 	    if(extInfos.length > 0){
	 	    	submitData['extInfo'] = JSON.stringify(extInfos);
	 	    }
	 	    isBegin = true;
			$.ajax({
				type : "POST",
				url : url,
				data : submitData,
				dataType : "json",
				success : function(data){
					callback(data);
		 	        isBegin = false;
				},
				timeout : 15000,
				error : function(xhr, type){
					//alert("网络异常，请刷新重试。");
					isBegin = false;
				}
			});
		});
		$("#username").val('');
		$("#phone").val('');
		$("#company").val('');
		$("#sncode").val('');
		if( $('#origWxid').val() != '' ){
			$("#username").val('');
			$("#phone").val('');
		}
		//额外资料
	}
	function init2(btnObj, url, callback,validateResult){
		var isBegin = false;
		$(btnObj).bind('click', function(){
			if(isBegin) return false;
			if(!validateResult()) return;	// 外部的验证
			if(!validate()) return; //验证表单
	 	    var submitData = {oid:'oFloWwVT6H5xJfGZ7vHzi9xAwMgY'};
	 	    var extInfos = [];
	 	   var form_feibo = {'data':{}};
	 		$('.input-group .form-control').each(function(i){
	 			var name = $(this).attr("name");
	 			var value = $.trim($(this).val());
	 			if(name == 'username'){
	 				submitData['username'] = value; //用户名
	 			} else if(name == 'phone'){
	 				submitData['phone'] = value; //手机号
	 			} else if(name == 'company'){
	 				submitData['company'] = value; //公司
	 			} else if(name == 'sncode'){
	 				submitData['sncode'] = value; //sn验证码
	 			} else if(name == 'smscode'){
	 				submitData['smscode'] = value; //短信验证码
	 			} else{
	 			   var arr ={
	 	 	           "title" : attrJson[name],
	 	 	           "name" : name,
	 	 	           "value" : value
	 	 	       }
	 			   if($(this).attr("type")!="checkbox" || $(this).prop("checked")){ //单、复选框需选中才赋值
		 				extInfos.push(arr);
	 			   }
	 			}
	 			//兔展表单数据
	 			var temp = {};
	 			temp[attrJson[name]] = value;
		 		form_feibo.data['form_'+(i+1)] = temp;
			});
	 	    if(extInfos.length > 0){
	 	    	submitData['extInfo'] = JSON.stringify(extInfos);
	 	    }
	 	    isBegin = true;
			$.ajax({
				type : "POST",
				url : url,
				data : submitData,
				dataType : "json",
				success : function(data){
					callback(data);
		 	        isBegin = false;
				},
				timeout : 15000,
				error : function(xhr, type){
					//alert("网络异常，请刷新重试。");
					isBegin = false;
				}
			});
		});
		$("#username").val('');
		$("#phone").val('');
		$("#company").val('');
		$("#sncode").val('');
		if( $('#origWxid').val() != '' ){
			$("#username").val('');
			$("#phone").val('');
		}
		//额外资料
	}
	return{init:init,init2:init2}
})();
//万能表单-发送短信验证码
$(function(){
	var isBegin = false;
	var sms_content = '【精准分众】参与珠江壹城给您拜年啦的验证码[smscode]，10分钟内有效。';
	var sms_flag = 'RPjjYOk7QMYTDYUD';
    $("#getSmscode").click(function() {
  	    if(isBegin) return;
        var phone = $('#phone').val();
        if (!isMobil(phone)) {
            alert("请输入有效手机号");
            return false;
        }
        isBegin = true;
        $.ajax({
            type : 'POST',
            url : '/send/smscode',
            data : {'phone':phone,'content':sms_content,'flag':sms_flag},
            dataType : 'json',
            success : function(data) {
                if(data.result_code == 0){
                	GetNumber(60);
                    alert("验证码已发送您的手机，请查收！");
                }else{
                    alert(data.result_msg);
                }
                isBegin = false;
            }
        });
    });
    function GetNumber(count) {
        $("#getSmscode").prop("disabled", "disabled");
        $("#getSmscode").val(count + "秒后重新获取");
        var GetNumberInterval = setInterval(function() {
            count--;
            if (count == 0) {
                $("#getSmscode").prop("disabled", "");
                $("#getSmscode").val("获取验证码");
                clearInterval(GetNumberInterval);
            } else {
                $("#getSmscode").prop("disabled", "disabled");
                $("#getSmscode").val(count + "秒后重新获取");
            }
        }, 1000);
    }
});


		registerFun.init(".btn-submit","/RPjjYOk7QMYTDYUD/save",function(data){
		    if(data.rescode == 0) {
		    	params.isRegister = "1";
		    	$('#registerPanel').remove();$('#panelBg').hide();
		    }else if(data.rescode == 1){
		    	alert(data.resmsg);
		    }else{
		    	alert("网络异常，请刷新重试。");
		    }
		});

//<!-- 引导关注控件 -->
//<!-- 关注提醒框 -->

		$(function($) {
			//判断是否显示关注
		    $(".popup_off,.popup_off_1").click(function() {
		        $("#guideSubscribe").fadeOut();
		    });
		});