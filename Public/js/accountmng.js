$(document).ready(function(){
    //jqGrid setting 开始.//////////////////////////////////////////////////////////////////////////////////////////////
    //账号管理
    function setAccountmngCommJqGrid(domTableId,domPageId,cur_user_id){
        var search_url = '/index.php/Home/Account/ajaxAccountmngSearch';
        var save_url = '/index.php/Home/Account/ajaxAccountmngSave';
        var colNames = ['ID','账号名称','券商','账号ID','账号密码','初始金额','说明','状态'];
        var caption_str = "账号管理";
        var $accountcommgridobj = jQuery(domTableId).jqGrid({
            url: search_url,
            datatype: "json",
            mtype: 'POST',
            postData: {user_id:cur_user_id},
            colNames: colNames,
            colModel:[
                {name:'id',index:'id',
                    width:20,align:"right",
                    sortable:true,
                    editable:false,
                    editoptions:{readonly:true,size:20}
                },
                {name:'account_name',index:'account_name',
                    width:30,align:"right",
                    sortable:true,
                    editable:true, edittype:"text",editrules:{required:true},
                    editoptions:{size:"30"},
                    formoptions:{rowpos:2, label: "账号名称", elmprefix:"(*)"}
                },
                {name:'qs_name',index:'qs_name',
                    width:30, align:"center",
                    sortable:true,
                    stype:'select',//查询类型
                    editable:true,edittype:"select",
                    editoptions:{
                        value:{
                            '0':"全部",
                            'zx':"中信证券"
                        },
                        defaultValue:"0"
                    },
                    editrules:{required:true},
                    formoptions:{ rowpos:3,label: "券商名称",elmprefix:"(*)"}
                },
                {name:'account_id',index:'account_id',
                    width:30,align:"right",
                    sortable:true,
                    editable:true, edittype:"text",editrules:{required:true},
                    editoptions:{size:"30"},
                    formoptions:{rowpos:4, label: "账号ID", elmprefix:"(*)"}
                },
                {name:'account_pwd',index:'account_pwd',
                    width:30,align:"right",
                    sortable:true,
                    editable:true, edittype:"text",editrules:{required:true},
                    editoptions:{size:"30"},
                    formoptions:{rowpos:5, label: "账号密码", elmprefix:"(*)"}
                },
                {name:'account_csje',index:'account_csje',
                    width:30,align:"right",
                    sortable:true,
                    editable:true, edittype:"text",editrules:{required:true},
                    editoptions:{size:"30"},
                    formoptions:{rowpos:6, label: "初始金额", elmprefix:"(*)"}
                },
                {name:'sm',index:'sm',
                    width:70, align:"right",
                    sortable:true,
                    editable:true, edittype:"text", editrules:{required:false},
                    editoptions:{size:"70",maxlength:"70"},
                    formoptions:{ rowpos:7,label: "说明",elmprefix:"(*)"}
                },
                {name:'status',index:'status',
                    width:30, align:"center",
                    sortable:true,
                    stype:'select',//查询类型
                    editable:true,edittype:"select",
                    editoptions:{value:{
                        '0':"全部",
                        '1':"新增(待审核)",
                        '2':"正常(已审核)",
                        '3':"注销(已删除)"},defaultValue:"0"},
                    editrules:{required:true},
                    formoptions:{ rowpos:8,label: "状态",elmprefix:"(*)"}
                }
            ],
            hiddengrid: false,
            autowidth: true,
            rownumbers: true,
            rowNum:20,
            rowList:[20,30,50],
            pager: domPageId,
            viewrecords: true,
            sortorder: "desc",
            forceFit : true,
            editurl: save_url,
            caption:caption_str,
            onSelectRow: function(id){
                //
            },
            onCellSelect: function(rowid,iCol,cellcontent,e){
                //alert("cellselect" + cellcontent);
            },
            afterInsertRow:function(rowid,rowdata,rowelem){
                //alert(rowid);
            }
        });

        //导航栏配置和CRUD函数
        jQuery(domTableId).jqGrid('navGrid',domPageId,
            {view:true,search:false,edit:false,add:false,del:true,alerttext:"请选择需要操作的数据行!"}
        );
        //
        var addparameters = {
            //
            rowID : "new_row",
            initdata : {},
            position :"first",
            useDefValues : false,
            useFormatter : false,
            addRowParams : {
                extraparam:{user_id:cur_user_id},
                successfunc : function(xhr){
                    var result = eval('(' + xhr.responseText + ')');
                    if(true == result.state){
                        jQuery(domTableId).trigger("reloadGrid");
                        return true;
                    }else{
                        jAlert(result.msg);
                        jQuery(domTableId).trigger("reloadGrid");
                        return true;
                    }
                }
            }
        }
        //
        var editparameters = {
            "keys" : false,
            oneditfunc: null,
            "successfunc" : function(xhr){
                var result = eval('(' + xhr.responseText + ')');
                if(true == result.state){
                    return true;
                }else{
                    jAlert(result.msg);
                    return true;
                }
            },
            "url" : null,
            "extraparam" : {user_id:cur_user_id},
            "aftersavefunc" : null,
            "errorfunc": null,
            "afterrestorefunc" : null,
            "restoreAfterError" : true,
            "mtype" : "POST"
        }
        var parameters = {
            edit: true,
            editicon: "ui-icon-pencil",
            add: true,
            addicon:"ui-icon-plus",
            save: true,
            saveicon:"ui-icon-disk",
            cancel: true,
            cancelicon:"ui-icon-cancel",
            addParams : addparameters,
            editParams : editparameters
        }
        jQuery(domTableId).jqGrid('inlineNav',domPageId,parameters);

        //其他配置
        jQuery(domTableId).jqGrid('gridResize',{minWidth:1500,maxWidth:2000,minHeight:500, maxHeight:1000});
        jQuery(domTableId).jqGrid('sortableRows');

         jQuery(domTableId).jqGrid('filterToolbar');
         if($(domPageId + ":contains(Toggle)").length < 1){
             jQuery(domTableId).jqGrid('navButtonAdd',domPageId,{caption:"Toggle",title:"Toggle Search Toolbar", buttonicon :'ui-icon-pin-s',
                 onClickButton:function(){
                     $accountcommgridobj[0].toggleToolbar();
                 }
             });
         }
    }
    setAccountmngCommJqGrid("#accountmnglist","#paccountmnglist",$("#user_id").val());
    //------------------------------------------------------------------------------------------------------------------
    //jqGrid setting 结束///////////////////////////////////////////////////////////////////////////////////////////////
});