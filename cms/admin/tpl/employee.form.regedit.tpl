<%include file="cb.panel.header.tpl" title="{LBL_EMPLOYEE}"%>
        <form method="post" action="<%$PHPSELF%>" class="jsonform" enctype="multipart/form-data">
            <fieldset>
                <legend>Benutzer</legend>
                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="">{LBL_EMPLOYEE} (Login)</label>
                            <input type="text" class="form-control" autocomplete="off" autofocus="true" name="FORM[mitarbeiter_name]" value="<% $empobjform.mitarbeiter_name|hsc %>">
                        </div><!-- /.form-group -->
                    </div><!-- /.col-md-9 -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{LBL_EMPLOYEE} {LBL_GROUP}</label>
                            <select class="form-control custom-select" name="FORM[gid]">
                                <% foreach from=$employeegroups item=gr %><option <% if ($gr.id==$empobjform.gid) %>selected<%/if%> value="<%$gr.id %>"><% $gr.mgname %></option><%/foreach%>
                            </select>
                        </div><!-- /.form-group -->
                    </div><!-- /.col-md-3 -->
                </div><!-- /.row -->
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">{LBL_PASSWORD}</label>
                            <input type="password" class="form-control" autocomplete="off" name="FORM[passwort]" value="" placeholder="***********">
                        </div><!-- /.form-group -->
                    </div><!-- /.col-md-6 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">{LBL_PASSWORDCONFIRM}</label>
                            <input type="password" class="form-control" autocomplete="off" name="password_control" value="" placeholder="***********">
                        </div><!-- /.form-group -->
                    </div><!-- /.col-md-6 -->
                </div><!-- /.row -->
                
                <div class="form-group">
                    <label>{LBL_EMAILCOPY}</label>
                    <div class="input-group">
                        <label class="radio-inline">
                            <input <% if ($empobjform.mi_email_copy==1) %>checked<%/if%> type="radio" name="FORM[mi_email_copy]" value="1"> {LBL_YES}
                        </label>
                        <label class="radio-inline">
                            <input <% if ($empobjform.mi_email_copy==0) %>checked<%/if%> type="radio" name="FORM[mi_email_copy]" value="0"> {LBL_NO}
                        </label>
                    </div><!-- /.input-group -->
                </div><!-- /.form-group -->
            </fieldset>
            
            <div class="row">
                <div class="col-md-6">
                    <fieldset>
                        <legend>Adresse</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{LBL_FIRSTNAME}</label>
                                    <input type="text" class="form-control" required="" name="FORM_NOTEMPTY[mi_firstname]" value="<% $empobjform.mi_firstname|hsc %>">
                                </div><!-- /.form-group -->
                            </div><!-- /.col-md-6 -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">{LBL_LASTNAME}</label>
                                    <input type="text" class="form-control" required="" name="FORM_NOTEMPTY[mi_lastname]" value="<% $empobjform.mi_lastname|hsc %>">
                                </div><!-- /.form-group -->
                            </div><!-- /.col-md-6 -->
                        </div><!-- /.row -->
                        
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="str">Strasse</label>
                                    <input type="text" id="str" name="FORM[mi_str]" class="form-control">
                                </div><!-- /.form-group -->
                            </div><!-- /.col-md-9 -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="hnr">Hausnummer</label>
                                    <input type="text" id="hnr" name="FORM[mi_hnr]" class="form-control">
                                </div><!-- /.form-group -->
                            </div><!-- /.col-md-3 -->
                        </div><!-- /.row -->
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="plz">PLZ</label>
                                    <input type="text" id="plz" name="FORM[mi_plz]" class="form-control">
                                </div><!-- /.form-group -->
                            </div><!-- /.col-md-3 -->
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="ort">Ort</label>
                                    <input type="text" id="ort" name="FORM[mi_ort]" class="form-control">
                                </div><!-- /.form-group -->
                            </div><!-- /.col-md-9 -->
                        </div><!-- /.row -->
                    </fieldset>
                </div><!-- /.col-md-6 -->
                <div class="col-md-6">
                    <fieldset>
                        <legend>Kontaktdaten</legend>
                        <div class="form-group">
                            <label for="">{LBL_PHONE}</label>
                            <input type="text" class="form-control" name="FORM[mobile]" value="<% $empobjform.mobile|hsc %>">
                        </div><!-- /.form-group -->
                        
                        <div class="form-group">
                            <label for="">Fax.</label>
                            <input type="text" class="form-control" name="FORM[mobile]" value="<% $empobjform.mobile|hsc %>">
                        </div><!-- /.form-group -->
                        
                        <div class="form-group">
                            <label for="">{LBL_EMAIL}</label>
                            <input type="text" class="form-control" name="FORM[email]" value="<% $empobjform.email|hsc %>">
                        </div><!-- /.form-group -->
                    </fieldset>
                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->
    
            <input type="hidden" name="cmd" value="emp_saveemployee">
            <input type="hidden" name="epage" value="<%$epage%>">
            <input type="hidden" name="id" class="empid" value="<%$GET.id%>">
            <div class="form-feet"><%$subbtn%></div><!-- ./form-feet -->
        </form>
<%include file="cb.panel.footer.tpl"%>