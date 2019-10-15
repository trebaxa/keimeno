<ul class="sub-menu">
<% function name="websitetree" %>
    <%foreach from=$items item=element%>
            <li id="js-wbst-<%$element.id%>">                        
                <%if !empty($element.children)%>
                    <ul class="sub-sub-menu"><%call name="websitetree" items=$element.children%></ul>
                    <div class="sub-sub-link">
                        <a onclick="load_pages(<%$element.id%>);" href="javascript:void(0)" data-tid="0" class="menu-toggle"><%$element.description%></a>
                        <a class="ajax-link toggle-btn" href="<%$PATH_CMS%>admin/run.php?epage=websitemanager.inc&cmd=page_axedit&id=<%$element.id%>"><i class="fas fa-edit"></i></a>
                        <a onclick="load_pages(<%$element.id%>);" href="javascript:void(0)" data-tid="0" class="menu-toggle toggle-btn"><i class="fas fa-chevron-right"></i></a>
                    </div>  
                <%else%>
                    <a class="js-website-click" data-tid="<%$element.id%>" id="ident-<%$element.id%>" 
                data-link="run.php?epage=websitemanager.inc&toplevel=<%$GET.toplevel%>&cmd=edit&id=<%$element.id%>" data-haschildren="<% if (!empty($element.children)) %>1<%else%>0<%/if%>"
                data-isadmin="<%$element.admin%>" 
                href="#" title="<%$element.description|hsc%>"
                ><%$element.description%></a>
                <%/if%>
            </li>
    <%/foreach%>
    <%/function%><% call name="websitetree" items=$WEBSITE.websitetree %>
</ul>
 

<script>
$( ".js-website-click" ).unbind('click');
$( ".js-website-click" ).click(function(e) {    
   if ($(this).data('haschildren')==0 && $(this).data('tid')>0) {
        simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=websitemanager.inc&cmd=page_axedit&id='+$(this).data('tid'));
        scrollToAnchor('admincontent');
   }
});  

init_tree_toggle();

<% if ($GET.id>0) %>
     $('#js-wbst-<%$GET.id%>').parents('ul').show();      
<%/if%>
      
</script> 

<%*

