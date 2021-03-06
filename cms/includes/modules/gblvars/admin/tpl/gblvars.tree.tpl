<ul class="sub-sub-menu">
<% function name="gblvarstreevar" %>
    <%foreach from=$items item=element%>     
            <li>
                <a class="js-gblvars-click"   
                 data-tid="<%$element.var_name%>" data-modid="<%$element.modident%>" data-haschildren="<% if ($element.haschildren==1) %>1<%else%>0<%/if%>"
                href="javascript:void(0)" title="<%$element.var_desc|sthsc%>"
                ><% if ($element.haschildren==1) %><%$element.var_desc|st%><%else%><%$element.var_desc|st%><%/if%>    
                </a>                
                <%if !empty($element.children)%>
                    <ul class="sub-sub-menu"><%call name="gblvarstreevar" items=$element.children%></ul>
                <%/if%>
            </li>
 
    <%/foreach%>
<%/function%><% call name="gblvarstreevar" items=$GBLVARS.vars %>
        </ul>
<div class="sub-sub-link">
    <a onclick="simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&cmd=ax_start&section=start');" href="javascript:void(0)" data-tid="0" class="menu-toggle">Globale Variablen</a>
    <a onclick="simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&cmd=ax_start&section=start');" href="javascript:void(0)" data-tid="0" class="menu-toggle toggle-btn"><i class="fas fa-chevron-right"></i></a>
</div>  


<script>
$( ".js-gblvars-click" ).unbind('click');
$( ".js-gblvars-click" ).click(function(e) {
   if ($(this).data('haschildren')==0 && $(this).data('tid')!="") {
        simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&id='+$(this).data('tid')+'&cmd=ax_edit');
   }
});  

init_tree_toggle();
  
</script>  

<%*
<script>

  function customMenu(node) {    
        var items = {
            createItem: { 
                label: "Neue Variable",
                action: function () {
                    var ref = $('#gblvarstreeul').jstree(true),
								sel = ref.get_selected();
							if(!sel.length) { return false; }
							sel = sel[0];
							sel = ref.create_node(sel, {"type":"file"});
							if(sel) {
								ref.edit(sel);
							}
                }
            },
            renameItem: { // The "rename" menu item
                label: "umbennen",
                action: function () {
                   var ref = $('#gblvarstreeul').jstree(true),
								sel = ref.get_selected();
							if(!sel.length) { return false; }
							sel = sel[0];
							ref.edit(sel);
                            
                }
            },
            deleteItem: { // The "delete" menu item
                label: "{LBL_DELETE}",
                action: function () {
                  if (confirm("{LBL_CONFIRM}")){
                   var ref = $('#gblvarstreeul').jstree(true),
								sel = ref.get_selected();
							if(!sel.length) { return false; }
							ref.delete_node(sel);
                        }    
                }
            }
        };
    
        if (node.parent!='gblvarstreeroot') {
            delete items.deleteItem;
            delete items.renameItem;
        }

        return items;
    }
    // ,"dnd", "search",    "state", "types", "wholerow"
    $('#gblvarstreeul').jstree({  
         "plugins" : [ "contextmenu", "types", "wholerow" ],
         core : {
            "animation" : 0,
            "check_callback" : true,
         },
          "types" : {
                    "#" : {
                    "max_children" : 1, 
                    "max_depth" : 4, 
                    "valid_children" : ["root"]
                },    
                "default" : {
                    "icon" : "fas fa-folder-open",
                    "valid_children" : ["default","file"]
                },
                "file" : {
                    "icon" : "far fa-file-alt",
                    "valid_children" : []
                }
            },
          "contextmenu": {items: customMenu}
     
        }).bind("select_node.jstree", function(event,data) {  
           var link_id = data.node.id;
           if ($('#'+link_id).data('haschildren')==0 && $('#'+link_id).data('tid')!="") {
                simple_load('js-gblvar-editor','<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&id='+$('#'+link_id).data('tid')+'&cmd=ax_edit');
                return;
           } 
           if ($('#'+link_id).data('haschildren')==1) {
             if ($("#"+link_id).hasClass("jstree-open")) {
                    $("#gblvarstreeul").jstree("close_node", "#" + link_id);
                   }
                else {                    
                    $("#gblvarstreeul").jstree("close_all");
                    $("#gblvarstreeul").jstree("open_node", "#gblvarstreeroot");
                    $("#gblvarstreeul").jstree("open_node", "#" + link_id);
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&cmd=ax_start&section=start');
                }    
           }
        }).on('rename_node.jstree', function (e, data) {
            $.ajax({
			     url: '<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&cmd=rename_gblvars',
                 data: { 'id' : $('#'+data.node.id).data('tid'), 'FORM[var_desc]' : data.text },
                 async :false,
                 dataType :'json'
                 })
                .done(function (result) {     
                    var container ="";
                    if ($("#js-gblvar-editor")){
                        container = 'js-gblvar-editor';
                    } else {
                        container = 'admincontent';
                    }
                    simple_load(container,'<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&id='+result.id+'&cmd=ax_edit');
        
                    reload_gblvar_tree(1);
				})
                .fail(function (result) {
                    alert('ren err: '+result.toSource());
                    console.log(result.toSource());
					data.instance.refresh();
				});
        }).on('delete_node.jstree', function (e, data) {
            $.getJSON( "<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&ident="+$('#'+data.node.id).data('tid')+"&cmd=axdelgblvars", function( data ) {				
					if (data.msge != "") {
                           $('#savedresult').html(data.msge);
                           $('#savedresult').removeClass('okbox').addClass('faultboxajax');
                           show_saved_msg(3000);
                        } else {
                           $('#js-gblvar-editor').html('');
                           $('#js-gblvar-table').html('');
                        }                    
				});
                
        }).on('create_node.jstree', function (e, data) {
            $.ajax({
			     url: '<%$PATH_CMS%>admin/run.php?epage=gblvars.inc&cmd=ax_create_gblvars',
                 data: { 'id' : data.node.parent, 'position' : data.position, 'FORM[var_desc]' : data.node.text },
                 async :false,
                 dataType :'json'
                 })
				.done(function (d) {
					data.instance.set_id(data.node, 'gblvarstreenode-'+d.id);
                    $('#gblvarstreenode-'+d.id).attr('data-haschildren','0');
                    $('#gblvarstreenode-'+d.id).attr('data-tid',d.id);
                    $('#gblvarstreenode-'+d.id+' a').attr('id','ident-'+d.id);                  
				})
				.fail(function (d,error) {
				    alert('create err: '+error);
                    console.log(d.toSource());
					data.instance.refresh();
				});
        });

<% if ($GET.doopentree==1)%>
    $("#gblvarstreeul").jstree("open_node", "#gblvarstreeroot");
<%/if%>
      
</script>

*%>