<tr class="<%$sclass%>">
<td><img src="<%$looparticle.thumbnail%>" ></td>
                <td><a href="<%$looparticle.link%>" title="<%$looparticle.a_title%>"><%$looparticle.ac_title%></a>
                <br><span class="small">vom <%$looparticle.date%> | Author: <%$looparticle.a_author%></span></td>
                <td style="text-align:right">
                
<% if ($looparticle.AFCOUNT>0) %><img alt="Attachment" title="<%$looparticle.AFCOUNT%> Anh&auml;nge" src="<%$PATH_CMS%>images/attach.png" ><%/if%>
<% if ($cuperm.PERM.edit==TRUE || $looparticle.a_kid==$cuperm.kid ) %>
         <a title="bearbeiten" href="<%$PHPSELF%>?gid=<%$gid%>&artid=<%$looparticle.AID%>&aktion=edit&page=400">
         <img alt="bearbeiten" src="<%$PATH_CMS%>images/page_white_edit.png" title="bearbeiten" ></a>

<a onclick="return confirm('Sind Sie sicher?')" href="<%$PHPSELF%>?gid=<%$gid%>&id=<%$looparticle.AID%>&page=400&aktion=a_delart">
<img src="<%$PATH_CMS%>images/page_delete.png" title="lÃ¶schen"  alt=""></a>
<%/if%> 
<% if ($cuperm.PERM.edit==TRUE ) %>
<a href="<%$PHPSELF%>?gid=<%$gid%>&aktion=a_approve&value=<% if ($looparticle.a_approved==1) %>0<%else%>1<%/if%>&id=<%$looparticle.AID%>&page=400">
<img title="<% if ($looparticle.a_approved!=1) %>nicht <%/if%>verÃ¶ffentlicht" src="<%$PATH_CMS%>images/page_<% if ($looparticle.a_approved!=1) %>not<%/if%>visible.png"  alt=""></a>
<%/if%> 
               </td>                
            </tr>
