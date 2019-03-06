<% if ($PERM.core_acc_systpl==1) %>
<div id="topltreeul">
<ul>
<li id="topltreeroot" data-haschildren="1" ><a id="ident-0" href="javascript:void(0)"  data-tid="0">Themen</a>
    <ul>
<% function name="gbltpltree" %>
    <%foreach from=$items item=element%>     
            <li id="topltreenode-<%$element.id%>" <% if ($element.haschildren==0) %>data-tid="<%$element.id%>"<%/if%> data-haschildren="<% if ($element.haschildren==1) %>1<%else%>0<%/if%>" data-modid="<%$element.modident%>" data-isadmin="<%$element.admin%>" <% if ($element.haschildren==0) %>data-jstree='{"icon":"glyphicon glyphicon-file"}'<%/if%> >
            <a id="ident-<%$element.id%>"  
             data-tid="<%$element.id%>" data-modid="<%$element.modident%>"
            href="javascript:void(0)" title="<%$element.description|sthsc%>"
            ><% if ($element.haschildren==1) %><%$element.module_name|st%><%else%><%$element.description|st%><%/if%>    
            </a>
            
            <%if !empty($element.children)%>
                <ul><%call name="gbltpltree" items=$element.children%></ul>
            <%/if%>
            
            </li>
 
    <%/foreach%>
<%/function%><% call name="gbltpltree" items=$TOPLMAN.topleveltree %>
        </ul>
    </li>
 </ul> 
</div>

<script>

  function customMenu(node) {
    // console.log(node.toSource());
        // The default set of all items
        var items = {
            createItem: { // The "create" menu item
                label: "Neuer Toplevel",
                action: function () {
                    var ref = $('#topltreeul').jstree(true),
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
                   var ref = $('#topltreeul').jstree(true),
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
                   var ref = $('#topltreeul').jstree(true),
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
    $('#topltreeul').jstree({  
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
                    "icon" : "glyphicon glyphicon-folder-open",
                    "valid_children" : ["default","file"]
                },
                "file" : {
                    "icon" : "glyphicon glyphicon-file",
                    "valid_children" : []
                }
            },
          "contextmenu": {items: customMenu}
     
        }).bind("select_node.jstree", function(event,data) {  
           var link_id = data.node.id;
           if ($('#'+link_id).data('haschildren')==0 && $('#'+link_id).data('tid')>0) {
                simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=tplmgr.inc&id='+$('#'+link_id).data('tid')+'&cmd=ax_topl_edit');
                return;
           } 
           if ($('#'+link_id).data('haschildren')==1) {
             if ($("#"+link_id).hasClass("jstree-open")) {
                    $("#topltreeul").jstree("close_node", "#" + link_id);
                   }
                else {                    
                    $("#topltreeul").jstree("close_all");
                    $("#topltreeul").jstree("open_node", "#topltreeroot");
                    $("#topltreeul").jstree("open_node", "#" + link_id);
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=tplmgr.inc&id='+$('#'+link_id).data('tid')+'&cmd=ax_show_all');
                }    
           }
        }).on('rename_node.jstree', function (e, data) {  
            $('#topleveldescription').val(data.text);
            $.ajax({
                 url: '?epage=tplmgr.inc&cmd=rename_topl',
                 data: { 'id' : data.node.id, 'FORM[description]' : data.text },
                 async :true,
                 dataType :'json'
                 })
                .done(function (result) {
                   
                })
                .fail(function () {
                    alert('ren err');
                    data.instance.refresh();
                });
        }).on('delete_node.jstree', function (e, data) {
            $.getJSON( "?epage=tplmgr.inc&ident="+$('#'+data.node.id).data('tid')+"&cmd=deltoplevel", function( data ) {                
                    if (data.msge != "") {
                           $('#savedresult').html(data.msge);
                           $('#savedresult').removeClass('okbox').addClass('faultboxajax');
                           show_saved_msg(3000);
                        } else {
                           $('#admincontent').html('');
                        }                    
                });
                
        }).on('create_node.jstree', function (e, data) {             
            $.ajax({
                 url: '?epage=tplmgr.inc&cmd=create_toplevel',
                 data: { 'id' : data.node.parent, 'position' : data.position, 'FORM[description]' : data.node.text },
                 async :false,
                 dataType :'json'
                 })
                .done(function (d) {
                    data.instance.set_id(data.node, 'topltreenode-'+d.id);
                    $('#topltreenode-'+d.id).attr('data-haschildren','0');
                    $('#topltreenode-'+d.id).attr('data-tid',d.id);
                    $('#topltreenode-'+d.id+' a').attr('id','ident-'+d.id);
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=tplmgr.inc&id='+d.id+'&cmd=ax_topl_edit');
                })
                .fail(function (d,error) {
                    alert('create err');
                    data.instance.refresh();
                });
        });

      
</script>
<%/if%>