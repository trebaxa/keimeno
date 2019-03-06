 <div class="row">
    <div class="col-md-3" id="js-folder-tree" style="background-color:#2A3F54">

    </div> 
    <div class="col-md-9">
        <%include file="kreg.files.upload.tpl"%>
    </div>
 </div>
 
 <script>
 function reload_folder_tree(id) {
    if (id=="" || id===undefined) {
        id ='folder_treeroot';
    }
    simple_load('js-folder-tree','<%$eurl%>cmd=load_folder_tree&doopentree=1&kid=<%$GET.kid%>&dirid='+id); 
 }
 reload_folder_tree();
 </script>