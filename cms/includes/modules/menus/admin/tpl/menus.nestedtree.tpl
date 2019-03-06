<div id="js-mmenu-tree-cont">
    
<ul>
<li id="js-mmenu-root" data-haschildren="1" data-tid="0" ><a id="ident-0" href="javascript:void(0)"  data-tid="0"><%$MENUS.menu.m_name%></a>
    <ul>
<% function name="recurmenu" %>
    <%foreach from=$items item=element%>     
            <li data-tid="<%$element.id%>" data-mm_id="<%$element.mm_id%>" data-link="#" id="pagenode-<%$element.id%>" data-haschildren="<% if (!empty($element.children)) %>1<%else%>0<%/if%>" data-tid="<%$element.id%>" data-isadmin="<%$element.admin%>" <% if (empty($element.children)) %>data-jstree='{"icon":"glyphicon glyphicon-file"}'<%/if%> >
            <a data-tid="<%$element.id%>" id="ident-<%$element.id%>" href="#" title="<%$element.description|hsc%>"
            ><%$element.description%></a>            
            <%if !empty($element.children)%>
                <ul><%call name="recurmenu" items=$element.children%></ul>
            <%/if%>            
            </li> 
    <%/foreach%>
    <%/function%><% call name="recurmenu" items=$MENUS.nested_menu %>
        </ul>
    </li>
 </ul> 
</div>

        
<script>

  function customMenu(node) {
        var items = {
            deleteItem: { // The "delete" menu item
                label: "{LBL_DELETE}",
                action: function () {
                  if (confirm("{LBL_CONFIRM}")){
                   var ref = $('#js-mmenu-tree-cont').jstree(true),
							sel = ref.get_selected();
							if(!sel.length) { return false; }
							ref.delete_node(sel);
                        }    
                }
            
           },
           editItem: { // The "edit" menu item
                label: "{LBL_EDIT}",
                action: function () {
                    var ref = $('#js-mmenu-tree-cont').jstree(true),	sel = ref.get_selected();
                    if(!sel.length) { return false; }      
                    simple_load('js-menuedit','<%$eurl%>cmd=edit_man_item&id='+$('#'+sel[0]).data('tid')+'&menuid=<%$MENUS.menu.id%>');  
                }
            }           
          
        };
        if (node.children.length>0) {
            delete items.deleteItem;
        }
        if (node.data.mm_id>0) {
            delete items.editItem;
        }
        return items;
    }
    var lastnode="";
    


    $('#js-mmenu-tree-cont').jstree({  
       
         "plugins" : [ "contextmenu", "types", "wholerow",  "dnd" ],
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
                    "valid_children" : ["default","file"]
                },
                "file" : {
                    "icon" : "glyphicon glyphicon-file",
                    "valid_children" : []
                }
            },
          "contextmenu": {items: customMenu},
         
     
        }).bind("select_node.jstree", function(event,data) {  
           var link_id = data.node.id;
           if ($('#'+link_id).data('haschildren')==0 && $('#'+link_id).data('tid')>0) {           
              //     simple_load('js-menuedit','<%$eurl%>cmd=edit_man_item&id='+$('#'+link_id).data('tid')+'&menuid=<%$MENUS.menu.id%>');
                show_msg('Use right mouse click for edit',1000);
                return;
           }
           var cmd = '<%$GET.urlcmd%>';
           var urltid = '<%$GET.id%>';
           var tid = $('#'+link_id).data('tid');
           if ($('#'+link_id).data('haschildren')==1 || cmd!='edit' || (urltid!=tid && cmd=='edit')) {
              $("#js-mmenu-tree-cont").jstree("open_node", "#js-mmenu-root");
              expand_node_webtree( link_id,'js-mmenu-tree-cont');
              if ($('#'+link_id).data('tid')>0) {
              //  simple_load('admincontent','<%$eurl%>cmd=page_axedit&id='+$('#'+link_id).data('tid'));
               } else { 
               // load_pages($('#'+link_id).data('tid'));
              }
           }
        }).on("after_open.jstree", function (event, data) {
            var ref = $('#js-mmenu-tree-cont').jstree(true),	sel = ref.get_selected();
			     if(!sel.length) { return false; }
            if ($('#'+sel[0]).data('haschildren')==0 && $('#'+sel[0]).data('tid')>0) {                
                lastnode = sel[0];
            }
        }).on("ready.jstree", function (event, data) {
            $("#js-mmenu-tree-cont").jstree("open_node", "#js-mmenu-root");           
           <% if ($GET.id>0) %>
            expand_node_webtree('pagenode-<%$GET.id%>','js-mmenu-tree-cont');
            $("#js-mmenu-tree-cont").jstree("select_node", 'pagenode-<%$GET.id%>');
           <%/if%>         
        }).on('delete_node.jstree', function (e, data) {
			$.get('<%$eurl%>cmd=delete_item', { 'id' : $('#'+data.node.id).data('tid') })
				.done(function () {
					//$('#admincontent').html('');
                    msg('{LBL_DELETED}',3000);
				})
                .fail(function () {
                    alert('del err');
					data.instance.refresh();
				});
        }).on('move_node.jstree', function (e, data) {
            var moveitemID = data.node.id.replace("pagenode-", "");
            var newParentID = $('#' + data.parent).find('a')[0].id;        
            var oldParentID = $('#' + data.old_parent).find('a')[0].id;        
            var ref = $('#js-mmenu-tree-cont').jstree(true);
            var fnode = ref.get_node('#pagenode-'+moveitemID);
            var next_node_id = parseInt($(ref.get_next_dom(fnode).context).data('tid'));
            var prev_node_id = parseInt($(ref.get_prev_dom(fnode).context).data('tid'));
            //  console.log(moveitemID+' '+newParentID+' '+oldParentID + ' ' +next_node_id +' '+prev_node_id);
            var url = '<%$PATH_CMS%>admin/run.php?epage=menus.inc&cmd=ax_sort_menutree&next_node_id='+next_node_id+'&prev_node_id='+prev_node_id+'&tid='+moveitemID+'&parent='+$('#'+newParentID).data('tid');            
            execrequest(url);
        })
       
      
      
</script>