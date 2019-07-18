<div class="row">
    <div class="col-md-3">
        <%include file="cb.panel.header.tpl" title="`$MENUS.menu.m_name`"%>
          <div id="js-menu-nested">
            <%include file="menus.nestedtree.tpl"%>
            </div>
        <%include file="cb.panel.footer.tpl"%> 
    </div>
    <div class="col-md-9">
     <div class="row">
        <div class="col-md-6">
        <%include file="cb.panel.header.tpl" title="Interne Seite"%>
        <form class="jsonform" method="post" action="<%$PHPSELF%>">
            <input type="hidden" value="add_item" name="cmd" />
            <input type="hidden" value="<%$epage%>" name="epage" />
            <input type="hidden" value="<%$MENUS.menu.id%>" name="FORM[mm_mid]" />
    
            <div class="form-group ">
                <label>Seiten Auswahl</label>
                <select class="form-control custom-select" name="FORM[mm_id]">
                    <% foreach from=$MENUS.menuorg_selectox item=opt %>
                        <%$opt%> 
                    <%/foreach%>
                </select>
            </div>
            
            <div class="form-group ">
                <label>Position</label>
                <select class="form-control custom-select" name="FORM[mm_parent]">
                   <option value="0">Root</option>
                    <% foreach from=$MENUS.menu_selectox item=opt %>
                        <%$opt%> 
                    <%/foreach%>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> hinzufügen</button>
          
        </form>
        <%include file="cb.panel.footer.tpl"%>
        </div>
        <div class="col-md-6">
        <%include file="cb.panel.header.tpl" title="Manueller Link"%>
        <form class="jsonform" method="post" action="<%$PHPSELF%>">
            <input type="hidden" value="add_item" name="cmd" />
            <input type="hidden" value="<%$epage%>" name="epage" />
            <input type="hidden" value="<%$MENUS.menu.id%>" name="FORM[mm_mid]" />            
            <div id="js-menuedit"><%include file="menus.edit.medit.tpl"%></div>            
            
        </form>
        <%include file="cb.panel.footer.tpl"%>
        </div>
     </div>
     
           
    </div>    
</div>


<div class="row">
    <div class="col-md-12">
         <form action="<%$PHPSELF%>" method="POST" class="jsonform">
          <input type="hidden" value="save_menu" name="cmd" />
          <input type="hidden" value="<%$epage%>" name="epage" />
          <input type="hidden" value="<%$MENUS.menu.id%>" name="id" />
            <div class="form-group">
                <label>Menü Name</label>
                <input type="text" class="form-control" name="FORM[m_name]" value="<%$MENUS.menu.m_name|sthsc%>"/>
            </div>
        
            <div class="form-group">
                <label>Template</label>
                <textarea name="FORM[m_tpl]" class="se-html"><%$MENUS.menu.m_tpl|hsc%></textarea>
            </div>
           <%$subbtn%>
          </form>
     
     <h3>Beispiel Menu Code</h3>
     <div class="well">
        <code>
            <%$MENUS.example|hsc|nl2br%>
        </code>
     </div>
           
    </div>
</div>

<script>
function reload_mmtree() {
     simple_load('js-menu-nested','<%$eurl%>cmd=reload_mmtree&id=<%$MENUS.menu.id%>');
}
</script>