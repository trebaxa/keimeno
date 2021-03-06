<ul class="sub-sub-menu">
    <% function name="gbltpltree" %>
        <%foreach from=$items item=element%>     
            <li>
                <%if !empty($element.children)%>
                    <ul class="sub-sub-menu"><%call name="gbltpltree" items=$element.children%></ul>
                    <div class="sub-sub-link">
                            <a onclick="simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gbltemplates.inc&cmd=load_start&modident=<%$element.modident%>');" href="javascript:void(0)" data-tid="<%$element.id%>" class="menu-toggle"><%$element.module_name|sthsc%></a>
                            <a onclick="simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gbltemplates.inc&cmd=load_start&modident=<%$element.modident%>');" href="javascript:void(0)" data-tid="<%$element.id%>" class="menu-toggle toggle-btn"><i class="fas fa-chevron-right"></i></a>
                     </div>
                <%else%>
                    <a class="js-gbltpl-click" data-isadmin="<%$element.admin%>" data-modid="<%$element.modident%>" data-haschildren="<% if ($element.haschildren==1) %>1<%else%>0<%/if%>" id="ident-<%$element.id%>" data-tid="<%$element.id%>"  <% if ($element.haschildren==0) %> data-modid="<%$element.modident%>"<%else%> data-modid="<%$element.id%>"<%/if%> href="javascript:void(0)" title="<%$element.description|sthsc%>"><%$element.description|st%></a>
                <%/if%>
            </li>
        <%/foreach%>
    <%/function%>
    <% call name="gbltpltree" items=$GBLTPL.gbltpltree %>    
</ul>
<div class="sub-sub-link">
    <a onclick="simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gbltemplates.inc&cmd=load_start&modident=');" href="javascript:void(0)" data-tid="0" class="menu-toggle">System Templates</a>
    <a onclick="simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gbltemplates.inc&cmd=load_start&modident=');" href="javascript:void(0)" data-tid="0" class="menu-toggle toggle-btn"><i class="fas fa-chevron-right"></i></a>
</div>

<script>
$( ".js-gbltpl-click" ).unbind('click');
$( ".js-gbltpl-click" ).click(function(e) {       
       if ($(this).data('haschildren')==0 && $(this).data('tid')>0) {
            simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gbltemplates.inc&id='+$(this).data('tid')+'&uselang=1&cmd=load_gbltpl_ax');
            scrollToAnchor('admincontent');
            return;
       } 
       if ($(this).data('tid')==0 || $(this).data('haschildren')==1) {
            simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gbltemplates.inc&cmd=load_start&modident='+$(this).data('modid'));
            scrollToAnchor('admincontent');
       }
});  

init_tree_toggle();
  
</script>   

