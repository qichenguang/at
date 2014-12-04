$(document).ready(function() {
    //
    var userListGrid = jQuery("#userlist").jqGrid({
        url:'/index.php/Home/User/ajaxUserSearch',
        datatype: "json",
        mtype: 'POST',
        colNames:['ID','用户名称','邮件地址','部门','用户状态'],
        colModel:[
            {name:'id',index:'id',
                width:100,align:"right",
                sortable:true,
                editable:false,
                editoptions:{readonly:true,size:100}
            },
            {name:'user_name',index:'user_name',
                width:150,align:"right",
                sortable:true,
                editable:true, edittype:"text",editrules:{required:true},
                editoptions:{size:"20"},
                formoptions:{rowpos:1, label: "用户姓名", elmprefix:"(*)"}
            },
            {name:'email',index:'email',
                width:150, align:"right",
                sortable:true,
                editable:true, edittype:"text", editrules:{email:true},
                editoptions:{size:"20",maxlength:"150"},
                formoptions:{ rowpos:2,label: "邮箱地址",elmprefix:"(*)"}
            },
            {name:'department',index:'department',
                width:150, align:"center",
                sortable:true,
                stype:'select',//查询类型
                editable:true,edittype:"select",
                editoptions:{
                    value:{
                        'all':"全部",
                        'kf' : "开发管理部",
                        'cl' : "策略管理部",
                        'zj' : "资金管理部",
                        'gl':"管理部"
                    },
                    defaultValue:"all"
                },
                editrules:{required:true},
                formoptions:{ rowpos:3,label: "所属部门",elmprefix:"(*)"}
            },
            {name:'status',index:'status',
                width:150, align:"center",
                sortable:true,
                stype:'select',//查询类型
                editable:true,edittype:"select",
                editoptions:{value:{
                    '0':"全部",
                    '1':"新增(待审核)",
                    '2':"正常(已审核)",
                    '3':"注销(已删除)"},defaultValue:"0"},
                editrules:{required:true},
                formoptions:{ rowpos:4,label: "用户状态",elmprefix:"(*)"}
            }
        ],
        hiddengrid: false,
        rownumbers: true,
        rowNum:10,
        rowList:[10,20,30],
        pager: '#puserlist',
        viewrecords: true,
        sortorder: "desc",
        forceFit : true,
        editurl: '/index.php/Home/User/ajaxUserSave',
        caption:"用户管理"
    });

    //导航栏配置和CRUD函数
    jQuery("#userlist").jqGrid('navGrid','#puserlist',
        {view:false,search:false}, //导航栏 按钮 是否显示 options
        {   // edit options begin
            mtype:'POST',
            height:290,
            modal: true, //模态显示
            drag: true,  //窗体是否可以拖动
            //addCaption: "新建公司简介",
            //bSubmit: "保存",
            //bCancel: "取消",
            topinfo:"(*) 为必填字段",
            bottominfo:"带 (*) 表示是必填字段",
            processData: "数据保存中...",
            jqModal:true,
            reloadAfterSubmit:true,
            closeOnEscape:true,
            closeAfterEdit:true,
            onclickSubmit: function(params, postdata) {
                //fires after the submit button is clicked and the postdata is constructed
                postdata.id = jQuery("#userlist").jqGrid('getGridParam','selrow');
                return postdata;
            },
            afterSubmit: function(xhr, postdata) {
                //fires after response has been received from server
                //[success, message, new_id];
                //动态页返回json格式的字符串，如{success:true/false}之类的，为false添加err属性什么的，成功则返回new_id
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
            //addCaption: "新建公司简介",
            //bSubmit: "保存",
            //bCancel: "取消",
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
    jQuery("#userlist").jqGrid('gridResize',{minWidth:350,maxWidth:1000,minHeight:80, maxHeight:350});
    jQuery("#userlist").jqGrid('sortableRows');
    jQuery("#userlist").jqGrid('filterToolbar');
    jQuery("#userlist").jqGrid('navButtonAdd',"#puserlist",{caption:"Toggle",title:"Toggle Search Toolbar", buttonicon :'ui-icon-pin-s',
        onClickButton:function(){
            userListGrid[0].toggleToolbar();
        }
    });
});