<ul class="sub-sub-menu">
    <% function name="resrc_treevar" %>
        <%foreach from=$items item=element%>     
                <li>
                    <a class="js-resource-click" id="ident-<%$element.id%>"  
                     data-tid="<%$element.id%>" data-modid="<%$element.modident%>" data-haschildren="<% if ($element.haschildren==1) %>1<%else%>0<%/if%>"
                    href="javascript:void(0)" title="<%$element.f_name|sthsc%>"
                    ><% if ($element.haschildren==1) %><%$element.f_name|st|truncate:10%><%else%><%$element.f_name|st%><%/if%>    
                    </a>
                    
                    <%if !empty($element.children)%>
                        <ul class="sub-sub-menu"><%call name="resrc_treevar" items=$element.children%></ul>
                    <%/if%>
                
                </li>
     
        <%/foreach%>
    <%/function%><% call name="resrc_treevar" items=$flextpl_list %>
            </ul>
<div class="sub-sub-link">
    <a onclick="simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=resource.inc&cmd=ax_start&section=start');" href="javascript:void(0)" data-tid="0" class="menu-toggle">Resource Manager</a>
    <a onclick="simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=resource.inc&cmd=ax_start&section=start');" href="javascript:void(0)" data-tid="0" class="menu-toggle toggle-btn"><i class="fas fa-chevron-right"></i></a>
</div>             

<script>
$( ".js-resource-click" ).unbind('click');
$( ".js-resource-click" ).click(function(e) {
   if ($(this).data('haschildren')==0 && $(this).data('tid')>0) {
        simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=resource.inc&id='+$(this).data('tid')+'&section=edit&cmd=ax_editflextpl');
        scrollToAnchor('admincontent');
   }
});  

init_tree_toggle();
  
</script>    

<%*    
    <script>
    
      function customMenu(node) {
        // console.log(node.toSource());
            // The default set of all items
            var items = {
                createItem: { // The "create" menu item
                    label: "Neue Resource",
                    action: function () {
                        var ref = $('#resrc_treeul').jstree(true),
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
                       var ref = $('#resrc_treeul').jstree(true),
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
                       var ref = $('#resrc_treeul').jstree(true),
    								sel = ref.get_selected();
    							if(!sel.length) { return false; }
    							ref.delete_node(sel);
                            }    
                    }
                }
            };
            if (node.children.length>0) {
                delete items.deleteItem;
                delete items.renameItem;
            }
            else{
                delete items.createItem;
            }
            if (node.data.isadmin==1) {
                delete items.deleteItem;
                delete items.renameItem;
            }
    
            return items;
        }
        // ,"dnd", "search",    "state", "types", "wholerow"
        $('#resrc_treeul').jstree({  
             "plugins" : [ "contextmenu", "types", "wholerow" ],
             core : {
                "animation" : 0,
                "check_callback" : true,
                themes : {
                    
                }
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
               if ($('#'+link_id).data('haschildren')==0 && $('#'+link_id).data('tid')>0) {
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=resource.inc&id='+$('#'+link_id).data('tid')+'&section=edit&cmd=ax_editflextpl');
                    return;
               } 
               if ($('#'+link_id).data('haschildren')==1) {
                 if ($("#"+link_id).hasClass("jstree-open")) {
                        $("#resrc_treeul").jstree("close_node", "#" + link_id);
                       }
                    else {                    
                        $("#resrc_treeul").jstree("close_all");
                        $("#resrc_treeul").jstree("open_node", "#resrc_treeroot");
                        $("#resrc_treeul").jstree("open_node", "#" + link_id);                        
                    }    
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=resource.inc&cmd=ax_start&section=start');
               }
            }).on('rename_node.jstree', function (e, data) {  
                $('#resrc_title').val(data.text);
                $.ajax({
    			     url: 'run.php?epage=resource.inc&cmd=rename_flextpls',
                     data: { 'id' : $('#'+data.node.id).data('tid'), 'FORM[f_name]' : data.text },
                     async :true,
                     dataType :'json'
                     })
                    .done(function (result) {
                       simple_load('orga-resource','<%$PATH_CMS%>admin/run.php?epage=resource.inc&cmd=load_tpl_tree&doopentree=1');
                       simple_load('js-flxtpls', '<%$eurl%>cmd=load_rsrctable');
    				})
                    .fail(function () {
                        alert('ren err');
    					data.instance.refresh();
    				});
            }).on('delete_node.jstree', function (e, data) {
                $.getJSON( "/admin/run.php?epage=resource.inc&id="+$('#'+data.node.id).data('tid')+"&cmd=axdelresrcbytree", function( data ) {				
    					if (data.msge != "") {
                               $('#savedresult').html(data.msge);
                               $('#savedresult').removeClass('okbox').addClass('faultboxajax');
                               show_saved_msg(3000);
                            } else {
                               simple_load('js-flxtpls', '<%$eurl%>cmd=load_rsrctable');
                            }                    
    				});
                    
            }).on('create_node.jstree', function (e, data) {             
                data.node.text='New Resource';
                $.ajax({
    			     url: '<%$PATH_CMS%>admin/run.php?epage=resource.inc&cmd=ax_create_flextpl',
                     data: { 'id' : data.node.parent, 'position' : data.position, 'FORM[f_name]' : data.node.text },
                     async :false,
                     dataType :'json'
                     })
    				.done(function (d) {
    					data.instance.set_id(data.node, 'resrc_treenode-'+d.id);
                        $('#resrc_treenode-'+d.id).attr('data-haschildren','0');
                        $('#resrc_treenode-'+d.id).attr('data-tid',d.id);
                        $('#resrc_treenode-'+d.id+' a').attr('id','ident-'+d.id);
                        simple_load('orga-resource','<%$PATH_CMS%>admin/run.php?epage=resource.inc&cmd=load_tpl_tree&doopentree=1');
                        simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=resource.inc&id='+d.id+'&cmd=ax_edittpl');
    				})
    				.fail(function (d,error) {
    				    alert('create err '+error);
    					data.instance.refresh();
    				});
            });
    
    <% if ($GET.doopentree==1)%>
        $("#resrc_treeul").jstree("open_node", "#resrc_treeroot");
    <%/if%>
    </script>
*%>