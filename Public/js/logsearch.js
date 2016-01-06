$(document).ready(function(){
    //jqGrid setting 开始.//////////////////////////////////////////////////////////////////////////////////////////////

    function getColModel_Id(){
        return {name:'id',index:'id',
            width:2,align:"right",
            sortable:true,
            editable:false,
            search: false,
            editoptions:{readonly:true,size:2},
            formoptions:{rowpos:1, label: "id", elmprefix:"(*)"}
        };
    }
    function getColModel_Comm_text(indexname,rowpos,label,length,search,require){
        return {
            name:indexname,
            index:indexname,
            width:(typeof(length ) == "undefined") ? 20 : length,
            align:"right",
            sortable:true,
            search: (typeof(search ) == "undefined") ? true : false,
            editable:true, edittype:"text",
            editrules:{required: (typeof(require )== "undefined") ? true : require},
            editoptions:{
                size:(typeof(length ) == "undefined") ? 20 : length
            },
            formoptions:{rowpos:rowpos, label: label, elmprefix:"(*)"}
        };
    }
    var logsearchListGrid = jQuery("#logsearchlist").jqGrid({
        url:'/index.php/Home/log/ajaxlogSearchSearch',
        datatype: "json",
        mtype: 'POST',
        colNames:['ID','生成日期','日志类型','日志信息'],
        colModel:[
            getColModel_Id()
            ,
            getColModel_Comm_text("log_time",2,"生成日期",5)
            ,
            {
                name: "lx",
                index: "lx",
                width:3,
                align:"right",
                sortable:true,
                stype:'select',//查询类型
                editable:true,edittype:"select",
                editoptions:{
                    value:{
                        '0':'全部',
                        '101':'HTTP'
                    },
                    defaultValue:"0"
                },
                editrules:{required:true},
                formoptions:{ rowpos:3,label: "日志类型" ,elmprefix:"(*)"}
            }
            ,
            getColModel_Comm_text("log_msg",4,"日志信息",30)
        ],
        hiddengrid: false,
        autowidth: true,
        rownumbers: true,
        rowNum:10,
        rowList:[10,20,30],
        pager: '#plogsearchlist',
        viewrecords: true,
        sortorder: "desc",
        forceFit : true,
        caption:"日志查询",
        onSelectRow: function(id){
            /*            var rowid = jQuery("#logsearchlist").jqGrid('getGridParam','selrow');
             if(rowid){
             var rowData = $("#logsearchlist").jqGrid('getRowData',rowid);
             alert(rowData.pro_id);
             }*/
        }
    });
    //导航栏配置和CRUD函数
    jQuery("#logsearchlist").jqGrid('navGrid','#plogsearchlist',
        {view:true,edit:false,add:false,del:false,search:false}
    );

    //其他配置
    jQuery("#logsearchlist").jqGrid('gridResize',{minWidth:350,maxWidth:1000,minHeight:80, maxHeight:350});
    jQuery("#logsearchlist").jqGrid('sortableRows');
    jQuery("#logsearchlist").jqGrid('filterToolbar');
    jQuery("#logsearchlist").jqGrid('navButtonAdd',"#plogsearchlist",{caption:"Toggle",title:"Toggle Search Toolbar", buttonicon :'ui-icon-pin-s',
        onClickButton:function(){
            logsearchListGrid[0].toggleToolbar();
        }
    });
    //jqGrid setting 结束///////////////////////////////////////////////////////////////////////////////////////////////
});