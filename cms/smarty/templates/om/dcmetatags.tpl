<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/">
<link rel="schema.DCTERMS" href="http://purl.org/dc/terms/">
<% if ($meta.title!="") %><meta name="DC.title" content="<% $meta.title %>"><%/if%>
<% if ($meta.author !="") %><meta name="DC.creator" content="<% $meta.author %>"><%/if%>
<% if ($meta.title !="") %><meta name="DC.subject" content="<% $meta.title %>"><%/if%>
<% if ($meta.description!="") %><meta name="DC.description" content="<% $meta.description%>"><%/if%>
<% if ($meta.publisher !="") %><meta name="DC.publisher" content="<% $meta.publisher %>"><%/if%>
<% if ($meta.contributor !="") %><meta name="DC.contributor" content="<% $meta.contributor %>"><%/if%>
<meta name="DC.type" content="Text" scheme="DCTERMS.DCMIType">
<meta name="DC.format" content="text/html" scheme="DCTERMS.IMT">
<% if ($meta.uri!="") %><meta name="DC.identifier"
      content="<% $meta.uri%>"
      scheme="DCTERMS.URI"><%/if%>
<% if ($meta.uri!="") %><meta name="DC.source"
      content="<% $meta.uri%>"
      scheme="DCTERMS.URI"><%/if%>
<% if ($meta.contentlang !="") %><meta name="DC.language" content="<% $meta.contentlang %>" scheme="DCTERMS.RFC3066"><%/if%>
<% if ($meta.copyright !="") %><meta name="DC.rights" content="<% $meta.copyright %>"><%/if%>
