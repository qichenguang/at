$(document).ready(function(){
    //jqGrid setting 开始.//////////////////////////////////////////////////////////////////////////////////////////////

    function pro_id_format( cellvalue, options, rowObject ){
        return '<a href="/index.php/Home/AllInOne/allmodules/pro_id/' + cellvalue + '"  target="_blank" >' + cellvalue + '</a>';
    }
    function pro_id_unformat(cellvalue, options, cell){
        return $('a', cell).text();
    }

    function getColModel_Id(){
        return {name:'id',index:'id',
            width:5,align:"right",
            sortable:true,
            editable:false,
            search: false,
            editoptions:{readonly:true,size:5},
            formoptions:{rowpos:1, label: "id", elmprefix:"(*)"}
        };
    }
    function getColModel_Comm_text(indexname,rowpos,label,length,search,require){
        return {
            name:indexname,
            index:indexname,
            width:(typeof(length )== "undefined") ? 20 : length,
            align:"right",
            sortable:true,
            search: (typeof(search )== "undefined") ? true : false,
            editable:true, edittype:"text",
            editrules:{required: (typeof(require )== "undefined") ? true : require},
            editoptions:{
                size:(typeof(length )== "undefined") ? 20 : length
            },
            formoptions:{rowpos:rowpos, label: label, elmprefix:"(*)"}
        };
    }
    var projectsearchListGrid = jQuery("#projectsearchlist").jqGrid({
        url:'/index.php/Home/Project/ajaxProjectSearchSearch',
        datatype: "json",
        mtype: 'POST',
        colNames:['ID','项目ID','项目名称','项目初始金额','项目资金锁定','开始日期','结束日期'],
        colModel:[
            getColModel_Id()
            ,
            {name:'pro_id',index:'pro_id',
                width:20,align:"right",
                sortable:true,
                editable:true, edittype:"text",editrules:{required:true},
                editoptions:{size:"20"},
                formatter:pro_id_format, unformat:pro_id_unformat,
                formoptions:{rowpos:2, label: "项目ID", elmprefix:"(*)"}
            }
            ,
            getColModel_Comm_text("pro_name",3,"项目名称",30)
            ,
            getColModel_Comm_text("pro_name",4,"项目初始金额",30)
            ,
            {
                name:"zj_lock",
                index:"zj_lock",
                width:10,
                align:"center",
                sortable:true,
                stype:'select',//查询类型
                editable:true,edittype:"select",
                editoptions:{
                    value:{
                        '0':"全部",
                        '1':"未锁定",
                        '2':"已锁定"
                    },
                    defaultValue:"0"
                },
                editrules:{required: true },
                formoptions:{ rowpos:5,label:"项目资金锁定",elmprefix:"(*)"}
            }
            ,
            getColModel_Comm_text("pro_ks_time",6,"实际开工日期",15,false)
            ,
            getColModel_Comm_text("pro_js_time",7,"实际完工日期",15,false)
        ],
        hiddengrid: false,
        autowidth: true,
        rownumbers: true,
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pprojectsearchlist',
        viewrecords: true,
        sortorder: "desc",
        forceFit : true,
        caption:"项目查询",
        onSelectRow: function(id){
/*            var rowid = jQuery("#projectsearchlist").jqGrid('getGridParam','selrow');
            if(rowid){
                var rowData = $("#projectsearchlist").jqGrid('getRowData',rowid);
                alert(rowData.pro_id);
            }*/
        }
    });
    //导航栏配置和CRUD函数
    jQuery("#projectsearchlist").jqGrid('navGrid','#pprojectsearchlist',
        {view:true,edit:false,add:false,del:false,search:false}
    );

    //其他配置
    jQuery("#projectsearchlist").jqGrid('gridResize',{minWidth:350,maxWidth:1000,minHeight:80, maxHeight:350});
    jQuery("#projectsearchlist").jqGrid('sortableRows');
    jQuery("#projectsearchlist").jqGrid('filterToolbar');
    jQuery("#projectsearchlist").jqGrid('navButtonAdd',"#pprojectsearchlist",{caption:"Toggle",title:"Toggle Search Toolbar", buttonicon :'ui-icon-pin-s',
        onClickButton:function(){
            projectsearchListGrid[0].toggleToolbar();
        }
    });
    //jqGrid setting 结束///////////////////////////////////////////////////////////////////////////////////////////////
});