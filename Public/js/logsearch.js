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
	//日志查询
    function setLogCommJqGrid(domTableId,domPageId){
        var search_url = '/index.php/Home/log/ajaxlogSearchSearch';
        var save_url = '/index.php/Home/log/ajaxLogSearchSave';
        var colNames = ['ID','生成日期','日志类型','日志信息'];
        var caption_str = "日志查询";
		var $logcommgridobj = jQuery(domTableId).jqGrid({
            url: search_url,
            datatype: "json",
            mtype: 'POST',
            postData: {},
            colNames: colNames,
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
							'101':'H_I_S'
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
            rowNum:20,
            rowList:[20,30,50],
            pager: domPageId,
            viewrecords: true,
            sortorder: "desc",
            forceFit : true,
            editurl: save_url,
            caption:caption_str,
            onSelectRow: function(id){
			},
            onCellSelect: function(rowid,iCol,cellcontent,e){
                //alert("cellselect" + cellcontent);
            },
            afterInsertRow:function(rowid,rowdata,rowelem){
                //alert(rowid);
            },
            subGrid: false,
            // define the icons in subgrid
            subGridOptions: {
                "plusicon"  : "ui-icon-triangle-1-e",
                "minusicon" : "ui-icon-triangle-1-s",
                "openicon"  : "ui-icon-arrowreturn-1-e"
                //expand all rows on load
                //,"expandOnLoad" : true
                // load the subgrid data only once
                // and the just show/hide
                ,"reloadOnExpand" : true
                // select the row when the expand column is clicked
                ,"selectOnExpand" : true
            },
            subGridRowExpanded: function(subgrid_id, row_id) {
                var subgrid_table_id, pager_id;
                subgrid_table_id = subgrid_id+"_t";
                pager_id = "p_"+subgrid_table_id;
                $("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
                jQuery("#"+subgrid_table_id).jqGrid({
                    url:"/index.php/Home/Log/ajaxLogSearchDetail",
                    datatype: "json",
                    mtype: 'POST',
                    postData: {id:row_id},
                    colNames: ['MsgId','详细信息'],
                    colModel: [
                        {name:"MsgId",index:"MsgId",align:"right",key:true,width:50},
                        {name:"log_detail",index:"log_detail",width:600}
                    ],
                    rowNum:20,
                    pager: pager_id,
                    height: '100%'
                });
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
                extraparam:{},
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
            "extraparam" : {},
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
                    $logcommgridobj[0].toggleToolbar();
                }
            });
        }
    }
	setLogCommJqGrid("#logsearchlist","#plogsearchlist");
    //jqGrid setting 结束///////////////////////////////////////////////////////////////////////////////////////////////
});