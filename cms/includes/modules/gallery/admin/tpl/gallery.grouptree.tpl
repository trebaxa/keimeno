<% include file="cb.panel.header.tpl" title="Alben"%>
<div class="btn-group">
    <a class="btn btn-primary btn-sm" href="javascript:void(0)" data-toggle="modal" data-target="#addalbum"><i class="fa fa-plus"></i> {LBLA_NEW_SETUP}</a>
</div>
<div id="gallerytreecont">
<ul>
<li id="gallerytreeroot" data-haschildren="1" data-tid="0"  ><a id="ident-0" href="javascript:void(0)" data-tid="0">Alben</a>
    <ul>
<% function name="gal_tree" %>
   <%foreach from=$items item=element%>
   <li id="gallerynode-<%$element.id%>" data-haschildren="<% if (!empty($element.children)) %>1<%else%>0<%/if%>" data-tid="<%$element.id%>" data-isadmin="<%$element.admin%>" <% if (empty($element.children)) %>data-jstree='{"icon":"glyphicon glyphicon-file"}'<%/if%>>  
   
    <a style="color:#000000!important" href="#" id="ident-<%$element.id%>" title="<%$element.catlabel|hsc%>"><%$element.groupname%>(<%$element.piccount%>)
   </a>
   
   <%if !empty($element.children)%>   
    <ul><%call name="gal_tree" items=$element.children%></ul>
   <%/if%>
   </li>
   <%/foreach%>
<%/function%><% call name=gal_tree items=$GALADMIN.tree %>
        </ul>
    </li>
 </ul> 
</div>
 <% include file="cb.panel.footer.tpl"%>
<style>
#gallerytreecont a{
    color:#000000!important
}
</style>

<script>
  function customMenuGallery(node) {
        var items = {
            createItem: { // The "create" menu item
                label: "{LA_NEUESALBUM}",
                action: function () {
                    var ref = $('#gallerytreecont').jstree(true),
								sel = ref.get_selected();
							if(!sel.length) { return false; }
							sel = sel[0];
							var retVal = prompt("Title: ", "your name here");
                            sel = ref.create_node(sel, {"type":"file","text":retVal});
							
                }
            },
            renameItem: { // The "rename" menu item
                label: "{LA_UMBENNEN}",
                action: function () {
                   var ref = $('#gallerytreecont').jstree(true),
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
                   var ref = $('#gallerytreecont').jstree(true),
								sel = ref.get_selected();
							if(!sel.length) { return false; }
							ref.delete_node(sel);
                        }    
                }
            },
            editItem: { // The "edit" menu item
                label: "{LBL_EDIT}",
                action: function () {
                    var ref = $('#gallerytreecont').jstree(true),	sel = ref.get_selected();
                    if(!sel.length) { return false; }      
                    window.location.href='run.php?epage=gallery.inc&cmd=edit_group&id='+$('#'+sel[0]).data('tid');  
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
        }

        return items;
    }
    var lastnode="";
    


    $('#gallerytreecont').jstree({  
         "plugins" : [ "contextmenu", "types"],
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
          "contextmenu": {items: customMenuGallery}
     
        }).bind("select_node.jstree", function(event,data) {  
           var link_id = data.node.id;
           if ($('#'+link_id).data('haschildren')==0 && $('#'+link_id).data('tid')>0) {                     
                simple_load('gallerygrouplist','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_group_table&gid='+$('#'+link_id).data('tid'));
                return;
           } else {
            expand_node_webtree( link_id,'gallerytreecont');
            simple_load('gallerygrouplist','<%$PHPSELF%>?epage=<%$epage%>&cmd=load_group_table&gid='+$('#'+link_id).data('tid'));
           }
          
        }).on('rename_node.jstree', function (e, data) {  
            $.ajax({
			     url: '?epage=gallery.inc&cmd=rename_gallery_by_node',
                 data: { 'id' : data.node.id, 'FORM[groupname]' : data.text },
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
			$.get('?epage=gallery.inc&cmd=delete_gallery_by_node', { 'id' : $('#'+data.node.id).data('tid') })
				.done(function () {
					$('#galleryrightcont').html('');
                    msg('{LBL_DELETED}',3000);
				})
                .fail(function () {
                    alert('del err');
					data.instance.refresh();
				});
        }).on('create_node.jstree', function (e, data) {   
            $.ajax({
			     url: '?epage=gallery.inc&cmd=add_group_tree',
                 data: { 'position' : data.position, 'FORM[groupname]' : data.node.text, 'FORM[parent]' : $('#'+data.node.parent).data('tid') },
                 async :false,
                 dataType :'json'
                 })
				.done(function (d) {
                    window.location.href='run.php?epage=gallery.inc&cmd=edit_group&id='+d.id;
				})
				.fail(function (d,error) {
				    alert('create err webtree');
					data.instance.refresh();
				});
        });

      

expand_node_webtree( 'gallerytreeroot','gallerytreecont');
</script>

