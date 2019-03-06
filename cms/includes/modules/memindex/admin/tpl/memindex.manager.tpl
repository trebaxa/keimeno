  <form class="form-inline">
                <div class="form-group">
                    <label class="sr-only">Neuen Kunden anlegen</label>
                    <a class="btn btn-primary" data-toggle="modal" data-target="#addcustomermodal" href="#"><i class="fa fa-plus"></i> {LA_NEUERKUNDE}</a>
                </div><!-- /.form-group -->
                <div class="form-group">
                    <label class="sr-only" for="wort">{LA_KUNDENSUCHEN}</label>
                    <input placeholder="{LA_KUNDENSUCHEN}" autocomplete="off" type="text" class="form-control" id="wort" value="<% $GET.wort|hsc %>" name="wort" size="11" onKeyUp="simple_load('ksuche_areaw','<%$PHPSELF%>?epage=<%$epage%>&cmd=ax_search&orderby=<% $GET.orderby %>&direc=<% $GET.direc %>&sword='+$('#wort').val());$('#ktable').hide();">
                </div><!-- /.form-group -->
                <div class="form-group">
                    <label for="">{LA_FILTER}</label>
                    <select class="form-control" onChange="location.href=this.options[this.selectedIndex].value">
                        <option <% if ($MEMINDEX.settings.filter.type=='-') %>selected<%/if%> value="<%$PHPSELF%>?type=-&aktion=showall">{LBL_NOSELECTION}</option>
                        <option <% if ($MEMINDEX.settings.filter.type=='showinactive') %>selected<%/if%> value="<%$PHPSELF%>?type=showinactive&aktion=showall">{LBL_SHOWINACTIV}</option>
                        <option <% if ($MEMINDEX.settings.filter.type=='notmember') %>selected<%/if%> value="<%$PHPSELF%>?type=notmember&aktion=showall">{LBL_NOTMEMBER}</option>
                        <option <% if ($MEMINDEX.settings.filter.type=='notindexed') %>selected<%/if%> value="<%$PHPSELF%>?type=notindexed&aktion=showall">{LBL_NOTINDEXED}</option>
                        <option <% if ($MEMINDEX.settings.filter.type=='nonewsletter') %>selected<%/if%> value="<%$PHPSELF%>?type=nonewsletter&aktion=showall">{LBL_NONEWSLETTER}</option>
                        <option <% if ($MEMINDEX.settings.filter.type=='membersince') %>selected<%/if%> value="<%$PHPSELF%>?type=membersince&aktion=showall">{LBL_LAST_500}</option>
                        <!-- <option <% if ($MEMINDEX.settings.filter.type=='showall') %>selected<%/if%> value="<%$PHPSELF%>?type=a_sall&aktion=showall">{LBL_SHOWALL}</option> -->
                    </select>
                </div><!-- /.form-group -->
                <div class="form-group">
                    <label for="">Export {LA_NACH} XLS</label>
                    <select class="form-control" size="-1" onChange="location.href=this.options[this.selectedIndex].value">
                        <option value="<% $PHPSELF %>?cmd=">-</option>
                        <option value="<% $PHPSELF %>?cmd=xls_kunden">{LA_ALLEKUNDEN}</option>
                        <option value="<% $PHPSELF %>?cmd=xls_kundennewsakt">Newsletter {LA_AAKTIV}</option>
                        <option value="<% $PHPSELF %>?cmd=xls_kundennewsnotakt">Newsletter {LA_INAKTIV}</option>
                        <option value="<% $PHPSELF %>?cmd=xls_kundenfirma">{LA_FIRMENKUNDEN}</option>
                    </select>
                </div><!-- /.form-group -->
            </form>
        
            <%include file="cb.panel.header.tpl" title="{LA_KUNDENSTAMM}"%>
                <div id="ksuche_areaw"></div>
                <div id="ktable">
                    <% if ($CUSTOMER.kid==0) %><% include file="memindex.table.tpl" %><%/if%>
                </div>
            <%include file="cb.panel.footer.tpl"%>
        <% if ($GET.aktion=="showall") %><script>simple_load('ksuche_areaw','<%$PHPSELF%>?epage=<%$epage%>&cmd=ax_search&type=<% $MEMINDEX.settings.filter.type %>');</script><%/if%>
        <% if ($GET.wort!="") %><script>simple_load('ksuche_areaw','<%$PHPSELF%>?epage=<%$epage%>&cmd=ax_search&orderby=<% $GET.orderby %>&direc=<% $GET.direc %>&sword='+$('#wort').val());</script><%/if%>