<script>
  $('#adminmenu ul').show();
  function customMenu(node) {
        var items = {
            loadMainFrame: {
                label: "Mainframe",
                action: function () {
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=websitemanager.inc&toplevel=1&cmd=page_axedit&id=1');
                }
            },
            createItem: { // The "create" menu item
                label: "{LA_NEUESTEMPLATE}",
                action: function () {
                    var ref = $('#adminmenu').jstree(true),
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
                   var ref = $('#adminmenu').jstree(true),
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
                   var ref = $('#adminmenu').jstree(true),
							sel = ref.get_selected();
							if(!sel.length) { return false; }
							ref.delete_node(sel);
                        }
                }
            },
            tableItem: {
                label: "Unterseiten",
                action: function () {
                  var ref = $('#adminmenu').jstree(true),
							sel = ref.get_selected();
							if(!sel.length) { return false; }
                  load_pages($('#'+sel[0]).data('tid'));
                }
            },
            editItem: { // The "edit" menu item
                label: "{LBL_EDIT}",
                action: function () {
                    var ref = $('#adminmenu').jstree(true),	sel = ref.get_selected();
                    if(!sel.length) { return false; }
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=websitemanager.inc&cmd=page_axedit&id='+$('#'+sel[0]).data('tid'));
                }
            }
        };
        if (node.children.length>0) {
            delete items.deleteItem;
            delete items.renameItem;
        }
        else{
            delete items.editItem;
            delete items.tableItem;
        }
        if (node.data.isadmin==1) {
            delete items.deleteItem;
        }
        if (node.data.tid>0) {
            delete items.loadMainFrame;
        }

        return items;
    }
    var lastnode="";



    $('#adminmenu').jstree({
         "plugins" : [ "contextmenu", "types", "wholerow",  "dnd" ],
         core : {
            "animation" : 1,
            "check_callback" : true,
            'themes': {
                'name': 'default',
                'responsive': true
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
          "contextmenu": {items: customMenu},


        }).bind("select_node.jstree", function(event,data) {
           var link_id = data.node.id;
           if ($('#'+link_id).data('haschildren')==0 && $('#'+link_id).data('tid')>0) {
                simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=websitemanager.inc&cmd=page_axedit&id='+$('#'+link_id).data('tid'));
                return;
           }
           var cmd = '<%$GET.urlcmd%>';
           var urltid = '<%$GET.id%>';
           var tid = $('#'+link_id).data('tid');
           if ($('#'+link_id).data('haschildren')==1 || cmd!='edit' || (urltid!=tid && cmd=='edit')) {
              $("#adminmenu").jstree("open_node", "#webtreeroot");
              expand_node_webtree( link_id,'adminmenu');
              if ($('#'+link_id).data('tid')>0) {
                simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=websitemanager.inc&cmd=page_axedit&id='+$('#'+link_id).data('tid'));
               } else {
                load_pages($('#'+link_id).data('tid'));
              }
           }
        }).on("after_open.jstree", function (event, data) {
            var ref = $('#adminmenu').jstree(true),	sel = ref.get_selected();
			     if(!sel.length) { return false; }
            if ($('#'+sel[0]).data('haschildren')==0 && $('#'+sel[0]).data('tid')>0) {
                lastnode = sel[0];
            }
        }).on("ready.jstree", function (event, data) {
            $("#adminmenu").jstree("open_node", "#webtreeroot");
           <% if ($GET.id>0) %>
            expand_node_webtree('pagenode-<%$GET.id%>','adminmenu');
            $("#adminmenu").jstree("select_node", 'pagenode-<%$GET.id%>');
           <%/if%>
        }).on('rename_node.jstree', function (e, data) {
            $('#wf-desc').val(data.text);
            $.ajax({
			     url: '<%$PATH_CMS%>admin/run.php?epage=websitemanager.inc&cmd=rename_webpage_by_node',
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
			$.get('<%$PATH_CMS%>admin/run.php?epage=websitemanager.inc&cmd=delete_webpage_by_node', { 'id' : $('#'+data.node.id).data('tid') })
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
			     url: '<%$PATH_CMS%>admin/run.php?epage=websitemanager.inc&cmd=add_website_tree',
                 data: { 'position' : data.position, 'FORM[description]' : data.node.text, 'FORM[parent]' : $('#'+data.node.parent).data('tid') },
                 async :false,
                 dataType :'json'
                 })
				.done(function (d) {
                    simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=websitemanager.inc&cmd=page_axedit&id='+d.id);
                    simple_load('websitetree','<%$PATH_CMS%>admin/run.php?epage=websitemanager.inc&cmd=load_website_tree&id='+d.id);
                    data.instance.refresh();
				})
				.fail(function (d,error) {
				    alert('create err webtree');
					data.instance.refresh();
            })
        }).on('move_node.jstree', function (e, data) {
            var moveitemID = data.node.id.replace("pagenode-", "");
            var newParentID = $('#' + data.parent).find('a')[0].id;
            var oldParentID = $('#' + data.old_parent).find('a')[0].id;
            var ref = $('#adminmenu').jstree(true);
            var fnode = ref.get_node('#pagenode-'+moveitemID);
            var next_node_id = parseInt($(ref.get_next_dom(fnode).context).data('tid'));
            var prev_node_id = parseInt($(ref.get_prev_dom(fnode).context).data('tid'));
            var url = '<%$PATH_CMS%>admin/run.php?epage=websitemanager.inc&cmd=ax_sort_tree&next_node_id='+next_node_id+'&prev_node_id='+prev_node_id+'&tid='+moveitemID+'&parent='+$('#'+newParentID).data('tid');
            execrequest(url);
        })





    function load_pages(starttree) {
        simple_load('admincontent','<%$PATH_CMS%>admin/run.php?epage=<%$epage%>&starttree=' +starttree+'&cmd=load_pages');
    }

</script>
*%>