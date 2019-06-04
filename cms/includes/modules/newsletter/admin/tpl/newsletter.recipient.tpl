 <%include file="cb.panel.header.tpl" title="{LBLA_RECIPIENTS} 2/3 `$NEWSLETTER.newsedit.e_subject`"%>

    <% if (count($NEWSSLETTER.errors)>0) %>
                <div class="alert alert-warning">
                    <b>{LBLA_WARNINGS}</b>:
                    <% foreach from=$NEWSLETTER.errors item=err %>	
                         <%$err%><br>
                    <%/foreach%>
                </div>
    <%/if%>            
    <% if (count($NEWSSLETTER.errors_critical)>0) %>
        <div class="alert alert-danger">
            <b>Fehler</b>:
            <% foreach from=$NEWSLETTER.errors_critical item=err %>	
                 <%$err%><br>
            <%/foreach%>
        </div>
    <%/if%>
    
    <% if (count($NEWSSLETTER.errors_critical)==0) %>
        <form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
        	<div class="form-group">	
                <label>Mitglieder Gruppen:</label>
        		<select class="form-control" name="FORM[groups]">
                <% foreach from=$NEWSLETTER.groupopt item=opt %>	
                         <%$opt%>
                    <%/foreach%>
                </select>
              </div>  
              
              <h4>E-Mail Listen</h4>
              <div class="form-group">
                <% foreach from=$NEWSLETTER.groups item=row %>
                   <div class="checkbox">
                        <label for="g<%$row.id%>">
                        <input type="checkbox" name="GROUPS[]" value="<%$row.id%>" id="g<%$row.id%>" />
                        <%$row.group_name%></label>
                   </div>
                    <%/foreach%>                
              </div>  
              
                <input type="hidden" name="epage" value="<%$epage%>">
                <input type="hidden" name="cmd" value="news_confirm">
                <input type="hidden" name="id" value="<%$POST.id%>">
                <input type="submit" class="btn btn-primary" value="{LBLA_CONFIRMATION} 3/3">
            </form>
    <%else%>
        <div class="bg-danger">{LBLA_STOPPNEWS}</div>        
    <%/if%>            
            
<%include file="cb.panel.footer.tpl"%>            