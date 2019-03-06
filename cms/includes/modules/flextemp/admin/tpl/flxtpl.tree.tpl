<% if ($PERM.core_acc_flextemplates==true) %>
    <div id="flextpls_treeul">
    <ul>
    <li id="flextpls_treeroot" data-haschildren="1" ><a id="ident-0" href="javascript:void(0)"  data-tid="0">Flex Templates</a>
        <ul>
    <% function name="flextpls_treevar" %>
        <%foreach from=$items item=element%>     
                <li id="flextpls_treenode-<%$element.id%>" <% if ($element.haschildren==0) %>data-tid="<%$element.id%>"<%/if%> data-haschildren="<% if ($element.haschildren==1) %>1<%else%>0<%/if%>" data-modid="<%$element.modident%>" data-isadmin="<%$element.admin%>" <% if ($element.haschildren==0) %>data-jstree='{"icon":"glyphicon glyphicon-file"}'<%/if%> >
                <a id="ident-<%$element.id%>"  
                 data-tid="<%$element.id%>" data-modid="<%$element.modident%>"
                href="javascript:void(0)" title="<%$element.f_name|sthsc%>"
                ><% if ($element.haschildren==1) %><%$element.f_name|st|truncate:10%><%else%><%$element.f_name|st%><%/if%>    
                </a>
                
                <%if !empty($element.children)%>
                    <ul><%call name="flextpls_treevar" items=$element.children%></ul>
                <%/if%>
                
                </li>
     
        <%/foreach%>
    <%/function%><% call name="flextpls_treevar" items=$flextpl_list %>
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
                    label: "Neues User Template",
                    action: function () {
                        var ref = $('#flextpls_treeul').jstree(true),
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
                       var ref = $('#flextpls_treeul').jstree(true),
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
                       var ref = $('#flextpls_treeul').jstree(true),
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
        $('#flextpls_treeul').jstree({  
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
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=flextemp.inc&id='+$('#'+link_id).data('tid')+'&section=edit&cmd=ax_editflextpl');
                    return;
               } 
               if ($('#'+link_id).data('haschildren')==1) {
                 if ($("#"+link_id).hasClass("jstree-open")) {
                        $("#flextpls_treeul").jstree("close_node", "#" + link_id);
                       }
                    else {                    
                        $("#flextpls_treeul").jstree("close_all");
                        $("#flextpls_treeul").jstree("open_node", "#flextpls_treeroot");
                        $("#flextpls_treeul").jstree("open_node", "#" + link_id);                        
                    }    
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=flextemp.inc&cmd=ax_start&section=start');
               }
            }).on('rename_node.jstree', function (e, data) {  
                $('#flextpls_title').val(data.text);
                $.ajax({
    			     url: 'run.php?epage=flextemp.inc&cmd=rename_flextpls',
                     data: { 'id' : $('#'+data.node.id).data('tid'), 'FORM[f_name]' : data.text },
                     async :true,
                     dataType :'json'
                     })
                    .done(function (result) {
                       simple_load('orga-flextemplates','<%$PATH_CMS%>admin/run.php?epage=flextemp.inc&cmd=load_tpl_tree&doopentree=1');
                       simple_load('js-flxtpls', '<%$eurl%>cmd=load_flxtpls');
    				})
                    .fail(function () {
                        alert('ren err');
    					data.instance.refresh();
    				});
            }).on('delete_node.jstree', function (e, data) {
                $.getJSON( "/admin/run.php?epage=flextemp.inc&id="+$('#'+data.node.id).data('tid')+"&cmd=axdelflextplsbytree", function( data ) {				
    					if (data.msge != "") {
                               $('#savedresult').html(data.msge);
                               $('#savedresult').removeClass('okbox').addClass('faultboxajax');
                               show_saved_msg(3000);
                            } else {
                               simple_load('js-flxtpls', '<%$eurl%>cmd=load_flxtpls');
                            }                    
    				});
                    
            }).on('create_node.jstree', function (e, data) {             
                $.ajax({
    			     url: '<%$PATH_CMS%>admin/run.php?epage=flextemp.inc&cmd=ax_create_flextpl',
                     data: { 'id' : data.node.parent, 'position' : data.position, 'FORM[f_name]' : data.node.text },
                     async :false,
                     dataType :'json'
                     })
    				.done(function (d) {
    					data.instance.set_id(data.node, 'flextpls_treenode-'+d.id);
                        $('#flextpls_treenode-'+d.id).attr('data-haschildren','0');
                        $('#flextpls_treenode-'+d.id).attr('data-tid',d.id);
                        $('#flextpls_treenode-'+d.id+' a').attr('id','ident-'+d.id);
                        simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=flextemp.inc&id='+d.id+'&cmd=ax_edittpl');
    				})
    				.fail(function (d,error) {
    				    alert('create err '+error);
    					data.instance.refresh();
    				});
            });
    
    <% if ($GET.doopentree==1)%>
        $("#flextpls_treeul").jstree("open_node", "#flextpls_treeroot");
    <%/if%>
    </script>
<%/if%>
