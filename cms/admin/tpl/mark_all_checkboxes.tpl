<% if ($class=="") %>
    <% assign var="class" value="checkall" %>
<% /if %>
<% if ($align=="") %>
    <% assign var="align" value="right" %>
<% /if %>
<% if ($label=="") %>
    <% assign var="label" value="{LA_ALLEAUSWHLEN}" %>
<% /if %>
<div class="row">
    <div class="col-md-12 text-<%$align%>">
        <div class="checkbox">
            <label>
                <input <% if ($ischecked=='true') %>checked=""<%/if%> type="checkbox" class="js-selecctallbox" data-class="<%$class%>"/> <%$label%>
            </label>
        </div>    
    </div>
</div>