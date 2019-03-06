<div class="form-group">
<label>Template:</label>
    <select class="form-control" name="PLUGFORM[tplid]">
        <% foreach from=$WEBSITE.PLUGIN.result.templates item=row %>
            <option <% if ($WEBSITE.node.tm_plugform.tplid==$row.ID) %>selected<%/if%> value="<%$row.ID%>"><%$row.LABEL%></option>
        <%/foreach%>
    </select>
</div>

<div class="form-group">    
<label>Tag Site hinzufügen:</label>
<div class="threecol">
<% foreach from=$WEBSITE.PLUGIN.result.webpages item=row %>
    <div class="checkbox">
        <label>
            <input <% if ($row.ID|inarray:$WEBSITE.node.tm_plugform.pages) %>checked<%/if%> value="<%$row.ID|sthsc%>" type="checkbox" name="PLUGFORM[pages][]"><%$row.LABEL|truncate:60%>
        </label>    
    </div>    
        <%/foreach%>
</div>
</div>    


<label>Optionen:</label>
Hilfe unter: <a href="http://www.goat1000.com/tagcanvas-options.php" target="_blank">hier</a>
<table class="table table-hover">
 <tr>
    <td>Interval</td>
    <td><input type="text" class="form-control jtopt" data-default="20" name="PLUGFORM[tagoptions][interval]" value="<%$WEBSITE.node.tm_plugform.tagoptions.interval|sthsc%>"></td>
 </tr>   
 <tr>
    <td>maxSpeed</td>
    <td><input type="text" class="form-control jtopt" data-default="0.05" name="PLUGFORM[tagoptions][maxSpeed]" value="<%$WEBSITE.node.tm_plugform.tagoptions.maxSpeed|sthsc%>"></td>
 </tr> 
 <tr>
    <td>minSpeed</td>
    <td><input type="text" class="form-control jtopt" data-default="0.0" name="PLUGFORM[tagoptions][minSpeed]" value="<%$WEBSITE.node.tm_plugform.tagoptions.minSpeed|sthsc%>"></td>
 </tr> 

 <tr>
    <td>dragControl</td>
    <td><input type="text" class="form-control jtopt" data-default="false" name="PLUGFORM[tagoptions][dragControl]" value="<%$WEBSITE.node.tm_plugform.tagoptions.dragControl|sthsc%>"></td>
 </tr>   
 
  <tr>
    <td>dragThreshold</td>
    <td><input type="text" class="form-control jtopt" data-default="4" name="PLUGFORM[tagoptions][dragThreshold]" value="<%$WEBSITE.node.tm_plugform.tagoptions.dragThreshold|sthsc%>"></td>
 </tr> 
 
  <tr>
    <td>initial</td>
    <td><input type="text" class="form-control jtopt" data-default="null" name="PLUGFORM[tagoptions][initial]" value="<%$WEBSITE.node.tm_plugform.tagoptions.initial|sthsc%>"></td>
 </tr>  

  <tr>
    <td>fadeIn</td>
    <td><input type="text" class="form-control jtopt" data-default="0" name="PLUGFORM[tagoptions][fadeIn]" value="<%$WEBSITE.node.tm_plugform.tagoptions.fadeIn|sthsc%>"></td>
 </tr> 
 
   <tr>
    <td>decel</td>
    <td><input type="text" class="form-control jtopt" data-default="0.95" name="PLUGFORM[tagoptions][decel]" value="<%$WEBSITE.node.tm_plugform.tagoptions.decel|sthsc%>"></td>
 </tr> 
 
 <tr>
    <td>minBrightness</td>
    <td><input type="text" class="form-control jtopt" data-default="0.1" name="PLUGFORM[tagoptions][minBrightness]" value="<%$WEBSITE.node.tm_plugform.tagoptions.minBrightness|sthsc%>"></td>
 </tr>
 
  <tr>
    <td>maxBrightness</td>
    <td><input type="text" class="form-control jtopt" data-default="1.0" name="PLUGFORM[tagoptions][maxBrightness]" value="<%$WEBSITE.node.tm_plugform.tagoptions.maxBrightness|sthsc%>"></td>
 </tr>   
 
   <tr>
    <td>textColour</td>
    <td><input type="text" class="form-control jtopt" data-default="#ff99ff" name="PLUGFORM[tagoptions][textColour]" value="<%$WEBSITE.node.tm_plugform.tagoptions.textColour|sthsc%>"></td>
 </tr>  
 
    <tr>
    <td>textHeight</td>
    <td><input type="text" class="form-control jtopt" data-default="15" name="PLUGFORM[tagoptions][textHeight]" value="<%$WEBSITE.node.tm_plugform.tagoptions.textHeight|sthsc%>"></td>
 </tr>    
 
     <tr>
    <td>textFont</td>
    <td><input type="text" class="form-control jtopt" data-default="Helvetica, Arial,sans-serif" name="PLUGFORM[tagoptions][textFont]" value="<%$WEBSITE.node.tm_plugform.tagoptions.textFont|sthsc%>"></td>
 </tr>  
 
     <tr>
    <td>outlineColour</td>
    <td><input type="text" class="form-control jtopt" data-default="#ffff99" name="PLUGFORM[tagoptions][outlineColour]" value="<%$WEBSITE.node.tm_plugform.tagoptions.outlineColour|sthsc%>"></td>
 </tr>
 
      <tr>
    <td>outlineMethod</td>
    <td><input type="text" class="form-control jtopt" data-default="outline" name="PLUGFORM[tagoptions][outlineMethod]" value="<%$WEBSITE.node.tm_plugform.tagoptions.outlineMethod|sthsc%>"></td>
 </tr>  
 
 <tr>
    <td>outlineThickness</td>
    <td><input type="text" class="form-control jtopt" data-default="2" name="PLUGFORM[tagoptions][outlineThickness]" value="<%$WEBSITE.node.tm_plugform.tagoptions.outlineThickness|sthsc%>"></td>
 </tr>      
 
  <tr>
    <td>outlineOffset</td>
    <td><input type="text" class="form-control jtopt" data-default="5" name="PLUGFORM[tagoptions][outlineOffset]" value="<%$WEBSITE.node.tm_plugform.tagoptions.outlineOffset|sthsc%>"></td>
 </tr> 
 
 <tr>
    <td>pulsateTo</td>
    <td><input type="text" class="form-control jtopt" data-default="1.0" name="PLUGFORM[tagoptions][pulsateTo]" value="<%$WEBSITE.node.tm_plugform.tagoptions.pulsateTo|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>pulsateTime</td>
    <td><input type="text" class="form-control jtopt" data-default="3" name="PLUGFORM[tagoptions][pulsateTime]" value="<%$WEBSITE.node.tm_plugform.tagoptions.pulsateTime|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>depth</td>
    <td><input type="text" class="form-control jtopt" data-default="0.5" name="PLUGFORM[tagoptions][depth]" value="<%$WEBSITE.node.tm_plugform.tagoptions.depth|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>freezeActive</td>
    <td><input type="text" class="form-control jtopt" data-default="false" name="PLUGFORM[tagoptions][freezeActive]" value="<%$WEBSITE.node.tm_plugform.tagoptions.freezeActive|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>freezeDecel</td>
    <td><input type="text" class="form-control jtopt" data-default="false" name="PLUGFORM[tagoptions][freezeDecel]" value="<%$WEBSITE.node.tm_plugform.tagoptions.freezeDecel|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>activeCursor</td>
    <td><input type="text" class="form-control jtopt" data-default="pointer" name="PLUGFORM[tagoptions][activeCursor]" value="<%$WEBSITE.node.tm_plugform.tagoptions.activeCursor|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>frontSelect</td>
    <td><input type="text" class="form-control jtopt" data-default="false" name="PLUGFORM[tagoptions][frontSelect]" value="<%$WEBSITE.node.tm_plugform.tagoptions.frontSelect|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>clickToFront</td>
    <td><input type="text" class="form-control jtopt" data-default="null" name="PLUGFORM[tagoptions][clickToFront]" value="<%$WEBSITE.node.tm_plugform.tagoptions.clickToFront|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>txtOpt</td>
    <td><input type="text" class="form-control jtopt" data-default="true" name="PLUGFORM[tagoptions][txtOpt]" value="<%$WEBSITE.node.tm_plugform.tagoptions.txtOpt|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>txtScale</td>
    <td><input type="text" class="form-control jtopt" data-default="2" name="PLUGFORM[tagoptions][txtScale]" value="<%$WEBSITE.node.tm_plugform.tagoptions.txtScale|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>reverse</td>
    <td><input type="text" class="form-control jtopt" data-default="false" name="PLUGFORM[tagoptions][reverse]" value="<%$WEBSITE.node.tm_plugform.tagoptions.reverse|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>hideTags</td>
    <td><input type="text" class="form-control jtopt" data-default="true" name="PLUGFORM[tagoptions][hideTags]" value="<%$WEBSITE.node.tm_plugform.tagoptions.hideTags|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>zoom</td>
    <td><input type="text" class="form-control jtopt" data-default="1.0" name="PLUGFORM[tagoptions][zoom]" value="<%$WEBSITE.node.tm_plugform.tagoptions.zoom|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>wheelZoom</td>
    <td><input type="text" class="form-control jtopt" data-default="true" name="PLUGFORM[tagoptions][wheelZoom]" value="<%$WEBSITE.node.tm_plugform.tagoptions.wheelZoom|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>zoomStep</td>
    <td><input type="text" class="form-control jtopt" data-default="0.05" name="PLUGFORM[tagoptions][zoomStep]" value="<%$WEBSITE.node.tm_plugform.tagoptions.zoomStep|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>zoomStep</td>
    <td><input type="text" class="form-control jtopt" data-default="0.05" name="PLUGFORM[tagoptions][zoomStep]" value="<%$WEBSITE.node.tm_plugform.tagoptions.zoomStep|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>zoomMax</td>
    <td><input type="text" class="form-control jtopt" data-default="3.0" name="PLUGFORM[tagoptions][zoomMax]" value="<%$WEBSITE.node.tm_plugform.tagoptions.zoomMax|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>zoomMin</td>
    <td><input type="text" class="form-control jtopt" data-default="0.3" name="PLUGFORM[tagoptions][zoomMin]" value="<%$WEBSITE.node.tm_plugform.tagoptions.zoomMin|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>shadow</td>
    <td><input type="text" class="form-control jtopt" data-default="#000000" name="PLUGFORM[tagoptions][shadow]" value="<%$WEBSITE.node.tm_plugform.tagoptions.shadow|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>shadowBlur</td>
    <td><input type="text" class="form-control jtopt" data-default="0" name="PLUGFORM[tagoptions][shadowBlur]" value="<%$WEBSITE.node.tm_plugform.tagoptions.shadowBlur|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>shadowOffset</td>
    <td><input type="text" class="form-control jtopt" data-default="[0,0]" name="PLUGFORM[tagoptions][shadowOffset]" value="<%$WEBSITE.node.tm_plugform.tagoptions.shadowOffset|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>weight</td>
    <td><input type="text" class="form-control jtopt" data-default="false" name="PLUGFORM[tagoptions][weight]" value="<%$WEBSITE.node.tm_plugform.tagoptions.weight|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>weightMode</td>
    <td><input type="text" class="form-control jtopt" data-default="size" name="PLUGFORM[tagoptions][weightMode]" value="<%$WEBSITE.node.tm_plugform.tagoptions.weightMode|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>weightSize</td>
    <td><input type="text" class="form-control jtopt" data-default="1.0" name="PLUGFORM[tagoptions][weightSize]" value="<%$WEBSITE.node.tm_plugform.tagoptions.weightSize|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>weightGradient</td>
    <td><input type="text" class="form-control jtopt" data-default="{0:'#f00', 0.33:'#ff0',0.66:'#0f0', 1:'#00f'}" name="PLUGFORM[tagoptions][weightGradient]" value="<%$WEBSITE.node.tm_plugform.tagoptions.weightGradient|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>weightFrom</td>
    <td><input type="text" class="form-control jtopt" data-default="null" name="PLUGFORM[tagoptions][weightFrom]" value="<%$WEBSITE.node.tm_plugform.tagoptions.weightFrom|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>weightSizeMin</td>
    <td><input type="text" class="form-control jtopt" data-default="null" name="PLUGFORM[tagoptions][weightSizeMin]" value="<%$WEBSITE.node.tm_plugform.tagoptions.weightSizeMin|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>weightSizeMax</td>
    <td><input type="text" class="form-control jtopt" data-default="null" name="PLUGFORM[tagoptions][weightSizeMax]" value="<%$WEBSITE.node.tm_plugform.tagoptions.weightSizeMax|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>shape</td>
    <td><input type="text" class="form-control jtopt" data-default="sphere" name="PLUGFORM[tagoptions][shape]" value="<%$WEBSITE.node.tm_plugform.tagoptions.shape|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>lock</td>
    <td><input type="text" class="form-control jtopt" data-default="null" name="PLUGFORM[tagoptions][lock]" value="<%$WEBSITE.node.tm_plugform.tagoptions.lock|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>tooltip</td>
    <td><input type="text" class="form-control jtopt" data-default="null" name="PLUGFORM[tagoptions][tooltip]" value="<%$WEBSITE.node.tm_plugform.tagoptions.tooltip|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>tooltipClass</td>
    <td><input type="text" class="form-control jtopt" data-default="tctooltip" name="PLUGFORM[tagoptions][tooltipClass]" value="<%$WEBSITE.node.tm_plugform.tagoptions.tooltipClass|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>tooltipDelay</td>
    <td><input type="text" class="form-control jtopt" data-default="300" name="PLUGFORM[tagoptions][tooltipDelay]" value="<%$WEBSITE.node.tm_plugform.tagoptions.tooltipDelay|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>radiusX</td>
    <td><input type="text" class="form-control jtopt" data-default="1" name="PLUGFORM[tagoptions][radiusX]" value="<%$WEBSITE.node.tm_plugform.tagoptions.radiusX|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>radiusY</td>
    <td><input type="text" class="form-control jtopt" data-default="1" name="PLUGFORM[tagoptions][radiusY]" value="<%$WEBSITE.node.tm_plugform.tagoptions.radiusY|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>radiusZ</td>
    <td><input type="text" class="form-control jtopt" data-default="1" name="PLUGFORM[tagoptions][radiusZ]" value="<%$WEBSITE.node.tm_plugform.tagoptions.radiusZ|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>stretchX</td>
    <td><input type="text" class="form-control jtopt" data-default="1" name="PLUGFORM[tagoptions][stretchX]" value="<%$WEBSITE.node.tm_plugform.tagoptions.stretchX|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>stretchY</td>
    <td><input type="text" class="form-control jtopt" data-default="1" name="PLUGFORM[tagoptions][stretchY]" value="<%$WEBSITE.node.tm_plugform.tagoptions.stretchY|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>offsetX</td>
    <td><input type="text" class="form-control jtopt" data-default="0" name="PLUGFORM[tagoptions][offsetX]" value="<%$WEBSITE.node.tm_plugform.tagoptions.offsetX|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>offsetY</td>
    <td><input type="text" class="form-control jtopt" data-default="0" name="PLUGFORM[tagoptions][offsetY]" value="<%$WEBSITE.node.tm_plugform.tagoptions.offsetY|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>shuffleTags</td>
    <td><input type="text" class="form-control jtopt" data-default="false" name="PLUGFORM[tagoptions][shuffleTags]" value="<%$WEBSITE.node.tm_plugform.tagoptions.shuffleTags|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>noSelect</td>
    <td><input type="text" class="form-control jtopt" data-default="false" name="PLUGFORM[tagoptions][noSelect]" value="<%$WEBSITE.node.tm_plugform.tagoptions.noSelect|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>noMouse</td>
    <td><input type="text" class="form-control jtopt" data-default="false" name="PLUGFORM[tagoptions][noMouse]" value="<%$WEBSITE.node.tm_plugform.tagoptions.noMouse|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>imageScale</td>
    <td><input type="text" class="form-control jtopt" data-default="1" name="PLUGFORM[tagoptions][imageScale]" value="<%$WEBSITE.node.tm_plugform.tagoptions.imageScale|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>centreFunc</td>
    <td><input type="text" class="form-control jtopt" data-default="null" name="PLUGFORM[tagoptions][centreFunc]" value="<%$WEBSITE.node.tm_plugform.tagoptions.centreFunc|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>animTiming</td>
    <td><input type="text" class="form-control jtopt" data-default="Smooth" name="PLUGFORM[tagoptions][animTiming]" value="<%$WEBSITE.node.tm_plugform.tagoptions.animTiming|sthsc%>"></td>
 </tr>
 
 <tr>
    <td>splitWidth</td>
    <td><input type="text" class="form-control jtopt" data-default="0" name="PLUGFORM[tagoptions][splitWidth]" value="<%$WEBSITE.node.tm_plugform.tagoptions.splitWidth|sthsc%>"></td>
 </tr>
 
</table>
  
     
    
<script>
$(".jtopt").each(function() {
        $('<i class="fa fa-tag" style="margin-left:10px">&nbsp;</i><span class="default">' + $(this).data('default') + '</span>').insertAfter($(this));
});
</script>





