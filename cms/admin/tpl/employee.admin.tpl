<%include file="cb.page.title.tpl" icon="fa fa-users" title="{LBL_EMPLOYEE} {LBL_MANAGER}"%>

<% if ($cmd=='') %>
    <%include file="cb.panel.header.tpl" title="Mitarbeiter"%>    
        <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=edit">{LBL_NEW_ADMIN}</a>
        <table class="table table-striped" id="employeetable">
            <thead>
                <tr>
                    <th></th>
                    <th>{LBL_EMPLOYEE}</th>
                    <th>Name</th>
                    <th>{LBL_EMAIL}</th>
                    <th>{LBL_GROUP}</th>
                    <th>{LBL_OPTIONS}</th>
                </tr>
            </thead>  

            <% foreach from=$employees item=emp %>
                <tr>
                    <td><a href="<%$PHPSELF%>?epage=<%$epage%>&id=<%$emp.MID%>&cmd=edit"><img src="<%$emp.thumb%>" class="img-circle"></a></td>
                    <td><a href="<%$PHPSELF%>?epage=<%$epage%>&id=<%$emp.MID%>&cmd=edit"><% $emp.mitarbeiter_name %></a></td>
                    <td><a href="<%$PHPSELF%>?epage=<%$epage%>&id=<%$emp.MID%>&cmd=edit"><% $emp.vorname %> <% $emp.nachname %></a></td>
                    <td><a href="mailto:<% $emp.email %>"><% $emp.email %></a></td>
                    <td><a href="<%$PHPSELF%>?epage=admin_groups.inc&id=<% $emp.gid %>&cmd=edit"><% $emp.mgname %></td>
                    <td class="text-right"><div class="btn-group"><% foreach from=$emp.icons item=picon name=cicons %><% $picon %><%/foreach%></div></td>
                </tr>
            <%/foreach%>        
        </table>
<%include file="cb.panel.footer.tpl"%>
    
<%* Tabellen Sortierungs Script *%>
<%assign var=tablesortid value="employeetable" scope="global"%>
<%include file="table.sorting.script.tpl"%>
<%/if%>

<%if ($cmd=='edit') %>
    <div class="row">
        <div class="col-md-3">        
            <% include file="employee.foto.tpl" %>            
            <% if ($GET.id>0) %>
               <%include file="cb.panel.header.tpl" title="{LBL_EMPLOYEE} {LBL_LANGUAGE} Manager"%>                
                    <form method="post" action="<%$PHPSELF%>" class="jsonform">
                        <% if (count($emplanglist)>0) %>
                            <table class="table table-striped">
                                <thead>
                                    <th>{LBL_LANGUAGE}</th>
                                    <th>Zugriff</th>
                                </thead>
                                <% foreach from=$emplanglist item=lang %>
                                    <tr>
                                        <td><% $lang.post_lang%></td>
                                        <td><input <% if ($lang.id|in_array:$empobjform.lang_id_matrix) %> checked<%/if%> type="checkbox" name="lang_ids[<%$lang.id%>]" value="<%$lang.id%>"></td>
                                    </tr>
                                <% /foreach %>
                            </table>
                        <%/if%>
                        <input type="hidden" name="cmd" value="add_lang_matrix">
                        <input type="hidden" name="epage" value="<%$epage%>">
                        <input type="hidden" name="employee_id" value="<%$GET.id%>">
                        <div class="form-feet"><%$subbtn%></div>
                    </form>
            <%include file="cb.panel.footer.tpl"%>
            <%/if%>
        </div><!-- /.col-md-3 -->
        <div class="col-md-9">
            <% include file="employee.form.regedit.tpl" %>
        </div><!-- /.col-md-9 -->
    </div><!-- /.row -->
    <script>
        function set_emp_id(id,img) {
            $('.empid').val(id);
            if (img!="") {
                $('#empimg').html('<img class="img-thumbnail" src="'+img+'?'+Math.random(1000)+'">');
                $('#delimgbtnpro').fadeIn();
            }
        }
        
        function delete_profil_img() {
            execrequest('<%$PHPSELF%>?epage=<%$epage%>&id=<%$GET.id%>&cmd=delete_profil_img');
            $('#empimg').html('<img class="img-thumbnail" src="/images/opt_member_nopic.jpg">');
            $('#delimgbtnpro').fadeOut();
        }
    </script>
    