<%*
<% if ($PERM.core_acc_systpl==1) %>
    
        <ul class="knone">
            <li id="gbltreeroot" data-haschildren="1" data-tid="0">
                <a id="ident-0" href="javascript:void(0)" data-tid="0">System Templates</a>
                <ul class="knone">
                    <% function name="gbltpltree" %>
                    <%foreach from=$items item=element%>     
                        <li id="gbltreenode-<%$element.id%>" <% if ($element.haschildren==0) %>data-tid="<%$element.id%>" data-modid="<%$element.modident%>"<%else%> data-modid="<%$element.id%>" <%/if%> data-haschildren="<% if ($element.haschildren==1) %>1<%else%>0<%/if%>"  data-isadmin="<%$element.admin%>" <% if ($element.haschildren==0) %>data-jstree='{"icon":"far fa-file-alt"}'<%/if%> >
                            <a id="ident-<%$element.id%>" data-tid="<%$element.id%>"  <% if ($element.haschildren==0) %> data-modid="<%$element.modident%>"<%else%> data-modid="<%$element.id%>"<%/if%> href="javascript:void(0)" title="<%$element.description|sthsc%>"><% if ($element.haschildren==1) %><%$element.module_name|st%><%else%><%$element.description|st%><%/if%></a>
                            <%if !empty($element.children)%>
                                <ul><%call name="gbltpltree" items=$element.children%></ul>
                            <%/if%>
                        </li>
                    <%/foreach%>
                    <%/function%>
                    <% call name="gbltpltree" items=$GBLTPL.gbltpltree %>
                    
                </ul>
            </li>           
        </ul> 
    

    <script>

  function customMenuGbl(node) {
  
        var items = {
            createItem: { // The "create" menu item
                label: "Neues Template",
                action: function () {
                    var ref = $('#gbltpltreeul').jstree(true),
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
                   var ref = $('#gbltpltreeul').jstree(true),
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
                   var ref = $('#gbltpltreeul').jstree(true),
                                sel = ref.get_selected();
                            if(!sel.length) { return false; }
                            ref.delete_node(sel);
                        }    
                }
            }
        };
        
        if (node.children.length>=0) {
            delete items.deleteItem;
            delete items.renameItem;
            
        }
        else{
           
        }
        if (node.data.isadmin==1) {
            delete items.deleteItem;
        }

        return items;
    }
    // ,"dnd", "search",    "state", "types", "wholerow"
    $('#gbltpltreeul').jstree({  
         "plugins" : [  "types", "wholerow" ],
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
         /* "contextmenu": {items: customMenuGbl} */
     
        }).bind("select_node.jstree", function(event,data) {  
           var link_id = data.node.id;
           if ($('#'+link_id).data('haschildren')==0 && $('#'+link_id).data('tid')>0) {
                simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gbltemplates.inc&id='+$('#'+link_id).data('tid')+'&uselang=1&cmd=load_gbltpl_ax');
                return;
           } 
           if ($('#'+link_id).data('tid')==0 || $('#'+link_id).data('haschildren')==1) {
                simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gbltemplates.inc&cmd=load_start&modident='+$('#'+link_id).data('modid'));
           }            
           if ($('#'+link_id).data('haschildren')==1) {
             if ($("#"+link_id).parent().hasClass("jstree-open")) {
                   // $("#gbltpltreeul").jstree("close_node", "#" + link_id);
                   }
                else {
                    $("#gbltpltreeul").jstree("close_all");
                    $("#gbltpltreeul").jstree("open_node", "#gbltreeroot");
                    $("#gbltpltreeul").jstree("open_node", "#" + link_id);
                }    
           }
        }).on('rename_node.jstree', function (e, data) {  
            $('#gbltreetpltitle').val(data.text);
            $.ajax({
                 url: 'run.php?epage=gbltemplates.inc&cmd=rename_gbltpl',
                 data: { 'id' : data.node.id, 'FORM[description]' : data.text },
                 async :true,
                 dataType :'json'
                 })
                .done(function (result) {
                   simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gbltemplates.inc&id='+$('#'+data.node.id).data('tid')+'&uselang=1&cmd=load_gbltpl_ax');
                })
                .fail(function (result) {
                    alert('ren err'+result);
                    data.instance.refresh();
                });
        }).on('delete_node.jstree', function (e, data) {
            $.get('run.php?epage=gbltemplates.inc&cmd=delete_gbltpl', { 'id' : $('#'+data.node.id).data('tid') })
                .done(function () {
                    $('#admincontent').html('');
                    msg('{LBL_DELETED}',3000);
                })
                .fail(function () {
                    alert('del err');
                    data.instance.refresh();
                });
        }).on('create_node.jstree', function (e, data) {             
            $.ajax({
                 url: 'run.php?epage=gbltemplates.inc&cmd=create_gbltpl',
                 data: { 'id' : data.node.parent, 'position' : data.position, 'FORM[description]' : data.node.text },
                 async :false,
                 dataType :'json'
                 })
                .done(function (d) {
                    data.instance.set_id(data.node, 'gbltreenode-'+d.id);
                    var modid=d.modid;
                    $('#gbltreenode-'+d.id).attr('data-modid',modid);
                    $('#gbltreenode-'+d.id).attr('data-haschildren','0');
                    $('#gbltreenode-'+d.id).attr('data-tid',d.id);
                    $('#gbltreenode-'+d.id+' a').attr('id','ident-'+d.id);
                    $('#gbltreenode-'+d.id+' a').attr('data-modid',modid);                                        
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=gbltemplates.inc&id='+d.id+'&uselang=1&cmd=load_gbltpl_ax');
                })
                .fail(function (d,error) {
                    alert('create err: '+error );
                    // console.log(d.getResponse.toSource());
                    data.instance.refresh();
                });
        });

</script>
<%/if%>

*%>