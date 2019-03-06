<% if (count($GALADMIN.grouptable)>0) %>
<% include file="cb.panel.header.tpl" title="{LBL_FOTOALBUMS}"%>
<form action="<%$PHPSELF%>" method="post" class="jsonform table-responsive">		
			<table class="table table-striped table-hover" id="gallery-foto-table" >
				<thead><tr>
					<th>{LBL_OPTIONS}</th>
                    <th>admin. {LBL_TITLE}</th>
					<%*<th>Res.:</th>*%>					
					<th>Thumb.-Breite/H&ouml;he</th>					
				<%*	<th>Thumb. Type</th>                    
					<th>Sort. Bilder</th>*%>
                    <th>Sort.</th>
                    <th>Create Date</th>							
					
				</tr></thead>
				<% foreach from=$GALADMIN.grouptable item=row %>
				<tr>
                <td>
                    <div class="btn-group">
                        <%$row.icon_edit%>
                        <%$row.icon_del%>
                        <%$row.icon_approve%>
					    <a class="btn btn-default ajax-link" href="run.php?section=start&cmd=initpicman&epage=gallerypicmanager.inc&gid=<%$row.id%>" title="Bilder verwalten"><i class="glyphicon glyphicon-picture"><!----></i></a>
                    
                    </div>
				</td>
					<td>
					   <input name="FORM[<%$row.id%>][groupname]" type="text" class="form-control input-sm" value="<%$row.groupname|sthsc%>">
                    </td>
  				 <%* <td>                    
                    <div class="form-group">
                        <label class="sr-only">Breite</label>
					    <input name="FORM[<%$row.id%>][max_width]" placeholder="Breite" type="text" class="form-control input-sm" value="<%$row.max_width%>">x
                    </div>   
                    <div class="form-group">
                        <label class="sr-only">Breite</label>
                        <input name="FORM[<%$row.id%>][max_height]" placeholder="Höhe" type="text" class="form-control input-sm" value="<%$row.max_height%>">
                    </div>    
                    </td>
                    *%>					
					<td>
                    <div class="form-inline">
                     <div class="form-group">
                        <label class="sr-only">Breite</label>
    					<input name="FORM[<%$row.id%>][thumb_width]" type="text" class="form-control" value="<%$row.thumb_width%>">
                     </div>
                     <i class="fa fa-times"></i>
                      <div class="form-group">
                        <label class="sr-only">Height</label>   
                        <input name="FORM[<%$row.id%>][thumb_height]" type="text" class="form-control" value="<%$row.thumb_height%>">
                      </div>  
                     </div>   
                    </td>
				<%*	<td>
					<select class="form-control input-sm"  name="FORM[<%$row.id%>][thumb_type]">
    					<option value="resize" <% if ($row.thumb_type=='resize') %>selected<%/if%> >{LBL_GAL_RESIZE}</option>
                        <option value="resizetofit" <% if ($row.thumb_type=='resizetofit') %>selected<%/if%> >{LBL_GAL_RESIZE} (fit)</option>
                        <option value="resizetofitpng" <% if ($row.thumb_type=='resizetofitpng') %>selected<%/if%> >{LBL_GAL_RESIZE} (fit PNG)</option>                    
    					<option value="rounded" <% if ($row.thumb_type=='rounded') %>selected<%/if%>>{LBL_GAL_ROUNDCORNER}</option>
    					<option value="crop" <% if ($row.thumb_type=='crop') %>selected<%/if%>>{LBL_GAL_CROP}</option>										
                        <option value="boxed" <% if ($row.thumb_type=='boxed') %>selected<%/if%>>Boxed</option>
					</select>
                    <select class="form-control input-sm"  name="FORM[<%$row.id%>][g_croppos]">
                        <option <% if ($row.g_croppos=='NorthWest') %>selected<%/if%> value="NorthWest">NorthWest</option>
                        <option <% if ($row.g_croppos=='North') %>selected<%/if%> value="North">North</option>
                        <option <% if ($row.g_croppos=='NorthEast') %>selected<%/if%> value="NorthEast">NorthEast</option>
                        <option <% if ($row.g_croppos=='West') %>selected<%/if%> value="West">West</option>
                        <option <% if ($row.g_croppos=='Center') %>selected<%/if%> value="Center">Center</option>
                        <option <% if ($row.g_croppos=='East') %>selected<%/if%> value="East">East</option>
                        <option <% if ($row.g_croppos=='SouthWest') %>selected<%/if%> value="SouthWest">SouthWest</option>
                        <option <% if ($row.g_croppos=='South') %>selected<%/if%> value="South">South</option>
                        <option <% if ($row.g_croppos=='SouthEast') %>selected<%/if%> value="SouthEast">SouthEast</option>
                      </select>
					</td>	
                   	
				<td>
					<select class="form-control"  name="FORM[<%$row.id%>][default_order]">
    					<option value="post_time_int" <% if ($row.default_order=='post_time_int') %>selected<%/if%> >Upload date</option>
    					<option value="pic_title" <% if ($row.default_order=='pic_title') %>selected<%/if%>>{LBL_TITLE}</option>
    					<option value="morder" <% if ($row.default_order=='morder') %>selected<%/if%>>manuelle Sortierung</option>										
    				</select>
                    <select class="form-control"  name="FORM[<%$row.id%>][default_direc]">
    					<option value="ASC" <% if ($row.default_direc=='ASC') %>selected<%/if%> >aufsteigend</option>
    					<option value="DESC" <% if ($row.default_direc=='DESC') %>selected<%/if%>>absteigend</option>
					</select>
				</td>
				 *%>													
					
                    <td>
                        <input name="ORDER[<%$row.id%>][g_order]" type="text" class="form-control" value="<%$row.g_order|sthsc%>">
                        <input name="ORDER[<%$row.id%>][id]" type="hidden" class="form-control" value="<%$row.id|sthsc%>">
                    </td>
					<td><input type="hidden" value="<%$row.GID%>" name="galids[]"><input type="text" class="form-control" name="FORM[<%$row.id%>][g_createdate]" value="<%$row.g_createdate%>" size="10" maxlength="10"></td>
					
					<%/foreach%>
				</tr>
			</table>
            
           
			<%$subbtn%>
			<input type="hidden" value="save_groups" name="cmd">
			<input type="hidden" value="<%$epage%>" name="epage">
			<input type="hidden" value="<%$GET.gid%>" name="gid">
		</form>
         <% include file="cb.panel.footer.tpl"%>   
        <%else%>
        <div class="alert alert-info">{LBL_NOGROUPS}</div>
        <%/if%>