<%/if%>

<% if ($cmd=='countryrelated') %>
<h3><% $empobjform.mitarbeiter_name %></h3>
    <form method="post" action="<%$PHPSELF%>">
    <div style="width:600px">
    <fieldset>
    <legend>{LBL_ACCESSTO} {LBL_COUNTRY}</legend>
    {LBL_COUNTRY} <select class="form-control custom-select" onChange="location.href=this.options[this.selectedIndex].value" >
            <option <% if ($continent.id==$GET.continentid) %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&cmd=countryrelated&id=<%$GET.id%>&continentid=0">- please choose -</option>
            <% foreach from=$continents item=continent  %>
            <option <% if ($continent.id==$GET.continentid) %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&cmd=countryrelated&id=<%$GET.id%>&continentid=<% $continent.id %>"><% $continent.lc_name %></option>
            <%/foreach%>
    </select>
    <% if (count($regions_by_continent)>0) %>
    Region: <select class="form-control custom-select" onChange="location.href=this.options[this.selectedIndex].value" >
            <option <% if ($continent.id==$GET.continentid) %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&cmd=countryrelated&id=<%$GET.id%>&regionid=0&continentid=<% $GET.continentid %>">- please choose -</option>
            <% foreach from=$regions_by_continent item=region %>    
            <option <% if ($region.id==$GET.regionid) %>selected<%/if%> value="<%$PHPSELF%>?epage=<%$epage%>&cmd=countryrelated&id=<%$GET.id%>&continentid=<% $GET.continentid %>&regionid=<% $region.id %>"><% $region.lr_name %></option>
            <%/foreach%>
    </select>   
    <%/if%>

<% if ($GET.regionid>0) %>
    <table class="table table-striped table-hover">
    <tbody>
            <% foreach from=$countries_by_region item=country %>
        <tr>
            <td><% $country.land %></td>
            <td>
                <input <% if ($country.id|in_array:$empobjform.country_id_matrix) %> checked<%/if%> type="checkbox" name="country_ids[<%$country.id%>]" value="<%$country.id%>">
            </td>
        </tr>
            <% /foreach %>
            </tbody>
    </table>
        <%/if%>
<% if (count($countrids_by_region)>0 && $GET.regionid>0) %>         
    <% foreach from=$empobjform.country_id_matrix key=cid item=citem %>
    <% if ($cid|in_array:$countrids_by_region) %>
    <%else%>
        <input type="hidden" name="country_ids[<%$cid%>]" value="<%$cid%>">
    <%/if%> 
    <% /foreach %>
    <%/if%>
    <input type="hidden" name="cmd" value="add_country_matrix">
    <input type="hidden" name="epage" value="<%$epage%>">
    <input type="hidden" name="employee_id" value="<%$GET.id%>">
    <input type="hidden" name="regionid" value="<%$GET.regionid%>">
    <input type="hidden" name="continentid" value="<%$GET.continentid%>">
    <% if (count($countrids_by_region)>0) %>    <div class="subright"><%$subbtn%></div> <%/if%>
    
    <% if (count($empobjform.countries)>0) %>
    <h3>{LBL_EMPLOYEE} - {LBL_COUNTRY}</h3>
    <table class="table table-striped table-hover" >
    <tbody>
        <% foreach from=$empobjform.countries key=ck item=continent %>
            <tr class="trsubheader">
                <td><%$continent.lc_name%></td>
            </tr>
            <% foreach from=$continent item=region %>
            <% if (count($region)>1) %>
            <tr class="trsubheader3">
                <td><%$region.lr_name%></td>
            </tr>
            <%/if%>
                    <% foreach from=$region item=country %>
            <% if (count($country)>1) %>
            <tr>
                <td><%$country.land%> [<%$country.country_rel%>]</td>
            </tr>
            <%/if%>
            <% /foreach %>
            <% /foreach %>
        <% /foreach %>
        </tbody>
    </table>    
        <%/if%>
    </fieldset>
    
    </div>
    </form>


<%/if%>