<?xml version="1.0" encoding="utf-8" ?>

<rss version="2.0" 
   xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
   xmlns:admin="http://webns.net/mvcb/"
   xmlns:dc="http://purl.org/dc/elements/1.1/"
   xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
   xmlns:wfw="http://wellformedweb.org/CommentAPI/"
   xmlns:content="http://purl.org/rss/1.0/modules/content/"
>
<channel>
    <title><%$metadata.title%></title>
    <link><%$metadata.link%></link>
    <description><%$metadata.description%></description>
    <dc:language><%$metadata.language%></dc:language>
<%if $metadata.showMail%>
    <admin:errorReportsTo rdf:resource="mailto:<%$metadata.email%>">
<%/if%>
    <generator>Keimeno <% $KEIMENO_VERSION %> - http://www.trebaxa.com/</generator>
    <image>
      <url><%$metadata.image%></url> 
      <title><%$metadata.title%></title> 
      <link><% $metadata.link %></link> 
      <width>100</width> 
      <height>21</height> 
    </image>    
    <pubdate><%$metadata.pubdate%></pubdate>
<%foreach from=$entries item="entry"%>
<item>
    <title><%$entry.feed_title%></title>
    <link><%$entry.feed_entryLink%></link>
    <author><%$entry.feed_email%> (<%$entry.feed_author%>)</author>
<%if !empty($entry.body)%>
    <content:encoded>
        <%$entry.body%>
    </content:encoded>
<%/if%>

    <pubDate><%$entry.feed_pubdate%></pubDate>
    <guid isPermaLink="false"><%$entry.feed_guid%></guid>
    <%$entry.per_entry_display_dat%>
</item>
<%/foreach%>

</channel>
</rss>
