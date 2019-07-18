function init_doc_tree() {
    $('#doctree').jstree({  
             "plugins" : [ "contextmenu", "types", "wholerow" ],
             core : {
                "animation" : 0,
                "check_callback" : true,
             },
              "types" : {
                        "#" : {
                        "max_children" : 1, 
                        "max_depth" : 5, 
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
                var myDropzone = Dropzone.forElement("#js-customer-dropzone");
                myDropzone.options.url='<%$eurl%>cmd=dragdropfile_user&kid=<%$GET.kid%>&folder='+$("#"+link_id).data('hash');
                current_folder=$("#"+link_id).data('hash');
                var url = '<%$eurl%>cmd=reload_customer_files&kid=<%$GET.kid%>&rnd='+Math.random()+'&folder='+$("#"+link_id).data('hash');
                simple_load('js-customer-files',url);
                $('#js-customer-dropzone').show();                  
            }).on('rename_node.jstree', function (e, data) {  
                $('#resrc_title').val(data.text);
                $.ajax({
    			     url: '<%$eurl%>cmd=rename_dir&kid=<%$GET.kid%>',
                     data: { 'dir' : $('#'+data.node.id).data('hash'), 'FORM[dir]' : data.text, 'rnd':Math.random() },
                     async :true,
                     dataType :'json'
                     })
                    .done(function (result) {
                       document.getElementById(data.node.id).dataset.hash = result.hash;
                       current_folder=result.hash;
                       simple_load('js-customer-files','<%$eurl%>cmd=reload_customer_files&kid=<%$GET.kid%>inittree=1&folder='+result.hash);
                       reload_folder_tree('resrc_treenode-'+result.id);                                            
    				})
                    .fail(function () {
                        alert('ren err');
    					data.instance.refresh();
    				});
            }).on('delete_node.jstree', function (e, data) {                
                $.getJSON( "<%$eurl%>folder="+$('#'+data.node.id).data('hash')+"&kid=<%$GET.kid%>&cmd=del_folder", function( data ) {				
    					if (data.msge != "") {
                               $('#savedresult').html(data.msge);
                               $('#savedresult').removeClass('okbox').addClass('faultboxajax');
                               show_saved_msg(3000);
                            } else {
                                current_folder=result.hash;
                              $('#js-customer-files').html('');
                              $('#js-customer-dropzone').hide();                              
                            }                    
    				});   
            }).on('create_node.jstree', function (e, data) {             
                data.node.text='- neues Verzeichnis';                                
                $.ajax({
    			     url: '<%$eurl%>cmd=add_folder&kid=<%$GET.kid%>',
                     data: { 'id' : data.node.parent, 'position' : data.position, 'FORM[dir]' : data.node.text, 'FORM[parent]' : $('#'+data.node.parent).data('hash') },
                     async :false,
                     dataType :'json'
                     })
    				.done(function (d) {
                        reload_folder_tree();                       
    				})
    				.fail(function (d,error) {
    				    alert('create err '+error);
    					data.instance.refresh();
    				});
            });
}            