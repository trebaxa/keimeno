<div class="btn-group"><a class="btn btn-secondary" href="<% $PHPSELF %>?epage=<%$epage%>&cmd=sync">{LBL_SYNCDIR}</a></div>
<%include file="cb.panel.header.tpl" title=$doc_center.cms_path_to_current_directory%>
<div class="row">
    <div class="col-md-6">   
            <form action="<%$PHPSELF%>" class="form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="epage" value="<%$epage%>">
                    <div class="form-group">
                        <label>Titel:</label>
                        <input type="text" required="" class="form-control" name="FORM[title]" placeholder="Bezeichnung" value="" />    
                    </div>    
                    <%include file="cb.fileupload.tpl" name="datei" label="{LBL_FILE}"%>
                        <input class="btn btn-primary" type="submit" value="{LBL_UPLOADFILE}" />
                        <input type="hidden" name="cmd" value="a_fileupload" />
                        <input type="hidden" name="ftarget" value= "<% $doc_center.current_directory %>" />
                        <input type="hidden" name="msgback" value="Datei Upload" />
                    
                </form>
        </div>
        <div class="col-md-6">  
                    <form action="<% $PHPSELF %>" method="post">
                        <div class="form-group">
                            <label>{LBL_CREATEDIR}</label>
                            <div class="input-group">
                                <input required="" type="text" class="form-control"  value="" name="cdir" placeholder="Verzeichnis Name"/>
                                <div class="input-group-btn"><button class="btn btn-primary" type="submit">{LBL_CREATEDIR}</button></div>
                            </div>
                        </div>    
                        <input type="hidden" name="epage" value="<%$epage%>">
                        <input type="hidden" value="a_cdir" name="cmd">                        
                    </form>                
            </div>
</div>
<%include file="cb.panel.footer.tpl" text=$doc_center.cms_path_to_current_directory%>
    
<%include file="cb.panel.header.tpl" title="{LBL_DIRECTORY}"%>    
    <% if ($doc_center.all_dirs_and_files) %>    
        <form action="<% $PHPSELF %>" method="post" class="form-inline">
        <input type="hidden" name="epage" value="<%$epage%>">
    			<table class="table table-striped table-hover" id="doc-file-table">
                    <% if (count($doc_center.all_dirs_and_files.all_files)>0)%>
                    <thead>
                        <th></th>
                        <th>File</th>
                        <th>Perm.</th>
                        <th>Size</th>
                        <th>Date</th>
                        <th>Icon</th>
                        <th>#</th>
                    </thead>
                    <%/if%>
                    <tbody>
                    <% if $doc_center.froot_greater %>
                        <tr>   
                                <td colspan="7">
                                    <a class="btn btn-primary" title="{LBL_ONEDIRBACK}" href="<%$PHPSELF%>?cmd=onceup&epage=<%$epage%>"><i class="fa fa-folder-open-o"></i> ..</a>
                                </td>
                        </tr>
                    <% /if %>
                    
                    <% foreach from=$doc_center.all_dirs_and_files.all_dirs item=dir  %>
                                <tr>
                                    <td>
                                        <a class="btn btn-secondary" title="<% $dir.dirname %>" href="<% $PHPSELF %>?cmd=enter&dir=<% $dir.dirname %>&epage=<%$epage%>">
                                           <i class="fa fa-folder-open"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <b><a href="<% $PHPSELF %>?cmd=enter&dir=<% $dir.dirname %>&epage=<%$epage%>"><% $dir.dirname %></a></b>
                                    </td>
                                    <td>
                                        <% $dir.perms %>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right"><% $dir.del_icon %></td>
                                </tr> 
                   <%   /foreach %>  
                   
                   <% foreach from=$doc_center.all_dirs_and_files.all_files item=file  %>
                    <tr>
                        <td><input type="checkbox" value="<% $file.filename %>" name="files[<% $file.filename %>]"/></td>
                        <td><a href="<%$PHPSELF%>?epage=<%$epage%>&cmd=dc_down&id=<% $file.path|escape:urlencode %>"><% $file.filename %></a></td>
                        <td><% $file.permission %></td>
                        <td><% $file.file_size %></td>
                        <td><% $file.create_date %></td>
                        <td><% $file.picture %></td>
                        <td class="text-right"><% $file.del_icon %> </td> 
                    </tr>
                   <% /foreach%>  
                   </tbody>
            </table> 
            <% if (count($doc_center.all_dirs_and_files.all_files)>0 && count($doc_center.all_dirs_and_files.all_dirs)==0 && $doc_center.froot_greater==0) %>
                <%* Tabellen Sortierungs Script *%>
                <%assign var=tablesortid value="doc-file-table" scope="global"%>
                <%include file="table.sorting.script.tpl"%>
            <%/if%>
            <div class="form-group">
               <label>{LBL_MARKEDFILES}:</label>
               <div class="input-group">
                <select class="form-control custom-select" name="cmd"><option value="a_massdel">{LBL_DELETE}</option></select>
                <div class="input-group-btn"><button class="btn btn-primary" type="submit">GO</button></div>
               </div>
            </div>  
            
            </form>
    <%/if%> 
<%include file="cb.panel.footer.tpl" text=$doc_center.cms_path_to_current_directory%> 