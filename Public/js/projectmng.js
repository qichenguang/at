$(document).ready(function(){
    function getAllZtreeCheckToUL(){
        var treeObj = $.fn.zTree.getZTreeObj("privilegeTree");
        var nodes = treeObj.getCheckedNodes(true);
        $("#privilegeCheckedShow li").remove();
        for(var i=0;i<nodes.length;i++){
            if(nodes[i].id > 100){
                $("#privilegeCheckedShow").append("<li>[ " + nodes[i].getParentNode().name + " : " + nodes[i].name + " ]&nbsp;&nbsp;&nbsp;&nbsp;</li>");
            }
        }
    }
    //zTree  设置开始 //////////////////////////////////////////////////////////////////////////////////////////////////
    var ztree_setting = {
        view: {
            //禁止多点选中
            selectedMulti: false
        },
        check: {
            enable: true,
            chkDisabledInherit: true
        },
        data: {
            simpleData: {
                enable:true,
                idKey: "id",
                pIdKey: "pId",
                rootPId: ""
            }
        },
        callback: {
            onCheck: ztree_onCheck
        }
    };
    function ztree_onCheck(){
        var treeObj = $.fn.zTree.getZTreeObj("privilegeTree");
        var nodes = treeObj.getCheckedNodes(true);
        var v = "";
        for(var i=0;i<nodes.length;i++){
            if(nodes[i].id > 100){
                v += nodes[i].pro_id + "," + nodes[i].department + "," + nodes[i].user_id + "|";
            }
        }
        var nodes2 = treeObj.getNodes();
        var result = "";
        for (var i = 0; i < nodes2.length; i++) {
            result = getAllChildrenNodes(nodes2[i],result);
        }
        $("#pro2user_all_str").attr("value",v + result);
        //
        getAllZtreeCheckToUL();
    }
    function getAllChildrenNodes(treeNode,result){
        if (treeNode.isParent) {
            var childrenNodes = treeNode.children;
            if (childrenNodes) {
                for (var i = 0; i < childrenNodes.length; i++) {
                    if(childrenNodes[i].id > 100 && (childrenNodes[i].department == "gl"))
                    result += childrenNodes[i].pro_id + "," + childrenNodes[i].department + "," + childrenNodes[i].user_id + "|";
                    result = getAllChildrenNodes(childrenNodes[i], result);
                }
            }
        }
        return result;
    }
    function initProIdZtree(pro_id){
        $.ajax({
            type: "POST",
            dataType: "json",
            url: '/index.php/Home/Project/ajaxPrivilegeGet',
            data: {pro_id:pro_id},
            success: function(data) {
                var treeObj = $.fn.zTree.init($("#privilegeTree"), ztree_setting, data);
                var nodes = treeObj.getCheckedNodes();
                for (var i = 0; i < nodes.length; i++) {
                    treeObj.checkNode(nodes[i], true, true);
                }
                var nodes2 = treeObj.getNodes();
                for (var i = 0; i < nodes2.length; i++) {
                    //console.log(nodes2[i]);
                    if(nodes2[i].name == "管理部" ){
                        treeObj.checkNode(nodes2[i], true, true);
                        nodes2[i].chkDisabled = true;
                        var nodes3 = nodes2[i].children;
                        for(var j=0;j<nodes3.length;j++){
                            //console.log(nodes3[j]);
                            treeObj.checkNode(nodes3[j], true, true);
                            nodes3[j].chkDisabled = true;
                        }
                    }
                }
                ztree_onCheck();
                //
                $("#privilegeDiv #pro2user_pro_id").val(pro_id);
                $("#privilegeDiv").show();
                //
                getAllZtreeCheckToUL();
            }
        });
    }
    $("#privilegeDiv").hide();
    ////zTree  设置结束/////////////////////////////////////////////////////////////////////////////////////////////////
    //Form 设置 开始  //////////////////////////////////////////////////////////////////////////////////////////////////
    function beforePrivilegeRequest(formData, jqForm, options){
        //生成权限字符串，并设置
        ztree_onCheck();
        return true;
    }
    //提交后
    function afterPrivilegeResponse(responseText, statusText , xhr , $form){
        //清空权限字符串，并设置
        $("#privilegeDiv #pro2user_pro_id").val("");
        $("#privilegeDiv #pro2user_all_str").val("");
        var result = eval('(' + xhr.responseText + ')');
        jAlert(result.msg);
        $("#privilegeDiv").hide();
    }
    var privilegeOptions = {
        target:        '#privilegeResult',   // 用服务器返回的数据 更新 id为 result 的内容.
        beforeSubmit:  beforePrivilegeRequest,  // 提交前
        success:       afterPrivilegeResponse,  // 提交后
        //另外的一些属性:
        //url:       url         // 默认是form的action，如果写的话，会覆盖from的action.
        //type:      type        // 默认是form的method，如果写的话，会覆盖from的method.('get' or 'post').
        //dataType:  null        // 'xml', 'script', or 'json' (接受服务端返回的类型.)
        //clearForm: true        // 成功提交后，清除所有的表单元素的值.
        resetForm: true,        // 成功提交后，重置所有的表单元素的值.
        //由于某种原因,提交陷入无限等待之中,timeout参数就是用来限制请求的时间,
        //当请求大于3秒后，跳出请求.
        timeout:   3000
    };
    //'ajaxForm' 方式的表单 .
    $('#privilegeAddForm').ajaxForm(privilegeOptions);
    //Form 设置 结束  //////////////////////////////////////////////////////////////////////////////////////////////////
    //jqGrid setting 开始.//////////////////////////////////////////////////////////////////////////////////////////////
    var projectmngListGrid = jQuery("#projectmnglist").jqGrid({
        url:'/index.php/Home/Project/ajaxProjectmngSearch',
        datatype: "json",
        mtype: 'POST',
        colNames:['ID','项目ID', '项目名称','项目状态'],
        colModel:[
            {name:'id',index:'id',
                width:20,align:"right",
                sortable:true,
                editable:false,
                editoptions:{readonly:true,size:20}
            },
            {name:'pro_id',index:'pro_id',
                width:30,align:"right",
                sortable:true,
                editable:true, edittype:"text",editrules:{required:true},
                editoptions:{size:"30"},
                formoptions:{rowpos:2, label: "项目ID", elmprefix:"(*)"}
            },
            {name:'pro_name',index:'pro_name',
                width:30,align:"right",
                sortable:true,
                editable:true, edittype:"text",editrules:{required:true},
                editoptions:{size:"30"},
                formoptions:{rowpos:3, label: "项目名称", elmprefix:"(*)"}
            },
            {name:'status',index:'status',
                width:20, align:"center",
                sortable:true,
                stype:'select',//查询类型
                editable:true,edittype:"select",
                editoptions:{
                    value:{
                        '0':"全部",
                        '1':"新增(待审核)",
                        '2':"正常(已审核)",
                        '3':"注销(已删除)"
                    },
                    defaultValue:"0"
                },
                editrules:{required:true},
                formoptions:{ rowpos:4,label: "项目状态",elmprefix:"(*)"}
            }
        ],
        hiddengrid: false,
        autowidth: true,
        rownumbers: true,
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pprojectmnglist',
        viewrecords: true,
        sortorder: "desc",
        forceFit : true,
        editurl: '/index.php/Home/Project/ajaxProjectmngSave',
        caption:"项目管理",
        onSelectRow: function(id){
            $("#privilegeDiv").hide();
        }
    });
    //导航栏配置和CRUD函数
    jQuery("#projectmnglist").jqGrid('navGrid','#pprojectmnglist',
        {view:false,search:false}, //导航栏 按钮 是否显示 options
        {   // edit options begin
            mtype:'POST',
            height:290,
            modal: true, //模态显示
            drag: true,  //窗体是否可以拖动
            topinfo:"(*) 为必填字段",
            bottominfo:"带 (*) 表示是必填字段",
            processData: "数据保存中...",
            jqModal:true,
            reloadAfterSubmit:true,
            closeOnEscape:true,
            closeAfterEdit:true,
            onclickSubmit: function(params, postdata) {
                //fires after the submit button is clicked and the postdata is constructed
                postdata.id = jQuery("#projectmnglist").jqGrid('getGridParam','selrow');
                return postdata;
            },
            afterSubmit: function(xhr, postdata) {
                //fires after response has been received from server
                //动态页返回json格式的字符串，如{success:true/false}之类的，为false添加err属性什么的，成功则返回new_id
                //[success, message, new_id];
                var result = eval('(' + xhr.responseText + ')');
                return [result.state,result.msg];
            },
            afterComplete: function(response, postdata, formid) {
                //This event fires immediately after all actions and events are completed and the row is inserted or updated in the grid.
                //jQuery("#userlist").trigger('reloadGrid');
            }

        }, // edit options end
        {  // add options begin
            mtype:'POST',
            height:290,
            modal: true, //模态显示
            drag: true,  //窗体是否可以拖动
            topinfo:"(*) 为必填字段",
            bottominfo:"带 (*) 表示是必填字段",
            processData: "数据保存中...",
            jqModal:true,
            reloadAfterSubmit:true,
            closeOnEscape:true,
            clearAfterAdd:true,
            closeAfterAdd:true,
            onclickSubmit: function(params, postdata) {
                //fires after the submit button is clicked and the postdata is constructed
                return postdata;
            },
            afterSubmit: function(xhr, postdata) {
                //fires after response has been received from server
                //动态页返回json格式的字符串，如{success:true/false}之类的，为false添加err属性什么的，成功则返回new_id
                var result = eval('(' + xhr.responseText + ')');
                return [result.state,result.msg,result.id];
            },
            afterComplete: function(response, postdata, formid) {
                //This event fires immediately after all actions and events are completed and the row is inserted or updated in the grid.
                //jQuery("#userlist").trigger('reloadGrid');
            }

        }, // add options end
        {mtype:'POST',reloadAfterSubmit:false,jqModal:false, closeOnEscape:true}, // del options
        {closeOnEscape:true}, // search options
        {height:250,jqModal:false,closeOnEscape:true} // view options
    );
    //其他配置
    jQuery("#projectmnglist").jqGrid('gridResize',{minWidth:350,maxWidth:1000,minHeight:80, maxHeight:350});
    jQuery("#projectmnglist").jqGrid('sortableRows');
    jQuery("#projectmnglist").jqGrid('filterToolbar');
    jQuery("#projectmnglist").jqGrid('navButtonAdd',"#pprojectmnglist",{caption:"Toggle",title:"Toggle Search Toolbar", buttonicon :'ui-icon-pin-s',
        onClickButton:function(){
            projectmngListGrid[0].toggleToolbar();
        }
    });
    jQuery("#projectmnglist").jqGrid('navButtonAdd',"#pprojectmnglist",{caption:"授权",title:"项目授权",
        onClickButton: function(){
            var rowid = jQuery("#projectmnglist").jqGrid('getGridParam','selrow');
            if(rowid){
                var rowData = $("#projectmnglist").jqGrid('getRowData',rowid);
                initProIdZtree(rowData.pro_id);
            }else{
                jAlert("请选择一行数据");
            }
        },
        position:"last"
    });
    //jqGrid setting 结束///////////////////////////////////////////////////////////////////////////////////////////////


});