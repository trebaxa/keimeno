<% if ($PERM.core_acc_usertemplates==true && count($usertpls_list)>0) %>
    <div id="usertpls_treeul">
    <ul>
    <li id="usertpls_treeroot" data-haschildren="1" ><a id="ident-0" href="javascript:void(0)"  data-tid="0">User Templates</a>
        <ul>
    <% function name="usertpls_treevar" %>
        <%foreach from=$items item=element%>     
                <li id="usertpls_treenode-<%$element.id%>" <% if ($element.haschildren==0) %>data-tid="<%$element.id%>"<%/if%> data-haschildren="<% if ($element.haschildren==1) %>1<%else%>0<%/if%>" data-modid="<%$element.modident%>" data-isadmin="<%$element.admin%>" <% if ($element.haschildren==0) %>data-jstree='{"icon":"glyphicon glyphicon-file"}'<%/if%> >
                <a id="ident-<%$element.id%>"  
                 data-tid="<%$element.id%>" data-modid="<%$element.modident%>"
                href="javascript:void(0)" title="<%$element.tpl_description|sthsc%>"
                ><% if ($element.haschildren==1) %><%$element.tpl_name|st%><%else%><%$element.tpl_name|st%><%/if%>    
                </a>
                
                <%if !empty($element.children)%>
                    <ul><%call name="usertpls_treevar" items=$element.children%></ul>
                <%/if%>
                
                </li>
     
        <%/foreach%>
    <%/function%><% call name="usertpls_treevar" items=$usertpls_list %>
            </ul>
        </li>
     </ul> 
    </div>
    
    <script>

      function customMenu(node) {
       
            var items = {
                createItem: { 
                    label: "Neues User Template",
                    action: function () {
                        var ref = $('#usertpls_treeul').jstree(true),
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
                       var ref = $('#usertpls_treeul').jstree(true),
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
                       var ref = $('#usertpls_treeul').jstree(true),
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
        $('#usertpls_treeul').jstree({  
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
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=tplvars.inc&id='+$('#'+link_id).data('tid')+'&cmd=ax_edittpl');
                    return;
               } 
               if ($('#'+link_id).data('haschildren')==1) {
                 if ($("#"+link_id).hasClass("jstree-open")) {
                        $("#usertpls_treeul").jstree("close_node", "#" + link_id);
                       }
                    else {                    
                        $("#usertpls_treeul").jstree("close_all");
                        $("#usertpls_treeul").jstree("open_node", "#usertpls_treeroot");
                        $("#usertpls_treeul").jstree("open_node", "#" + link_id);
                        simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=tplvars.inc&cmd=ax_start&section=start');
                    }    
               }
            }).on('rename_node.jstree', function (e, data) {  
                $('#usertpls_title').val(data.text);
                $.ajax({
    			     url: 'run.php?epage=tplvars.inc&cmd=rename_usertpls',
                     data: { 'id' : $('#'+data.node.id).data('tid'), 'FORM[tpl_name]' : data.text },
                     async :true,
                     dataType :'json'
                     })
                    .done(function (result) {
                       simple_load('orga-usertemplates','<%$PATH_CMS%>admin/run.php?epage=tplvars.inc&cmd=load_tpl_tree&doopentree=1');
                       reloadtpls();
    				})
                    .fail(function () {
                        alert('ren err');
    					data.instance.refresh();
    				});
            }).on('delete_node.jstree', function (e, data) {
                $.getJSON( "/admin/run.php?epage=tplvars.inc&id="+$('#'+data.node.id).data('tid')+"&cmd=axdelusertplsbytree", function( data ) {				
    					if (data.msge != "") {
                               $('#savedresult').html(data.msge);
                               $('#savedresult').removeClass('okbox').addClass('faultboxajax');
                               show_saved_msg(3000);
                            } else {
                               reloadtpls();
                            }                    
    				});
                    
            }).on('create_node.jstree', function (e, data) {             
                $.ajax({
    			     url: '<%$PATH_CMS%>admin/run.php?epage=tplvars.inc&cmd=ax_create_usertpl',
                     data: { 'id' : data.node.parent, 'position' : data.position, 'FORM[tpl_name]' : data.node.text },
                     async :false,
                     dataType :'json'
                     })
    				.done(function (d) {
    					data.instance.set_id(data.node, 'usertpls_treenode-'+d.id);
                        $('#usertpls_treenode-'+d.id).attr('data-haschildren','0');
                        $('#usertpls_treenode-'+d.id).attr('data-tid',d.id);
                        $('#usertpls_treenode-'+d.id+' a').attr('id','ident-'+d.id);
                        simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=tplvars.inc&id='+d.id+'&cmd=ax_edittpl');
    				})
    				.fail(function (d,error) {
    				    alert('create err'+error);
    					data.instance.refresh();
    				});
            });
    
    <% if ($GET.doopentree==1)%>
        $("#usertpls_treeul").jstree("open_node", "#usertpls_treeroot");
    <%/if%>
    </script>
<%/if%>
