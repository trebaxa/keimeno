<div class="row mt-lg">
 <div class="col-md-12">
    <div class="btn-group">
        <a class="btn btn-primary" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=edit"><i class="fa fa-plus"></i> Neu anlegen</a>
        <% if ($NEWSLETTER.not_finished_newsletter==true) %>
            <a class="btn btn-default json-link" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=a_reset">Reset Newsletter</a>
        <%/if%>
        <% if ($cmd=='edit') %>
            <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=setframework&id=<%$GET.id%>">Newsletter Vorlage laden (&uuml;berschreibt bestehenden Inhalt!)</a>
        <%/if%>
    </div>
   </div>
</div>


<% if ($NEWSLETTER.not_finished_newsletter==true) %>
<div class="alert alert-warning mt-lg" id="js-newswarn">
    WARNUNG! Ein Newsletter wurde noch nicht beendet. Klicken Sie auf "Reset Newsletter", um alle 
        Empf&auml;nger f&uuml;r den Empfang eines neuen Newsletters zu aktivieren.
</div>        
<%/if%>

<% if ($cmd=="show_hist") %>
    <%include file="newsletter.table.tpl"%>  
<%/if%>

<% if ($cmd=="add_mails") %>
<h3>Newsletter</h3>
<div class="col-md-6">
	<fieldset>
		<legend>Optionen</legend>
	
	<form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="aktion" value="email_import">
			<input type="hidden" name="epage" value="<%$epage%>">
        <div class="form-group">
	       <label>Ziel-Gruppe (Zuordnung):</label>
		  <%$NEWSLETTER.ngroups_select%>
		</div>		
        <div class="form-group">
			<label>Welches Zeichen ist zwischen den Emails?<br>Trenner:</label>
			<input type="text" class="form-control" size="3" value="<% $GET.sign %>" name="sign">
		</div>			
        <div class="form-group">
			<label>Oder: Pro Zeile eine Email:</label>
			<input type="checkbox" value="1" name="pro_zeile"> Ja
		</div>			
		<div class="form-group">
			<label>Email-Liste (*.txt):</label>
			<input type="file" name="datei" size="30" class="file_btn">
		</div>
	<%$importbtn%>
    </form>
			</fieldset>
		</div>
<%/if%>


<% if ($cmd=="listmails") %>
<% if (count($NEWSLETTER.emailliste)>0) %>
<table class="table table-striped table-hover" id="listmails-table">
<% foreach from=$NEWSLETTER.emailliste item=row %>		
 <tr>
    <td><% $row.email %></td>
 </tr>
<%/foreach%>
</table>
<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="listmails-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>
<%else%>
    <div class="bg-info text-info">Keine EMails eingetragen</div>
<%/if%> 
<%/if%>


<% if ($cmd=="load_lists" || $cmd=="group_edit") %>
    <% include file="newsletter.lists.tpl"%>
<%/if%>

<% if ($cmd=="preview") %>
    <h3>{LBLA_PREVIEW} 2/4</h3>
    <div class="text-center">
<form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="aktion" value="show_send">
        <input type="hidden" name="id" value="<%$GET.id%>">
	 <input type="submit" class="btn btn-primary" value="{LBLA_RECIPIENTS} 3/4"></form>
     </div>

     <br><iframe src="<%$NEWSLETTER.preview_link%>" width="99%" height="600" name="news_preview" scrolling="yes" marginheight="0" marginwidth="0" frame target="_self" class="thumb"></iframe>

<%/if%>

<% if ($cmd=="edit") %>
    <% include file="newsletter.edit.tpl"%>
<%/if%>


<% if ($cmd=="news_confirm") %>
 <% include file="newsletter.confirm.tpl"%>
<%/if%>    

<% if ($cmd=="show_send") %>
<h3>{LBLA_RECIPIENTS} 3/4 - "<% $NEWSLETTER.newsedit.e_subject%>"</h3>
<% if (count($NEWSSLETTER.errors)>0) %>
            <div class="bg-info text-info">
            <b>{LBLA_WARNINGS}</b>:
            <% foreach from=$NEWSLETTER.errors item=err %>	
                 <%$err%><br>
            <%/foreach%>
            </div>
<%/if%>            
<% if (count($NEWSSLETTER.errors_critical)>0) %>
            <div class="bg-danger">
            <b>Fehler</b>:
            <% foreach from=$NEWSLETTER.errors_critical item=err %>	
                 <%$err%><br>
            <%/foreach%>
            </div>
<%/if%>

<% if (count($NEWSSLETTER.errors_critical)==0) %>
    <form method="post" action="<%$PHPSELF%>" enctype="multipart/form-data">
	<div class="form-group">	
        <label>{LBLA_RECIPIENTS}:</label>
		<select class="form-control" name="FORM[groups]">
        <% foreach from=$NEWSLETTER.groupopt item=opt %>	
                 <%$opt%>
            <%/foreach%>
        </select>
      </div>  
        <input type="hidden" name="epage" value="<%$epage%>">
        <input type="hidden" name="aktion" value="news_confirm">
        <input type="hidden" name="id" value="<%$POST.id%>">
        <input type="submit" class="btn btn-primary" value="{LBLA_CONFIRMATION} 4/4">
        </form>
<%else%>
 <div class="bg-danger">{LBLA_STOPPNEWS}</div>        
<%/if%>

<%/if%>   

<% if ($cmd=="members") %>
<h3>{LBLA_NEWSDEACTEMAILS}</h3>
<br><form action="<%$PHPSELF%>" method="post">
    <input type="hidden" name="epage" value="<%$epage%>">
    <input type="hidden" name="cmd" value="a_deac_news">{LBLA_DEAK_NEWS}:<br>
    <textarea class="form-control se-html" name="emails" rows="10" cols="90"></textarea>
        <%$subbtn%></form> 
<h3>{LBLA_NEWSMEMBERS}</h3>
    <table class="table table-striped table-hover" id="customer-table">
    <thead>
        <tr>
           <th>Kunde</th>
	       <th>Email</th>
	       <th>Knr</th>           
	       <th></th>
        </tr>
    </thead>
    <% foreach from=$NEWSLETTER.members item=row %>
        <tr>
            <td><%$row.nachname%>, <%$row.vorname%></td>
            <td><%$row.email%></td>
            <td><%$row.kid%></td>            
            <td><% foreach from=$row.icons item=icon %><%$icon%><%/foreach%></td>
        </tr>  
        <%/foreach%>
    </table>  
<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="customer-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>         
<%/if%>         

<% if ($cmd=="a_tracking") %>
<% if (count($NEWSLETTER.all_feedback )>0) %> 
<h3>{LBLA_NEWSHASREAD} (<%$NEWSLETTER.all_feedback_count%>):</h3>
        <table class="table table-striped table-hover" id="okfeedback-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>{LBLA_READED} X</th>
                    <th>Status</th>
                </tr>
           </thead>     
        <% foreach from=$NEWSLETTER.all_feedback key=email item=counter %>
            <tr>
                <td><%$email %></td>
                <td><%$counter %></td>
                <td>
                <% if ($email|in_array:$NEWSLETTER.no_feedback) %>
                    <i class="fa fa-warning fa-red"><!----></i>
                <%else%>
                    <i class="fa fa-check-circle fa-green"><!----></i>                    
                <%/if%>    
                </td>
            </tr>
        <%/foreach%>
    </table>
<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="okfeedback-table" scope="global"%>
<%include file="table.sorting.script.tpl"%>       
<%/if%>    
          
<%/if%>   

<script>
function remove_newswarn() {
   $('#js-newswarn').remove(); 
}
</script>