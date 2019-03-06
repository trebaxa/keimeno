<% if ($section=='showtools') %>
    <div class="page-header"><h1>System {LBL_FILES} & Aufgaben</h1></div>
    
    <h3>System {LBL_TASKS}</h3>
    <form action="<%$eurl%>" class="form-inline jsonform" method="post" enctype="multipart/form-data">
    	<label for="toolcmd">Aufgabe:</label>
        <select id="toolcmd" class="form-control" name="cmd">
    		<option value="cleancache" <% if ($POST.task=="cleancache") %>selected<%/if%>>{LBLA_CLEANPICCACHE}</option>		
            <option value="clearsmartycache" <% if ($POST.task=="clearsmartycache") %>selected<%/if%>>SMARTY Cache leeren</option>
    		<option value="instlangadmin" <% if ($POST.task=="instlangadmin") %>selected<%/if%>>{LBLA_INSTALLADMINLANG}</option>
            <option value="rewritetpls" <% if ($POST.task=="rewritetpls") %>selected<%/if%>>Templates neu erzeugen</option>            		
    	</select>
        <%$execbtn%>
    </form>
    
    <h3>robots.txt anpassen</h3>
      <div class="text-warning">Dies sind Experten Einstellungen!</div>
      <form action="<%$PHPSELF%>" method="POST" class="jsonform form-inline">
         <textarea class="form-control se-html" name="FORM[robots]" rows="20" cols="130"><%$CMSUPDT.robot|hsc%></textarea>
         <br><%$subbtn%>
         <input type="hidden" name="cmd" value="save_robot">
         <input type="hidden" name="epage" value="<%$epage%>">
     </form>
<%/if%>

<% if ($section=='history') %>
    <div class="page-header"><h1>Keimeno - Version Build Table:</h1></div>
    <%$CMSUPDT.history%>
<%/if%>



<% if ($section=='backup' ) %>
    <div class="page-header"><h1>Backups</h1></div>
        <div class="btn-group mb-lg">
            <a class="btn btn-default json-link" data-spinner="1" href="<%$eurl%>cmd=createbackup">Create DB backup</a>
            <a class="btn btn-default json-link" data-spinner="1" href="<%$eurl%>cmd=backuphomepage">Backup CMS</a>
        </div>
            
<% if (count($BACKUPCMS.files)>0) %>            
   <table class="table table-striped table-hover">
    <thead>
        <th>#</th>
        <th>File</th>
        <th>Size</th>
        <th>Date</th>
        <th></th>
        <th></th>
    </thead>
   	<% foreach from=$BACKUPCMS.files item=row %>
        <tbody>	 
            <tr>
			 <td><% $row.num%></td>
			 <td><a href="./db_backup/<% $row.file%>"><% $row.file%></a></td>
			 <td class="text-right"><%$row.filesize%></td>
			 <td><%$row.date%></td>
			 <td><%$row.delicon%>          </td>
			 <td><% if ($row.ismysql) %>
                    <a onClick="return confirm('Are you sure?')" href="<%$eurl%>cmd=a_importback&file=<% $row.file%>">import</a>
                <%else%>
                    <a onClick="return confirm('Are you sure?')" href="<%$eurl%>cmd=importcmsbackup&file=<% $row.file%>">import</a>
                <%/if%></td>
			</tr>
    <%/foreach%>
    </tbody>
   </table>
   <div class="alert alert-info mt-lg">Total used backupspace: <%$BACKUPCMS.usedspace%></div>
   <%else%>
    <div class="alert alert-info">No Backup founds.</div>
   <%/if%>                                                                                                                                                                                  
<%/if%>



<% if ($section=='update' || $section=='') %>
    <script src="./js/cmsupt.js"></script>
    <div class="page-header">
        <h1><i class="fa fa-database"></i> Keimeno Update</h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div id="updatecont" class="text-center-">
        
            <%include file="cb.panel.header.tpl" icon="fa-database" title="Remote Update"%>
                    <% if ($CMSUPDT.needupd==true) %>
                        <div class="alert alert-warning text-center">
                           Ihre CMS-Version: <%$CMSUPDT.local_version%> | Aktuelle CMS-Version: <b><%$CMSUPDT.version%></b>
                        </div>
                    <%else%>
                        <div class="alert alert-success text-success text-center">Ihre CMS-Version: <%$CMSUPDT.local_version%> | Aktuelle CMS-Version: <b><%$CMSUPDT.version%><br>{NO_UPDATE_NEEDED}</b></div>
                    <%/if%>
                    <div class="alert alert-info">
                    Folgendes habe ich bedacht:
                        <ul>
                            <li>Ich habe ein <a class="ajax-link" href="<%$eurl%>cmd=initbackup&section=backup&msid=3ce0710a22c657211d247bb741085419">Backup</a> gemacht</li>
                            <li>Ich bin ein Administrator</li>
                            <li>Ich habe die Datenbank gesichert</li>
                        </ul>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input id="rulecheck" type="checkbox" required="" value="1" name="yes">{LBL_UPD_CONFIRM}
                        </label>
                    </div>
                    <p class="alert alert-danger" id="uptfault" style="display:none">{ERR_TERMSNOTCONFIRMED}</p>
              
              <div class="panel-footer text-center">
                <button class="btn btn-primary" onclick="return start_update();">{LBLA_DOUPDATE}</button>
                <hr><a class="json-link" title="exceute update scripts" href="<%$eurl%>cmd=repair">or only exceute update scripts</a>
              </div>
            <%include file="cb.panel.footer.tpl"%>
        
        </div>
     


        <div style="display:none" id="tpltab">
            <div id="updprocess">
            <p id="updwarning" class="bg-danger text-danger">Bitte warten! Vorgang nicht abbrechen!</p><br><br><br>
            <table class="table table-striped table-hover"  >
                <tbody>
               <tr style="display:none">
                        <td width="26%">SQL Update</td>
                        <td><div id="tpl-sql_upt" data-cmd="sql_upt" class="js-tplcheck">-</div></td>
                    </tr>
                    <tr style="display:none">
                        <td width="26%">File Update</td>
                        <td><div id="tpl-file_upt" data-cmd="file_upt" class="js-tplcheck">-</div></td>
                    </tr>
                    <tr style="display:none">
                        <td width="26%">Core Update</td>
                        <td><div id="tpl-core_upt" data-cmd="core_upt" class="js-tplcheck">-</div></td>
                    </tr>
                    <tr style="display:none">
                        <td width="26%">Software Update</td>
                        <td><div id="tpl-release_updt" data-cmd="release_updt" class="js-tplcheck">-</div></td>
                    </tr>
                </tbody>
            </table>
            </div>
            <div class="alert alert-success" id="uptfinish" style="display:none">{LBL_UPDATEDONE}</div>
        </div>        
  </div> 
</div> 

<%/if%>