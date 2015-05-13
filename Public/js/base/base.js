$.ajaxSetup({
    cache: false
});
(function ($) {
    //UI调整
    Do.ready('check', function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
    });
    //表格处理
    $.fn.duxTable = function (options) {
        var defaults = {
            selectAll: '#selectAll',
            selectSubmit: '#selectSubmit',
            selectAction: '#selectAction',
            deleteUrl: '',
            actionUrl: '',
            actionParameter: {}
        }
        var options = $.extend(defaults, options);
        this.each(function () {
            var table = this;
            var id = $(this).attr('id');
            //处理多选单选
            $(options.selectAll).click(function () {
                $(table).find("[name='id[]']").each(function () {
                    if ($(this).prop("checked")) {
						$(this).iCheck('uncheck');
						$(this).prop("checked",false);
                    } else {
						$(this).iCheck('check');
						$(this).prop("checked",true);
                    }
                })
            });
            //处理批量提交
            $(options.selectSubmit).click(function () {
                Do.ready('tips', 'dialog', function () {
                    //记录获取
                    var ids = new Array();
                    $(table).find("[name='id[]']").each(function () {
                        if ($(this).prop("checked")) {
                            ids.push($(this).val());
                        }
                    })
                    toastr.options = {
                        "positionClass": "toast-bottom-right"
                    }
                    if (ids.length == 0) {
                        toastr.warning('请先选择操作记录');
                        return false;
                    }
                    //操作项目
                    var dialog = layer.confirm('你确认要进行本次批量操作！', function () {
                        var parameter = $.extend({
                                ids: ids,
                                type: $(options.selectAction).val()
                            },
                            options.actionParameter());
                        $.post(options.actionUrl, parameter, function (json) {
                            if (json.status) {
                                toastr.success(json.info);
                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                toastr.warning(json.info);
                            }
                        }, 'json');
                        layer.close(dialog);
                    });

                });
            });
            //处理删除
            $(table).find('.del').click(function () {
                var obj = this;
                var div = $(obj).parent().parent();
				var url = $(obj).attr('url');
				if(url == '' || url == null || url == 'undefined'){
					url = options.deleteUrl;
				}
				operat(
					obj,
					url,
					function(){
						div.remove();
					},
					function(){
				});
            });
			//处理其他动作
			$(table).find('.action').click(function () {
                var obj = this;
                var div = $(obj).parent().parent();
				var url = $(obj).attr('url');
				operat(
					obj,
					url,
					function(){
						var success = $('obj').attr(success);
						if(success){
							eval(success);
						}
					},
					function(){
						var failure = $('obj').attr(failure);
						if(failure){
							eval(failure);
						}
				});
            });
			//处理动作
			function operat(obj,url,success,failure){
				Do.ready('tips', 'dialog', function () {
					var text = $(obj).text();
                    var dialog = layer.confirm('你确认执行' + text + '操作？', function () {
						var Dload = layer.load('操作执行中，请稍候...');
                        $.post(url, {
                                data: $(obj).attr('data')
                            },
                            function (json) {
								layer.close(Dload);
								layer.close(dialog);
                                if (json.status) {
                                    toastr.success(json.info);
									success();
                                } else {
                                    toastr.warning(json.info);
									failure();
                                }
                            }, 'json');
                    });
                });
			}
			//处理编辑
			$(table).find('.table_edit').blur(function () {
                var obj = this;
				var data = $(obj).attr('data');
				var url = $(obj).attr('url');
				if(url == '' || url == null || url == 'undefined'){
					url = options.editUrl;
				}
				Do.ready('tips', function () {
					$.post(url, {
							data: $(obj).attr('data'),
							name: $(obj).attr('name'),
							val: $(obj).val(),
						},
						function (json) {
						if (json.status) {
							toastr.success(json.info);
						} else {
							toastr.warning(json.info);
						}
					}, 'json');
				});
			});
        });
    };
    //表单处理
    $.fn.duxForm = function (options) {
        var defaults = {
            postFun: {},
            returnFun: {}
        }
        var options = $.extend(defaults, options);
        this.each(function () {
            //表单提交
            var form = this;
            Do.ready('form', function () {
                $(form).Validform({
                    ajaxPost: true,
                    postonce: true,
                    tiptype: function (msg, o, cssctl) {
                        if (!o.obj.is("form")) {
                            //设置提示信息
                            var objtip = o.obj.siblings(".help-block");
                            if (o.type == 2) {
                                //通过
                                var className = 'formitm-success';
                                $('#tips').html('');
                            }
                            if (o.type == 3) {
                                //未通过
                                var html = '<div class="u-alert u-alert-danger">您填写的信息未通过验证，请检查后重新提交！</div>';
                                $('#tips').html(html);
                                var className = 'formitm-danger';
                            }
                            //设置样式
                            o.obj.parents('.formitm').removeClass('formitm-success formitm-danger');
                            o.obj.parents('.formitm').addClass(className);
                            objtip.text(msg);
                        }
                    },
                    beforeSubmit: function (curform) {
                        //锁定按钮
                        $(form).find('#btn-group').find('button').attr('disabled', true);
                        $(form).find('#btn-submit').text($('#btn-submit').text() + '中...');
                        if ($.isFunction(options.postFun)) {
                            options.postFun();
                        }
                    },
                    callback: function (data) {
                        $(form).find('#btn-submit').text($('#btn-submit').text().replace('中...', ''));
                        if (data.status == 1) {
                            //成功返回
                            if ($.isFunction(options.returnFun)) {
                                options.returnFun(data);
                            } else {
                                if (data.url == null || data.url == '') {
                                    var html = '<div class="u-alert u-alert-success">' + data.info + '3秒后为您重新载入！</div>';
                                    $('#tips').html(html);
                                    window.setTimeout(function () {
                                        window.location.reload();
                                    }, 3000);
                                } else {
                                    var html = '<div class="u-alert u-alert-success">您可以 <a href="javascript:window.location.reload(); ">重新载入</a> 或 <a href="' + data.url + '">跳转</a>，无操作5秒后会系统会自动跳转。</div>';
                                    $('#tips').html(html);
                                    window.setTimeout(function () {
                                        window.location.href = options.returnUrl;
                                    }, 5000);
                                }
                            }
                        } else {
                            //失败返回
                            $(form).find('#btn-group').find('button').attr('disabled', false);
                            var html = '<div class="u-alert u-alert-danger">' + data.info + '</div>';
                            $('#tips').html(html);
                        }
                    }
                });
                //下拉赋值
                var assignObj = $(form).find('.dux-assign');
                assignObj.each(function () {
                    var assignTarget = $(this).attr('target');
                    $(this).change(function () {
                        $(assignTarget).val($(this).val());
                    });
                });
            });
        });
    };

    //编辑器调用
    $.fn.duxEditor = function (options) {
        var defaults = {
            uploadUrl: rootUrl + 'index.php?m=DuxCms&c=AdminUpload&a=editor',
            uploadParams: function () {},
            config: {}
        }
        var options = $.extend(defaults, options);
        var uploadParams = {
                        session_id : sessId
                    };
        this.each(function () {
            var id = this;
			var idName = $(this).attr('id') + '_editor';
            Do.ready('editor', function () {
                //编辑器
                var editorConfig =
                {
                    allowFileManager : false,
                    uploadJson : options.uploadUrl,
                    extraFileUploadParams : $.extend(uploadParams, options.uploadParams()),
                    afterBlur : function ()
                    {
                        this.sync();
                    },
                    width : '100%',
                    height: '450px'
                };
                editorConfig = $.extend(editorConfig, options.config);
				var str = idName + ' = KindEditor.create(id, editorConfig);';
				eval(str);
            });

        });
    };

    //时间插件
    $.fn.duxTime = function (options) {
        var defaults = {
            lang: 'ch'
        }
        var options = $.extend(defaults, options);
        this.each(function () {
            var id = this;
            Do.ready('time', function () {
                $(id).datetimepicker(options);
            });
        });
    };

    //上传调用
    $.fn.duxFileUpload = function (options) {
        var defaults = {
            uploadUrl: rootUrl + 'index.php?m=Admin&c=AdminUpload&a=upload',
            type: '',
            uploadParams: function () {},
            complete: function () {}
        }
        var options = $.extend(defaults, options);
        this.each(function () {
            var upButton = $(this);
            var urlVal = upButton.attr('data');
            urlVal = $('#' + urlVal);
            var buttonText = upButton.text();
            var preview = upButton.attr('preview');
            preview = $('#' + preview);
			/* 图片预览 */
            preview.click(function () {
                if (urlVal.val() == '') {
                    alert('没有发现已上传图片！');
                } else {
                    window.open(urlVal.val());
                }
                return;
            });
            /*创建上传*/
            Do.ready('webuploader', function () {
                var uploader = WebUploader.create({
                    swf: baseDir + 'webuploader/Uploader.swf',
                    server: options.uploadUrl,
                    pick: {
                        id : upButton,
                        multiple : false
                    },
                    resize: false,
                    auto: true,
                    accept : {
                        title: '指定格式文件',
                        extensions: options.type
                    },
                    formData: options.uploadParams()
                });
				//上传开始
				uploader.on( 'uploadStart', function(file) {
                    upButton.attr('disabled', true);
                    upButton.find('.webuploader-pick').text('等待');
                });
                //上传完毕
                uploader.on( 'uploadSuccess', function(file, data) {
					upButton.attr('disabled', false);
					upButton.find('.webuploader-pick').text('上传');
                    if (data.status){
                        urlVal.val(data.data.url);
                        options.complete(data.data);
                    }else{
                        alert(data.info);
                    }
                });
                uploader.on( 'uploadError', function( file ) {
                    alert('文件上传失败');
                });
            });
        });
    };
	//多图上传
	$.fn.duxMultiUpload = function(options) {
		var defaults = {
			uploadUrl: rootUrl + 'index.php?m=Admin&c=AdminUpload&a=upload',
			uploadParams: function() {},
			complete: function() {},
			type: ''
		}
		var options = $.extend(defaults, options);
		this.each(function() {
			var upButton = $(this);
			var dataName = upButton.attr('data');
			var div = $('#' + dataName);
			var data = div.attr('data');
            /*创建上传*/
            Do.ready('webuploader', 'sortable', function () {
                var uploader = WebUploader.create({
                    swf: baseDir + 'webuploader/Uploader.swf',
                    server: options.uploadUrl,
                    pick: upButton,
                    resize: false,
                    auto: true,
                    accept : {
                        title: '指定格式文件',
                        extensions: options.type
                    },
                    formData: options.uploadParams()
                });
				//上传开始
				uploader.on( 'uploadStart', function(file) {
                    upButton.attr('disabled', true);
                    upButton.find('.webuploader-pick').text('上传中...');
                });
                //上传完毕
                uploader.on( 'uploadSuccess', function(file, data) {
					upButton.attr('disabled', false);
					upButton.find('.webuploader-pick').text('上传');
                    if (data.status){
                        htmlList(data.data);
                        options.complete(data.data);
                    }else{
                        alert(data.info);
                    }
                });
                uploader.on( 'uploadError', function( file ) {
                    alert('文件上传失败');
                });
                uploader.on( 'uploadComplete', function( file ) {
                    //图片排序
                    div.sortable().on('sortupdate');
                });
                //处理图片预览
                function zoomPic() {
                    xOffset = 10;
                    yOffset = 30;
                    var maxWidth= 400;
                    div.on('mouseenter', '.pic img', function(e) {
                        $("body").append("<div id='imgZoom'><img class='pic' src='"+ $(this).attr('src') +"' /></div>");                                 
                        $("#imgZoom").css("top",(e.pageY - xOffset) + "px").css("left",(e.pageX + yOffset) + "px").fadeIn("fast");
                        var imgZoom = $("#imgZoom").find('.pic');
                        imgZoom.css("width",300).css("height",200);
                    });
                    div.on('mouseleave', '.pic img', function(e) {
                        $("#imgZoom").remove();
                    });
                    div.on('mousemove', '.pic img', function(e) {
                        $("#imgZoom").css("top",(e.pageY - xOffset) + "px").css("left",(e.pageX + yOffset) + "px");
                    });
                }
                zoomPic();
                //处理上传列表
                function htmlList(file) {
                    var html = '<li>\
                    <a class="close" href="javascript:;">×</a>\
                    <div class="img"><span class="pic"><img src="' + file.url + '" width="80" height="80" /></span></div>\
                    <div class="title">\
                    <input name="' + dataName + '[url][]" type="hidden" value="' + file.url + '" />\
                    <input name="' + dataName + '[title][]" type="text" value="' + file.title + '" />\
                    </div>\
                    </li>';
                    div.append(html);
                }
                //处理删除
                div.on('click', '.close',function() {
                    $(this).parent().remove();
                });
            });
		});
	};
	
	//表单页面处理
        $.fn.duxFormPage = function (options) {
            var defaults = {
                uploadUrl: rootUrl + 'index.php?m=Admin&c=AdminUpload&a=upload',
                editorUploadUrl: rootUrl + 'index.php?m=Admin&c=AdminUpload&a=editor',
                uploadComplete: function () {},
                uploadParams: function () {},
				uploadType : [],
                postFun: {},
                returnUrl: '',
                returnFun: {},
				form: true
            }
            var options = $.extend(defaults, options);
            this.each(function () {
                var form = this;
                form = $(form);
                //表单处理
				if(options.form){
					form.duxForm({
						postFun: options.postFun,
						returnUrl: options.returnUrl,
						returnFun: options.returnFun
					});
				}
                //文件上传
                if (form.find(".u-file-upload").length > 0) {
                    form.find('.u-file-upload').duxFileUpload({
						type: '*',
                        uploadUrl: options.uploadUrl,
                        complete: options.uploadComplete,
                        uploadParams: options.uploadParams
                    });
                }
				//图片上传
                if (form.find(".u-img-upload").length > 0) {
                    form.find('.u-img-upload').duxFileUpload({
						type: 'jpg,png,gif,bmp,jpeg',
                        uploadUrl: options.uploadUrl,
                        complete: options.uploadComplete,
                        uploadParams: options.uploadParams
                    });
                }
				//多图片上传
                if (form.find(".u-multi-upload").length > 0) {
                    form.find('.u-multi-upload').duxMultiUpload({
						type: 'jpg,png,gif,bmp,jpeg',
                        uploadUrl: options.uploadUrl,
                        complete: options.uploadComplete,
                        uploadParams: options.uploadParams
                    });
                }
                //编辑器
                if (form.find(".u-editor").length > 0) {
                    form.find('.u-editor').duxEditor({
                        uploadUrl: options.editorUploadUrl
                    });
                }
                //时间选择
                if (form.find(".u-time").length > 0) {
                    form.find('.u-time').duxTime();
                }
            });
        };
})(jQuery);