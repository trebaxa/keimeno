<div class="row">
    <div class="col-md-6">
	   <%include file="cb.panel.header.tpl" title="Email Import" %>
            	<form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
            			<input type="hidden" name="cmd" value="email_import">
            			<input type="hidden" name="epage" value="<%$epage%>">
                    <div class="form-group">
            	       <label>Ziel-Gruppe (Zuordnung):</label>
            		  <%$NEWSLETTER.ngroups_select%>
            		</div>		
                    <div class="form-group">
            			<label>Welches Zeichen ist zwischen den Emails (Trenner):</label>
            			<input type="text" class="form-control" size="3" value="<% $GET.sign %>" name="sign">
            		</div>			
                    <div class="checkbox">
            			<label>
           			      <input type="checkbox" value="1" name="pro_zeile"> oder pro Zeile eine Email
                        </label>
            		</div>			
            		<div class="form-group">
            			<label>Email-Liste (*.txt):</label>
            			<input type="file" name="datei" size="30" class="file_btn">
            		</div>
            	<%$importbtn%>
                </form>
		<%include file="cb.panel.footer.tpl"%>
	</div>
    
    <div class="col-md-6">
	   <%include file="cb.panel.header.tpl" title="manuell hinzufügen" %>
       <form action="<%$PHPSELF%>" class="jsonform" method="post" enctype="multipart/form-data">
            			<input type="hidden" name="cmd" value="email_import_man">
            			<input type="hidden" name="epage" value="<%$epage%>">
                    <div class="form-group">
            	       <label>Ziel-Gruppe (Zuordnung):</label>
            		  <%$NEWSLETTER.ngroups_select%>
            		</div>	
                    <div class="form-group">
            	       <label>pro Zeile eine E-Mail:</label>
            		  <textarea class="se-html" name="FORM[emails]"></textarea>
            		</div>
                    <%$importbtn%>
                </form>		
       <%include file="cb.panel.footer.tpl"%>
	</div>
</div>  