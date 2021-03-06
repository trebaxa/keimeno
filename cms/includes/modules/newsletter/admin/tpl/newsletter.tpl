<div class="row mt-lg">
 <div class="col-md-12">
    <div class="btn-group">
        <a class="btn btn-primary" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=edit"><i class="fa fa-plus"></i> Neu anlegen</a>
        <% if ($NEWSLETTER.not_finished_newsletter==true) %>
            <a class="btn btn-secondary json-link" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=a_reset">Reset Newsletter</a>
        <%/if%>
        <% if ($cmd=='edit') %>
            <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&aktion=setframework&id=<%$GET.id%>">Newsletter Vorlage laden (&uuml;berschreibt bestehenden Inhalt!)</a>
            <a class="btn btn-secondary" href="<%$eurl%>cmd=preview&id=<%$GET.id%>" title="Versenden"><i class="fa fa-envelope"></i> Versenden</a>
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
    <%include file="newsletter.addmails.tpl"%>
<%/if%>


<% if ($cmd=="listmails") %>
    <%include file="newsletter.listmails.tpl"%>
<%/if%>


<% if ($cmd=="load_lists" || $cmd=="group_edit") %>
    <div id="js-lists">
        <% include file="newsletter.lists.tpl"%>
    </div>
<%/if%>

<% if ($cmd=="preview") %>    
    <% include file="newsletter.preview.tpl"%>
<%/if%>

<% if ($cmd=="edit") %>
    <% include file="newsletter.edit.tpl"%>
<%/if%>


<% if ($cmd=="news_confirm") %>
 <% include file="newsletter.confirm.tpl"%>
<%/if%>    

<% if ($cmd=="show_send") %>
    <% include file="newsletter.recipient.tpl"%>
<%/if%>   

<% if ($cmd=="members") %>
 <% include file="newsletter.members.tpl"%>
<%/if%>         

<% if ($cmd=="a_tracking") %>
    <% include file="newsletter.tracking.tpl"%>
<%/if%>   

<script>
function remove_newswarn() {
   $('#js-newswarn').remove(); 
}

function reload_list() {
    simple_load('js-lists', '<%$eurl%>cmd=reload_list');
}
</script>