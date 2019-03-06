<%* 'Anfang: Formular Kunde bearbeiten/anlegen' *%>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Kunden Daten</h3><!-- /.panel-title -->
    </div><!-- /.panel-heading -->
    <div class="panel-body">
    <form method="POST" class="jsonform" action="<%$PHPSELF%>">

            <input type="hidden" name="kid" value="<% $GET.kid %>">
            <input type="hidden" name="aktion" value="a_save">
       
        <div class="row">
            <div class="col-md-6">
                <fieldset>
                    <legend>{LA_ANSCHRIFT}</legend>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{LA_ANREDE}</label>
                                <select class="form-control" name="FORM[anrede_sign]">
                                    <%$CUSTOMER.anrede_arr%>                                  
                                </select>
                            </div><!-- /.form-group -->
                        </div><!-- /.col-md-2 -->
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="fstnm">{LA_VORNAME}</label>
                                <input id="fstnm" type="text" class="form-control" placeholder="{LA_VORNAME}" value="<% $CUSTOMER.vorname|hsc %>" name="FORM[vorname]">
                            </div><!-- /.form-group -->
                        </div><!-- /.col-md-5 -->
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="lstnm">{LA_NACHNAME}</label>
                                <input id ="lstnm" type="text" class="form-control" placeholder="{LA_NACHNAME}" value="<% $CUSTOMER.nachname|hsc %>" name="FORM[nachname]">
                            </div><!-- /.form-group -->
                        </div><!-- /.col-md-5 -->
                    </div><!-- /.row -->
                    <div class="form-group">
                        <label for="fra">{LA_FIRMA}</label>
                        <input id="fra" type="text" class="form-control" placeholder="{LA_FIRMA}" value="<% $CUSTOMER.firma|hsc %>" name="FORM[firma]">
                    </div><!-- /.form-group -->
                    <div class="form-group">
                        <label for="firih">{LA_FIRMAINHABER}</label>
                        <input id="firih" type="text" class="form-control" placeholder="{LA_FIRMAINHABER}" value="<% $CUSTOMER.firma_inhaber|hsc %>" name="FORM[firma_inhaber]">
                    </div><!-- /.form-group -->
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                            <label for="cstr">{LA_STRASSE}</label>
                            <input id="cstr" type="text" class="form-control" placeholder="{LA_STRASSE}" value="<% $CUSTOMER.strasse|hsc %>" name="FORM[strasse]">
                        </div><!-- /.form-group -->
                        </div><!-- /.col-md-10 -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="hno">{LA_HAUSNR}</label>
                                <input id="hno" class="form-control" maxlength="6" value="<% $CUSTOMER.hausnr|hsc %>" name="FORM[hausnr]">
                            </div><!-- /.form-group -->
                        </div><!-- /.col-md-2 -->
                    </div><!-- /.row -->
                    <!-- <div class="form-group">
                        <label for="cstr2">{LA_STRASSE} 2</label>
                        <input id="cstr2" type="text" class="form-control" placeholder="{LA_STRASSE}" value="<% $CUSTOMER.strasse_zusatz|hsc %>" name="FORM[strasse_zusatz]">
                    </div> --><!-- /.form-group -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="plz">{LA_PLZ}</label>
                                <input id="plz" type="text" class="form-control" placeholder="{LA_PLZ}" value="<% $CUSTOMER.plz|hsc %>" name="FORM[plz]" maxlength="10">
                            </div><!-- /.form-group -->
                        </div><!-- /.col-md-3 -->
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="cort">{LA_ORT}</label>
                                <input id="cort" type="text" class="form-control" placeholder="{LA_ORT}" value="<% $CUSTOMER.ort|hsc %>" name="FORM[ort]">
                            </div><!-- /.form-group -->
                        </div><!-- /.col-md-9 -->
                    </div><!-- /.row -->
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>{LA_LAND}</label>
                            <%$CUSTOMER.landselect%>
                        </div><!-- /.form-group -->
                        <div class="form-group col-md-6">
                            <label for="prov">Provinz</label>
                            <input id="prov" type="text" class="form-control" placeholder="Provinz" value="<% $CUSTOMER.province|hsc %>" name="FORM[province]">
                        </div><!-- /.form-group -->
                     </div>   
                </fieldset><!-- Anschrift -->
            </div><!-- /.col-md-6 -->
            <div class="col-md-6">
                <fieldset>
                    <legend>{LA_KONTAKTMGLICHKEITEN}</legend>
                    <div class="form-group">
                        <label for="cmail">{LA_EMAIL}</label>
                        <input id="cmail" placeholder="{LA_EMAIL}" value="<% $CUSTOMER.email|hsc %>" name="FORM[email]" type="email" class="form-control">
                    </div><!-- /.form-group -->
                    <div class="form-group">
                        <label for="cmailalt">{LA_EMAILALTERNATIV}</label>
                        <input id="cmailalt" type="email" class="form-control" placeholder="{LA_EMAIL}" value="<% $CUSTOMER.email_notpublic|hsc %>" name="FORM[email_notpublic]">
                    </div><!-- /.form-group -->
                   <div class="form-group">
                        <label for="cpass">{LA_PASSWORT}</label>
                        <input id="cpass" class="form-control" type="password" placeholder="{LA_PASSWORT}" value="" name="FORM[passwort]">
                        <p class="help-block">{LA_PASSNEUSETZEN}</p>
                    </div><!-- /.form-group -->                    
                  <div class="row">
                    <div class="form-group col-md-6">
                        <label for="ctel">{LA_TELEFON}</label>
                        <input id="ctel" type="text" class="form-control" placeholder="{LA_TELEFON}" value="<% $CUSTOMER.tel|hsc %>" name="FORM[tel]">
                    </div><!-- /.form-group -->
                    <div class="form-group col-md-6">
                        <label for="ctelcom">{LA_TELEFONDIENSTLICH}</label>
                        <input id="ctelcom" type="text" class="form-control" placeholder="{LA_TELEFONDIENSTLICH}" value="<% $CUSTOMER.tel_office|hsc %>" name="FORM[tel_office]">
                    </div><!-- /.form-group -->
                  </div> 
                  <div class="row"> 
                    <div class="form-group col-md-6">
                        <label for="ctelmobil">{LA_MOBIL}</label>
                        <input id="ctelmobil" type="text" class="form-control" placeholder="{LA_MOBIL}" value="<% $CUSTOMER.mobil|hsc %>" name="FORM[mobil]">
                    </div><!-- /.form-group -->
                    <div class="form-group col-md-6">
                        <label for="cfax">{LA_FAX}</label>
                        <input id="cfax" type="text" class="form-control" placeholder="{LA_FAX}" value="<% $CUSTOMER.fax|hsc %>" name="FORM[fax]">
                    </div><!-- /.form-group -->
                   </div> 
                    <div class="form-group">
                        <label for="cskype">Skype</label>
                        <input id="cskype" type="text" class="form-control" placeholder="Skype" value="<% $CUSTOMER.skype|hsc %>" name="FORM[skype]">
                    </div><!-- /.form-group -->
                </fieldset><!-- Kontaktmöglichkeiten -->
            </div><!-- /.col-md-6 -->
        </div><!-- /.row -->
        <div class="row">
            <div class="col-md-6">
                <fieldset>
                    <legend>{LA_BANKVERBINDUNG}</legend>
              
                    <div class="form-group">
                        <label for="bnkna">{LA_BANKNAME}</label>
                        <input id="bnkna" type="text" class="form-control" placeholder="{LA_BANKNAME}"  value="<% $CUSTOMER.bank|hsc %>" name="FORM[bank]">
                    </div><!-- /.form-group -->
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="ciban">IBAN</label>
                            <input id="ciban" type="text" class="form-control" placeholder="IBAN"  value="<% $CUSTOMER.iban|st|hsc %>" name="FORM[iban]">
                        </div><!-- /.form-group -->
                        <div class="form-group col-md-6">
                            <label for="cbic">BIC</label>
                            <input id="cbic" type="text" class="form-control" placeholder="BIC"  value="<% $CUSTOMER.bic|st|hsc %>" name="FORM[bic]">
                        </div><!-- /.form-group -->
                     </div>   
                </fieldset><!-- Bankverbindung -->
            </div><!-- /.col-md-6 -->
            <div class="col-md-6">
                <fieldset>
                    <legend>{LA_WEITERES}</legend>
                    <div class="form-group">
                        <label for="umst">{LA_USTID}</label>
                        <input id="umst" type="text" class="form-control" placeholder="{LA_USTID}" value="<% $CUSTOMER.str_nr|hsc %>" name="FORM[str_nr]">
                    </div><!-- /.form-group -->
                    <div class="form-group">
                        <label for="curl">{LA_WEBSEITEADD}</label>
                        <input id="curl" type="text" class="form-control" placeholder="Homepage" value="<% $CUSTOMER.homepage|hsc %>" name="FORM[homepage]">
                    </div><!-- /.form-group -->
                   <%* <div class="form-group">
                        <label for="stdnfr">{LA_STANDARDSTUNDENLOHNFR}</label>
                        <input id="stdnfr" type="text" class="form-control" placeholder="{LA_STANDARDSTUNDENLOHNFR}" value="<% $CUSTOMER.std_fee_nt|hsc %>" name="FORM[std_fee_nt]"><%$curr_lettercode%>
                    </div><!-- /.form-group -->*%>
                    <div class="form-group">
                        <label for="zunum">{LA_ZUSTZLICHEKUNDENNUMME}</label>
                        <input id="zunum" type="text" class="form-control" placeholder="{LA_ZUSTZLICHEKUNDENNUMME}" value="<% $CUSTOMER.knr_additional|hsc %>" name="FORM[knr_additional]">
                    </div><!-- /.form-group -->
                    <div class="form-group">
                        <label for="dofb">{LA_GEBURTSTAG}</label>
                        <input id="dofb" type="text" class="form-control" placeholder="{LA_GEBURTSTAG}" value="<% $CUSTOMER.birthday|hsc %>" name="FORM[birthday]">
                    </div><!-- /.form-group -->
                    <div class="form-group">
                        <label for="username">{LA_BENUTZERNAME}</label>
                        <input id="username" type="text" class="form-control" placeholder="Benutzername" value="<% $CUSTOMER.username|hsc %>" name="FORM[username]">
                    </div><!-- /.form-group -->
 
                    <%*<div class="form-group">
                        <label for="cfound">{LA_GEFUNDENVON}</label>
                        <input id="cfound" type="text" class="form-control" placeholder="{LA_GEFUNDENVON}" value="<% $CUSTOMER.knownof|hsc %>" name="FORM[knownof]">
                    </div><!-- /.form-group -->*%>
                </fieldset><!-- Weiteres -->
            </div><!-- /.col-md-6 -->
        </div><!-- /.row -->
        
  <div class="row">
    <div class="col-md-6">
    <fieldset>
            <legend>{LBL_OPTIONS}</legend>
            <%include file="cb.radioswitch.tpl" label="Newsletter {LA_AAKTIV}" name="FORM[mailactive]" value="<%$CUSTOMER.mailactive%>"%>
            <%include file="cb.radioswitch.tpl" label="{LA_IMINDEXVEROEFF}" name="FORM[cms_isindex]" value="<%$CUSTOMER.cms_isindex%>"%>
            <%*<%include file="cb.radioswitch.tpl" label="{LA_RECHNUNGENSTEUERBEFRE}" name="FORM[is_firma]" value="<%$CUSTOMER.is_firma%>"%>*%>
            <%*<%include file="cb.radioswitch.tpl" label="{LA_VIP}" name="FORM[vip]" value="<%$CUSTOMER.vip%>"%>*%>
            <%*<%include file="cb.radioswitch.tpl" label="{LA_DIESENKUNDENFRBAREINZ}" name="FORM[is_barpaycustomer]" value="<%$CUSTOMER.is_barpaycustomer%>"%>*%>
            <%include file="cb.radioswitch.tpl" label="{LA_BEINCHSTERANMELDUNGNE}" name="FORM[new_pass_mode]" value="<%$CUSTOMER.new_pass_mode%>"%>
            <%include file="cb.radioswitch.tpl" label="{LA_KUNDENSPERREN}" name="FORM[sperren]" value="<%$CUSTOMER.sperren%>"%>
            <%*<%include file="cb.radioswitch.tpl" label="{LA_BANKEINZUGERLAUBNISFR}" name="FORM[gbl_bankcollection]" value="<%$CUSTOMER.gbl_bankcollection%>"%>*%>
            
            
     
         
        </fieldset><!-- Optionen -->
        
        <%if (count($CUSTOMER.customer_fields)>0) %>
        <fieldset>
            <legend>{LA_INDIVIDUELLEFELDER}</legend>            
                <%foreach from=$CUSTOMER.customer_fields item=row %>
                    <div class="form-group">
                        <label for="<% $row.key %>"><% $row.CF.cf_name %></label>
                        <input id="<% $row.key %>" type="text" class="form-control" name="FORM[<%$row.key%>]" value="<% $row.value|hsc %>">
                    </div><!-- /.form-group -->
                <%/foreach%>
            
        </fieldset><!-- Individualfelder -->
       <%/if%> 
    </div>
    <div class="col-md-6">
       
    </div>
  </div>      
        
        
        <div class="form-group">
            <label>{LA_BESCHREIBUNG}</label>
            <%$POBJ.desceditor%>
        </div><!-- /.form-group -->
        
        <table class="table table-striped table-hover">
            <% foreach from=$POBJ.modincs item=modinc name=mloop %><% include file=$modinc %><%/foreach%>
        </table>
        
        <div class="form-feet"><%$subbtn%></div><!-- /.form-feet -->
    </form>
    </div>
</div><!-- /.panel panel-default -->
<%* 'Ende: Formular Kunde bearbeiten/anlegen' *%>