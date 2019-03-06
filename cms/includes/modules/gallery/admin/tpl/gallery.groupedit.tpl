<div class="page-header"><h1> <i class="fa fa-photo"><!----></i>{LBL_FOTOALBUMS}</h1></div>
<h3><%$GALOBJ.gallery.groupname%></h3>
<div class="row">
    <div class="col-md-6">
        <form class="jsonform" method="post" action="<%$PHPSELF%>">
        	<% include file="cb.panel.header.tpl" title="Settings"%>
        			<div class="form-group">
                        <label>{LBL_FOTOALBUM}:</label>
                        <%$GALOBJ.group%>
                    </div>
        			<div class="form-group">
                        <label>admin. {LBL_TITLE}:</label>
                        <input type="text" class="form-control" name="FORM[groupname]" value="<%$GALOBJ.gallery.groupname%>">
                    </div>
                    <div class="form-group">
                        <label>Template:</label>
                        <%$GALOBJ.gallery.templselect%>
                    </div>                    
                    <div class="form-group">
                        <label>Hashtags:</label>
                        <input type="text" class="form-control" name="FORM[g_hashtag]" value="<%$GALOBJ.gallery.g_hashtag%>">
                    </div>
                    <%include file="cb.radioswitch.tpl" value=$GALOBJ.gallery.approval name="FORM[approval]" label="{LA_VISIBLEMENU}"%>
                    <%include file="cb.radioswitch.tpl" value=$GALOBJ.gallery.g_enabled name="FORM[g_enabled]" label="{LA_DISABLEGALALBUM}"%>
                        
        			<div class="form-group">
                        <label>{LBL_GALPICUTRE}:</label>
                        <% if ($GALOBJ.gallery.picid==0) %>es wird ein Zufallsbild verwendet<br><%/if%>	
                        <a class="btn btn-default json-link" href="<%$eurl%>cmd=a_randpic&id=<%$GALOBJ.gallery.GID%>">{LBL_RANDOMPICUTRE}</a>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Breite</label>
    					    <input name="FORM[max_width]" placeholder="Breite" type="text" class="form-control input-sm" value="<%$GALOBJ.gallery.max_width%>">
                        </div>   
                        <div class="form-group col-md-6">
                            <label>Höhe</label>
                            <input name="FORM[max_height]" placeholder="Höhe" type="text" class="form-control input-sm" value="<%$GALOBJ.gallery.max_height%>">
                        </div>
                    </div>    
                    <div class="row">
                        <div class="form-group col-md-6">
                        <label>Thumbnail resize method:</label>
                       	<select class="form-control input-sm"  name="FORM[thumb_type]">
        					<option value="resize" <% if ($GALOBJ.gallery.thumb_type=='resize') %>selected<%/if%> >{LBL_GAL_RESIZE}</option>
                            <option value="resizetofit" <% if ($GALOBJ.gallery.thumb_type=='resizetofit') %>selected<%/if%> >{LBL_GAL_RESIZE} (fit)</option>
                            <option value="resizetofitpng" <% if ($GALOBJ.gallery.thumb_type=='resizetofitpng') %>selected<%/if%> >{LBL_GAL_RESIZE} (fit PNG)</option>                    
        					<option value="rounded" <% if ($GALOBJ.gallery.thumb_type=='rounded') %>selected<%/if%>>{LBL_GAL_ROUNDCORNER}</option>
        					<option value="crop" <% if ($GALOBJ.gallery.thumb_type=='crop') %>selected<%/if%>>{LBL_GAL_CROP}</option>										
                            <option value="boxed" <% if ($GALOBJ.gallery.thumb_type=='boxed') %>selected<%/if%>>Boxed</option>
    					</select>
                        </div>
                        <div class="form-group col-md-6">
                        <label>Crop Position</label>
                        <select class="form-control input-sm"  name="FORM[g_croppos]">
                            <option <% if ($GALOBJ.gallery.g_croppos=='NorthWest') %>selected<%/if%> value="NorthWest">NorthWest</option>
                            <option <% if ($GALOBJ.gallery.g_croppos=='North') %>selected<%/if%> value="North">North</option>
                            <option <% if ($GALOBJ.gallery.g_croppos=='NorthEast') %>selected<%/if%> value="NorthEast">NorthEast</option>
                            <option <% if ($GALOBJ.gallery.g_croppos=='West') %>selected<%/if%> value="West">West</option>
                            <option <% if ($GALOBJ.gallery.g_croppos=='Center') %>selected<%/if%> value="Center">Center</option>
                            <option <% if ($GALOBJ.gallery.g_croppos=='East') %>selected<%/if%> value="East">East</option>
                            <option <% if ($GALOBJ.gallery.g_croppos=='SouthWest') %>selected<%/if%> value="SouthWest">SouthWest</option>
                            <option <% if ($GALOBJ.gallery.g_croppos=='South') %>selected<%/if%> value="South">South</option>
                            <option <% if ($GALOBJ.gallery.g_croppos=='SouthEast') %>selected<%/if%> value="SouthEast">SouthEast</option>
                          </select>
                          </div>    
                       </div>
                      <div class="row">    
                          <div class="form-group col-md-6">
                          <label>Sortierung:</label>                 
    	               <select class="form-control"  name="FORM[default_order]">
        					<option value="post_time_int" <% if ($GALOBJ.gallery.default_order=='post_time_int') %>selected<%/if%> >Upload date</option>
        					<option value="pic_title" <% if ($GALOBJ.gallery.default_order=='pic_title') %>selected<%/if%>>{LBL_TITLE}</option>
        					<option value="morder" <% if ($GALOBJ.gallery.default_order=='morder') %>selected<%/if%>>manuelle Sortierung</option>										
        				</select>
                        </div>
                        <div class="form-group col-md-6">
                        <label>Sortierung Richtung:</label>
                        <select class="form-control"  name="FORM[default_direc]">
        					<option value="ASC" <% if ($GALOBJ.gallery.default_direc=='ASC') %>selected<%/if%> >aufsteigend</option>
        					<option value="DESC" <% if ($GALOBJ.gallery.default_direc=='DESC') %>selected<%/if%>>absteigend</option>
    					</select>
                        </div>
                      </div>  
        			<div class="form-group"><label>Link:</label><%$GALOBJ.gallery.url%></div>
                    
        	<div class="pull-right"><%$subbtn%></div>
            <% include file="cb.panel.footer.tpl"%>        	
        	<input type="hidden" name="cmd" value="save_gallery_group">
        	<input type="hidden" name="id" value="<%$GALOBJ.gid%>">
        	<input type="hidden" value="<%$epage%>" name="epage">
        </form>
   </div>
   <div class="col-md-6">
    <% include file="cb.panel.header.tpl" title="Beschreibungen"%>
    <div class="form-group">
        <label>{LBLA_LANGUAGE}</label>
        <%$GALADMIN.langselect%>
    </div>
        <form class="jsonform" method="post" action="<%$PHPSELF%>">
            <div class="form-group">
        			<label>{LBL_TITLE}:</label>
        			<input type="text" class="form-control" name="FORM_CON[g_title]" value="<%$GALADMIN.FORM_CON.g_title|hsc%>">
        	</div>
            <div class="form-group">
        			<label>Sub-{LBL_TITLE}:</label>
        			<textarea class="se-html" rows="3" name="FORM_CON[g_subtitle]"><%$GALADMIN.FORM_CON.g_subtitle|hsc%></textarea>
        	</div>
            <div class="form-group">
        			<label>{LBLA_DESCRIPTION}:</label>
        			<%$GALADMIN.fck%>
            </div>                
        		
        	<input type="hidden" name="FORM_CON_ID" value="<%$GALADMIN.FORM_CON.id%>">
        	<input type="hidden" name="cmd" value="save_group_content">
        	<input type="hidden" name="FORM_CON[lang_id]" value="<%$GALADMIN.uselang%>">
        	<input type="hidden" name="FORM_CON[g_id]" value="<%$GALADMIN.gid%>">
        	<input type="hidden" value="<%$epage%>" name="epage">
            <div class="subright"><%$subbtn%></div>
        </form>   
        <% include file="cb.panel.footer.tpl"%>   
   </div>
</div>        


<% if ($GALADMIN.admingallery) %>
<% include file="cb.panel.header.tpl" title="{LBL_TITLEPICTURE}"%>
<div class="row">
	<% foreach from=$GALADMIN.admingallery item=foto name=gloop %>
		<div class="col-md-2 text-center">
			<img data-id="<%$foto.img_id%>" src="<% $foto.thumbnail %>" alt="<%$foto.imginfo.pic_title%>" title="<%$foto.width_foto_px%> x <%$foto.height_foto_px%>" class="<% if ($galgroup.picid==$foto.img_id) %>bg-success <% /if %>img-thumbnail gallery-title-img">
		</div>
    <% /foreach %>
</div>
<% include file="cb.panel.footer.tpl"%> 	
<script>
$('.gallery-title-img').css('cursor','pointer');
$('.gallery-title-img').click(function() {
    execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=set_title_img&picid='+$(this).data('id')+'&gid=<% $galgroup.GID %>');
    $('.gallery-title-img').removeClass('bg-success');
    $(this).addClass('bg-success');    
});
</script>
<% /if %>