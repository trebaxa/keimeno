<% if (count($feedbacks)>0) %>
<table class="tab_std"  width="100%">
  <tbody>
<% foreach from=$feedbacks item=fb_obj %>
<tr><td>
{LBL_ABSENDER} <% $fb_obj.fb_customer %><br>
 {LBL_GESCHRIEBENAM} <% $fb_obj.fb_date %><br>
 <% $fb_obj.fb_text%>
</td></tr>
     <tr>
            <td><hr style="border-right: #cccccc 1px solid; border-top: #cccccc 1px solid; border-left: #cccccc 1px solid; border-bottom: #cccccc 1px solid">
            </td>
           
        </tr>
<% /foreach %>
</tr>
    </tbody>
</table>
<% /if %>