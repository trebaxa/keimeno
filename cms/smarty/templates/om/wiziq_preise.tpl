<h1>Preise</h1>

<table >
 <%foreach from=$WIZIQ.productsbyfb item=product %>
   <tr><td><%$product.pname%></td>
   <td class="tdright"><%$product.product_price%></td>
   </tr>
<%/foreach%>
          
</table>
