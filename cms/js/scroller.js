
/* * * * * * * * * * * * * * * * * * D E R  T I C K E R * * * * * * * * * * * * * * * * * * * * * */
// http://www.doktormolle.de/temp/ticker6.htm
    //IE ab V4?
IE=document.all&&!window.opera;
    //DOM-Browser(ausser IE)
DOM=document.getElementById&&!IE;


//läuft ab IE4 und in DOM-Browsern
if(DOM||IE)
  {
        //Ermitteln, ob Ticker horizontal oder vertikal laufen soll
    blnDir=(strDir=='up'||strDir=='down')?true:false;

        //Bei horizontalem Ticker wird ein nobr-, ansonsten ein div-Tag verwendet
    strNobr=(blnDir)?'div':'nobr';

        //Trennzeichen zwischen den Einzelnen Eintraegen
        //bei horizontalem Ticker gemäss Angabe in Variale strDelimiter
        //Ansonsten Zeilenumbrueche
    strDelimiter=(blnDir)?'<br><br>':strDelimiter;

        //String fuer Textausrichtung bei vertikalem Ticker
    strAlign=(blnDir)?'text-align:'+strAlign+';':'';

        //Variable zum Speichern des Intervals
    var objGo;
        //Variable zum Speichern der Position
    intPos=0;

        //String erzeugen fuer JS-Code, falls Ticker beim mouseover stoppen soll
    strStopHover=(blnStopHover)?'onmouseover="clearInterval(objGo)"onmouseout="objGo=setInterval(\'DM_ticken()\','+intInterval+')"':'';

        //Tickertext zu String zusammenfuegen
    strText=(blnDir)?tNews.join(strDelimiter)+strDelimiter:tNews.join(strDelimiter)+strDelimiter;    
    strNews=strText;
    for(i=1;i<intRepeat;++i)
        {
        strNews+=strText;
        }

        //TickerCode zu String zusammenfuegen
    strTicker='<div style="position: relative; '+strAlign+'overflow:hidden;background-color:'+strBgc+
                    ';border:'+strBorder+';width:'+intWidth+';height:'+intHeight+';padding:'+intPadding+
                    'px;"><'+strNobr+'><div id="ticker"style="position:relative;color:'+strTxtc+';background-color:'+strBgc+
                    ';"'+strStopHover+'>'+strNews+'</div></'+strNobr+'></div>';

        //TickerCode im Dokument ausgeben
    document.write(strTicker);

        //Funktion, um Ticker ticken zu lassen
    function DM_ticken()
    {
        //Ticker-Objekt je nach Browser ermitteln
    objTicker=(IE)?document.all.ticker:document.getElementById('ticker');

        //Array fuer zu manipulierende Eigenschaften des Tickers je nach Richtung
        //Richtung=new Array(Pixelwert zur Aenderung der Position,Breite/Höhe des Tickers,zu andernder Positionswert);
    arrDir=new Array();
    arrDir['up']    =new Array(-1,objTicker.offsetHeight,'top');
    arrDir['down']  =new Array(1,objTicker.offsetHeight,'top');
    arrDir['left']  =new Array(1,objTicker.offsetWidth,'left');
    arrDir['right'] =new Array(-1,objTicker.offsetWidth,'left');

        //Ermitteln von Breite bzw. Höhe der anzuzeigenden Items
    dblOffset=arrDir[strDir][1]/intRepeat;

        //Neuen Positionswert ermitteln
    switch(strDir)
        {
        case'right':
            intPos=(Math.abs(intPos)>dblOffset)?0:intPos;break;
        case'left':
            intPos=(intPos>0)?-dblOffset:intPos;break;
        case 'up':
            intPos=(Math.abs(intPos)>dblOffset)?0:intPos;break;
        case 'down':
            intPos=(intPos>0)?-dblOffset:intPos;break;
        }
        //Neuen Positionswert zuweisen
    objTicker.style[arrDir[strDir][2]]=intPos + "px";

        //Positionswert hoch/heruntersetzen
    intPos+=intStep*arrDir[strDir][0];
    }
        //Erneut ticken lassen
    objGo=setInterval('DM_ticken()',intInterval);
  }       
      