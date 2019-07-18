<div class="row">
    <div class="col-md-6">
    <%include file="cb.panel.header.tpl" title="Create Template Package"%>  
    <form action="<%$PHPSELF%>" method="POST">
        <input type="hidden" name="cmd" value="create_template_backup"/>
        <input type="hidden" name="epage" value="<%$epage%>"/>
        <div class="form-group">
            <label>CMS Template Name</label>
            <input class="form-control" type="text" value="" placeholder="" name="FORM[tpl_name]" />
        </div>
        <input class="btn btn-primary" value="create layout backup" type="submit" />
    </form>
    <%include file="cb.panel.footer.tpl"%>
    </div>
    <div class="col-md-6">
    <%include file="cb.panel.header.tpl" title="Template Package Install"%>
    <form class="jsonform" action="<%$PHPSELF%>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="cmd" value="install_layout"/>
        <input type="hidden" name="epage" value="<%$epage%>"/>
        <div class="checkbox">
            <label>
                <input value="1" type="checkbox" name="FORM[only_update]"> Only update
            </label>
        </div>
        <div class="form-group">
            <label>Datei</label>
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Keine Datei ausgewählt" readonly="" value="" name=""/>
                <input class="xform-control" onchange="this.previousElementSibling.value = this.value" type="file" value="" placeholder="" name="datei" />
                <span class="input-group-btn">
                    <button class="btn btn-secondary" type="button">Durchsuchen</button>
                </span>
            </div>
        </div>
        <input class="btn btn-primary" value="upload and install" type="submit" />
    </form>
    <div class="alert alert-info">Installiert das hochgeladene CMS Template und überschreibt das bestehende. ACHTUNG! Angelegte Daten innerhalb der Flex-Templates werden überschrieben.</div>
    <%include file="cb.panel.footer.tpl"%>
    </div>
</div>   
 