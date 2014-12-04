$(document).ready(function() {

    //
    function hide_all_modules(){
        $("#module_zjcgl").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        $("#module_curInventory").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        $("#module_jobbuy").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        $("#module_jobsell").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        $("#module_xmscfx").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        $("#module_khbyd").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        $("#module_xmrlzygl").accordion({ collapsible: true, autoHeight: true ,clearStyle: true,fillSpace:true,heightStyle:"content" });
        $("#module_xmsjgl").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        $("#module_xmcbgl").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        $("#module_xmzjlgl").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        $("#module_gysgl").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        $("#module_ygjxgl").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        $("#module_gczcgl").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        $("#module_dxmgl").accordion({ collapsible: true, autoHeight: true,clearStyle: true,fillSpace:true,heightStyle:"content"  });
        //1.first hide all.
        $(".module").hide();
    };
    //
    function show_dep_modules(){
        //2.second show can use model.
        var dep_mod_arr = $("#dep_module").val().split(",");
        $.each(dep_mod_arr, function(key, val) {
            $('#' + val).show();
            // firebug console
            //console.log('index in arr:' + key + ", corresponding value:" + val);
            // 如果想退出循环
            // return false;
        });
    }
    //1.
    hide_all_modules();
    //2.
    show_dep_modules();
    //3.初始化控件
    $('.module [jb_field=multiple]').multiselect({
        multiple: true,
        header: true,
        noneSelectedText: "请选择至少一项",
        selectedList: 8
    });
    $('.module [jb_field=single]').multiselect({
        multiple: false,
        header: "请选择一项",
        noneSelectedText: "请选择一项",
        selectedList: 8
    });
    $(".module [jb_time_field]" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat:"yy-mm-dd",
        showButtonPanel: true
    });
    //3.1 初始化控件 选择用户
    $(".module select[jb_dep]").each(function(){
        var dep = $(this).attr("jb_dep");
        //alert(dep);
        var all_dep_str = $("#all_dep_username").val();
        var dep_obj = $.parseJSON(all_dep_str);
        if(dep_obj[dep] === undefined || dep_obj[dep] == null){
            //error dep
        }else{
            var $curSelectObj = $(this);
            $(this).empty();
            var head="请选择";
            var ui = "<option value='' selected='selected' >" + head + "</option>";
            $(this).append(ui);
            $.each(dep_obj[dep],function(i,item){
                var ui="<option value='"+ item['id'] +"'>" + item['user_name'] + "</option>"
                $curSelectObj.append(ui);
            })
        }
    });
    //3.2   初始化控件 textarea
    //4.froozen_all_modules
    //------------------------------------------------------------------------------------------------------------------
    $(".module textarea").each(function(index,elem){
        $(this).attr({placeholder:"请输入:",maxlength:"500",cols:"50", rows:"1"});
        $(this).css({overflow:"visible"});
        $(this).on("input propertychange",function(){
            this.style.posHeight = this.scrollHeight;
        });
        this.style.height = this.scrollHeight + 1 + 'px';
    });
    $(".module input").each(function(index,elem){
        $(this).attr({placeholder:"请输入:",maxlength:"50"});
        var id = $(this).attr("id");
        //alert(id.substring(0,3));
        var dep = id.substring(0,3);
        if(dep == "sc_" || dep == "ys_" || dep == "sj_" || dep == "jd_" || dep == "ht_" || dep == "sh_" || dep == "hr_"
            || dep == "cg_" || dep == "gc_" || dep == "zj_"){
            $(this).addClass("recurly");
        }

    });
    $(".module table[jb_table]").each(function(index,elem){
        var cls = $(this).attr("jb_table");
        //alert(cls); bordered table1
        $(this).addClass("bordered");
    });
    //------------------------------------------------------------------------------------------------------------------
    //5.lock unlock
    //6.
    $('.module [jb_field=jb_select_save]').darkTooltip({
        animation:'flipIn',
        gravity:'west',
        //trigger:'click',
        confirm:true,
        theme:'light',
        content:'存盘?',
        onYes:function(ctl){
            //alert(ctl.attr("id"));
            var depcode = $("#user_department").val();
            var id = ctl.attr("id");
            if(id.substring(0,3) == (depcode + "_")){
                //alert(ctl.val());
                var ot_id = id + "_ot";
                var $ot_ctl = $("#" + ot_id);
                var ot_value = $ot_ctl.val();
                var requestData = {pro_id: $("#pro_id").val(),fieldname: ot_id,fieldvalue:ot_value};
                //console.log(requestData);
                $.get('/index.php/Home/AllInOne/ajaxSingleFieldSave', requestData, function(data) {
                    //alert("save ok");
                    //alert(data);
                    //alert(data.state + "," + data.msg);
                    if(false == data.state){
                        //jAlert(data.msg);
                        $ot_ctl.addClass("ui-state-error");
                    }else{
                        $ot_ctl.removeClass("ui-state-error");
                        $ot_ctl.css("background","#bbffaa");
                    }
                    //console.log(data);
                }).fail(function(jqXHR) {
                        jAlert("fail:" + jqXHR.status + jqXHR.responseText);
                    });
                //
                var base_id = id + "_base";
                var $base_ctl = $("#" + base_id);
                var base_value = $base_ctl.val();
                requestData = {pro_id: $("#pro_id").val(),fieldname: base_id,fieldvalue:base_value};
                $.get('/index.php/Home/AllInOne/ajaxSingleFieldSave', requestData, function(data) {
                    //alert("save ok");
                    //alert(data);
                    //alert(data.state + "," + data.msg);
                    if(false == data.state){
                        //jAlert(data.msg);
                        $base_ctl.addClass("ui-state-error");
                    }else{
                        $base_ctl.removeClass("ui-state-error");
                        $base_ctl.css("background","#bbffaa");
                    }
                    //console.log(data);
                }).fail(function(jqXHR) {
                        jAlert("fail:" + jqXHR.status + jqXHR.responseText);
                    });
            }
        }
    });

    $('.module [jb_field=jb_single_save]').darkTooltip({
        animation:'flipIn',
        gravity:'west',
        confirm:true,
        theme:'light',
        content:'存盘?',
        onYes:function(ctl){
            //alert(ctl.attr("id"));
            var depcode = $("#user_department").val();
            var id = ctl.attr("id");
            var zb_flag = $("#cur_project_sc_zb_flag").val();
            //落标后可以存其他部门字段
            if(id.substring(0,3) == (depcode + "_") || "A" == zb_flag){
                //alert(ctl.val());
                var requestData = {pro_id: $("#pro_id").val(),fieldname: ctl.attr("id"),fieldvalue:ctl.val()};
                //console.log(requestData);
                $.get('/index.php/Home/AllInOne/ajaxSingleFieldSave', requestData, function(data) {
                    //alert("save ok");
                    //alert(data);
                    //alert(data.state + "," + data.msg);
                    if(false == data.state){
                        jAlert(data.msg);
                        ctl.addClass("ui-state-error");
                    }else{
                        ctl.removeClass("ui-state-error");
                        ctl.css("background","#bbffaa");
                        if("sc_zb_flag" == id){
                            jAlert("请按 F5 刷新页面");
                        }
                    }
                    //console.log(data);
                }).fail(function(jqXHR) {
                        jAlert("fail:" + jqXHR.status + jqXHR.responseText);
                    });
            }
        }
    });


});
