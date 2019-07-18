<%*$MEMINDEX.tree|echoarr*%>
<div id="doctree">
            <ul>
            <li data-hash="<%$MEMINDEX.root_hash%>" id="folder_treeroot" data-haschildren="1" ><a id="ident-0" href="javascript:void(0)"   data-tid="0">Storage Center</a>
                <ul id="doc-ul-<%$element.id%>">
            <% function name="resrc_treevar" %>
                <%foreach from=$items item=element%>     
                        <li id="resrc_treenode-<%$element.id%>" data-hash="<%$element.hash%>" <% if ($element.haschildren==0) %>data-tid="<%$element.id%>"<%/if%> data-haschildren="<% if ($element.haschildren==1) %>1<%else%>0<%/if%>" data-modid="<%$element.modident%>" <% if ($element.haschildren==0) %>data-jstree='{"icon":"far fa-file-alt"}'<%/if%> >
                        <a id="ident-<%$element.id%>"  
                         data-tid="<%$element.id%>" data-modid="<%$element.modident%>"
                        href="javascript:void(0)" title="<%$element.folder|sthsc%>"
                        ><% if ($element.haschildren==1) %><%$element.folder|st|truncate:10%><%else%><%$element.folder|st%><%/if%>    
                        </a>
                        
                        <%if !empty($element.children)%>
                            <ul><%call name="resrc_treevar" items=$element.children%></ul>
                        <%/if%>
                        
                        </li>
             
                <%/foreach%>
            <%/function%><% call name="resrc_treevar" items=$MEMINDEX.tree %>
                    </ul>
                </li>
          </ul>
    </div>
<small>*Rechts-Klick ist aktiv. 5 Ebenen möglich.</small>
  
    <script>
    
      function customMenu(node) {        
            var items = {
                createItem: { // The "create" menu item
                    label: "Neues Verzeichnis",
                    action: function () {
                        var ref = $('#doctree').jstree(true),
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
                       var ref = $('#doctree').jstree(true),
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
                       var ref = $('#doctree').jstree(true),
    								sel = ref.get_selected();
    							if(!sel.length) { return false; }
    							ref.delete_node(sel);
                            }    
                    }
                }
            };
            if (node.children.length>0) {
              //  delete items.deleteItem;
                delete items.renameItem;
            }
            else{
               // delete items.createItem;
            }
            if (node.data.isadmin==1) {
                delete items.deleteItem;
                delete items.renameItem;
            }
    
            return items;
        }
                
       <%include file="memindex.editor.docs.tree.script.tpl"%>
       init_doc_tree();
    
    <% if ($GET.doopentree==1)%>             
        <% if ($GET.dirid!="" && $GET.dirid!="folder_treeroot"   ) %>
/*           var parentid = $('#<%$GET.dirid%>').parent().parent().attr('id');
            $("#doctree").jstree("open_node", "#"+parentid);
            console.log('<%$GET.dirid%>');
            */
            $("#doctree").jstree("open_all");
       <%else%>
            //$("#doctree").jstree("open_node", "#folder_treeroot");
            $("#doctree").jstree("open_all");
        <%/if%>
    <%else%>    
        $("#doctree").jstree("open_all");
    <%/if%>
       
    
    </script>