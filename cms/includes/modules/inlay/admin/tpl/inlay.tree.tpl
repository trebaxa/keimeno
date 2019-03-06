<% if ($PERM.core_acc_inlay==true) %>
<div id="inlaytreeul">
<ul>
<li id="inlaytreeroot" data-haschildren="1" ><a id="ident-0" href="javascript:void(0)"  data-tid="0">Inlays</a>
    <ul>
<% function name="inlaytreevar" %>
    <%foreach from=$items item=element%>     
            <li id="inlaytreenode-<%$element.id%>" <% if ($element.haschildren==0) %>data-tid="<%$element.id%>"<%/if%> data-haschildren="<% if ($element.haschildren==1) %>1<%else%>0<%/if%>" data-modid="<%$element.modident%>" data-isadmin="<%$element.admin%>" <% if ($element.haschildren==0) %>data-jstree='{"icon":"glyphicon glyphicon-file"}'<%/if%> >
            <a id="ident-<%$element.id%>"  
             data-tid="<%$element.id%>" data-modid="<%$element.modident%>"
            href="javascript:void(0)" title="<%$element.description|sthsc%>"
            ><% if ($element.haschildren==1) %><%$element.module_name|st%><%else%><%$element.description|st%><%/if%>    
            </a>
            
            <%if !empty($element.children)%>
                <ul><%call name="inlaytreevar" items=$element.children%></ul>
            <%/if%>
            
            </li>
 
    <%/foreach%>
<%/function%><% call name="inlaytreevar" items=$inlay_list %>
        </ul>
    </li>
 </ul> 
</div>

<script>

  function customMenu(node) {    
        var items = {
            createItem: { // The "create" menu item
                label: "Neues Inlay",
                action: function () {
                    var ref = $('#inlaytreeul').jstree(true),
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
                   var ref = $('#inlaytreeul').jstree(true),
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
                   var ref = $('#inlaytreeul').jstree(true),
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
    $('#inlaytreeul').jstree({  
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
                simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=inlayadmin.inc&id='+$('#'+link_id).data('tid')+'&cmd=ax_edit');
                return;
           } 
           if ($('#'+link_id).data('haschildren')==1) {
             if ($("#"+link_id).hasClass("jstree-open")) {
                    $("#inlaytreeul").jstree("close_node", "#" + link_id);
                   }
                else {                    
                    $("#inlaytreeul").jstree("close_all");
                    $("#inlaytreeul").jstree("open_node", "#inlaytreeroot");
                    $("#inlaytreeul").jstree("open_node", "#" + link_id);
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=inlayadmin.inc&cmd=ax_show_all');
                }    
           }
        }).on('rename_node.jstree', function (e, data) {  
            $('#inlaytitle').val(data.text);
            $.ajax({
			     url: 'run.php?epage=inlayadmin.inc&cmd=rename_inlay',
                 data: { 'id' : $('#'+data.node.id).data('tid'), 'FORM[description]' : data.text },
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
            $.getJSON( "run.php?epage=inlayadmin.inc&ident="+$('#'+data.node.id).data('tid')+"&cmd=axdelinlay", function( data ) {				
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
			     url: 'run.php?epage=inlayadmin.inc&cmd=ax_create_inlay',
                 data: { 'id' : data.node.parent, 'position' : data.position, 'FORM[description]' : data.node.text },
                 async :false,
                 dataType :'json'
                 })
				.done(function (d) {
					data.instance.set_id(data.node, 'inlaytreenode-'+d.id);
                    $('#inlaytreenode-'+d.id).attr('data-haschildren','0');
                    $('#inlaytreenode-'+d.id).attr('data-tid',d.id);
                    $('#inlaytreenode-'+d.id+' a').attr('id','ident-'+d.id);
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=inlayadmin.inc&id='+d.id+'&cmd=ax_edit');
                    data.instance.refresh();
                    $("#inlaytreeul").jstree("open_node", "#inlaytreeroot");
				})
				.fail(function (d,error) {
				    alert('create err'+error);
					data.instance.refresh();
				});
        });

      
</script>
<%/if%>