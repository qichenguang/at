$(document).ready(function(){
    //jqGrid setting 开始.//////////////////////////////////////////////////////////////////////////////////////////////
    //-----------------------------------------------------------------------------------------------------
    function setAlertJqGrid(domTableId,domPageId,cur_dep_sx,cur_user_id){
        var caption_str = "报警";
        var search_url = '/index.php/Home/Alert/ajaxAlertSearch';
        var save_url = '/index.php/Home/Alert/ajaxAlertSave';
        var colNames = ['ID','项目ID','项目名称','信息','部门','日期'];

        var $aletcommgridobj = jQuery(domTableId).jqGrid({
            url: search_url,
            datatype: "json",
            mtype: 'POST',
            postData: {depsx:cur_dep_sx},
            colNames: colNames,
            colModel:[
                {name:'id',index:'id',
                    width:10,align:"right",
                    sortable:true,
                    editable:false,
                    readonly:true,
                    editoptions:{readonly:true,size:20},
                    formoptions:{rowpos:1, label: "id", elmprefix:"(*)"}
                },
                {
                    name:"pro_id",
                    index:"pro_id",
                    width:20,
                    align:"right",
                    sortable:true,
                    editable:true, edittype:"text",
                    editrules:{required:true},
                    editoptions:{
                        size:"20"
                    },
                    formoptions:{rowpos:2, label: "项目ID", elmprefix:"(*)"}
                },
                {
                    name:"pro_name",
                    index:"pro_name",
                    width:30,
                    align:"center",
                    sortable:true,
                    editable:true, edittype:"text",
                    editrules:{required:true},
                    editoptions:{
                        size:"30"
                    },
                    formoptions:{rowpos:3, label: "项目名称", elmprefix:"(*)"}
                },
                {
                    name:"msg",
                    index:"msg",
                    width:150,
                    align:"center",
                    sortable:true,
                    editable:true, edittype:"text",
                    editrules:{required:true},
                    editoptions:{
                        size:"150"
                    },
                    formoptions:{rowpos:4, label: "信息", elmprefix:"(*)"}
                },
                {
                    name:"depsx",
                    index:"depsx",
                    width:20,
                    align:"right",
                    sortable:true,
                    editable:true, edittype:"text",
                    editrules:{required:true},
                    editoptions:{
                        size:"20"
                    },
                    formoptions:{rowpos:5, label: "部门", elmprefix:"(*)"}
                },
                {
                    name:"alert_time",
                    index:"alert_time",
                    width:20,
                    align:"right",
                    sortable:true,
                    editable:true, edittype:"text",
                    editrules:{required:true},
                    editoptions:{
                        size:"20"
                    },
                    formoptions:{rowpos:6, label: "日期", elmprefix:"(*)"}
                }
            ],
            hiddengrid: false,
            autowidth: true,
            rownumbers: true,
            rowNum:10,
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
            onCellSelect: function(){
                //alert("cellselect");
            },
            afterInsertRow:function(rowid,rowdata,rowelem){
                //alert(rowid);
            }
        });

        //导航栏配置和CRUD函数
        jQuery(domTableId).jqGrid('navGrid',domPageId,
            {view:true,search:false,edit:false,add:false,del:false,alerttext:"请选择需要操作的数据行!"}
        );
        //
        var addparameters = {
        }
        //
        var editparameters = {
            "keys" : false,
            "oneditfunc" : null,
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
            "extraparam" : {depsx:cur_dep_sx},
            "aftersavefunc" : null,
            "errorfunc": null,
            "afterrestorefunc" : null,
            "restoreAfterError" : true,
            "mtype" : "POST"
        }
        var parameters = {
            edit: false,
            editicon: "ui-icon-pencil",
            add: false,
            addicon:"ui-icon-plus",
            save: false,
            saveicon:"ui-icon-disk",
            cancel: false,
            cancelicon:"ui-icon-cancel",
            addParams : addparameters,
            editParams : editparameters
        }
        jQuery(domTableId).jqGrid('inlineNav',domPageId,parameters);

        //其他配置
        jQuery(domTableId).jqGrid('gridResize',{minWidth:1500,maxWidth:2000,minHeight:500, maxHeight:1000});
        jQuery(domTableId).jqGrid('sortableRows');


/*         jQuery(domTableId).jqGrid('filterToolbar');
         if($(domPageId + ":contains(Toggle)").length < 1){
             jQuery(domTableId).jqGrid('navButtonAdd',domPageId,{caption:"Toggle",title:"Toggle Search Toolbar", buttonicon :'ui-icon-pin-s',
                 onClickButton:function(){
                     $aletcommgridobj[0].toggleToolbar();
                 }
             });
         }*/
    }
    setAlertJqGrid("#alertsearchlist","#palertsearchlist",$("#user_department").val());
    //----------------------------------------------------------------------------
    //jqGrid setting 结束///////////////////////////////////////////////////////////////////////////////////////////////
});