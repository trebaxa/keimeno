<div class="page-header"><h1>{LBL_DOWNLOADCENTER}</h1></div>


    <% if ($cmd=='') %>
        <% include file="downloadcenter.expl.tpl" %>  
    <% /if %>

    
    <% if ($cmd=='a_tracking') %>
        <% include file="downloadcenter.tracking.tpl" %>  
    <% /if %>
    
    
    <% if ($cmd=='edit') %>
        <% include file="downloadcenter.edit.tpl" %>  
    <% /if %>
    
    <% if ($cmd=='mfiles') %>
        <% include file="downloadcenter.mfiles.tpl" %>  
    <% /if %>
    
    
    <% if ($cmd=='sync') %>
        <div class="bg-success">{LBL_DONE}</div>
    <% /if %>