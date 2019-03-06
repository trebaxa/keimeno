//
// Kalender Popup 1.1
// Written by KjM <kjm@kjm.hu>
// Download und Dokumentation: http://www.goweb.de/javascriptkalender.htm
// 
//
// Danke an Volker Umpfenbach und Sebastian Ohme für Fehlermeldungen
// und Tipps zur Behebung.
//
// Neu in 1.1
// Berechnung der aktuellen Position auch wenn der User schon gescrollt hat
//
// Das Script kann frei auf jeder Seite verwendet werden
// Ich würde mich sehr über einen Backlink auf www.goweb.de freuen
//
// Shortcut
function gE(d) { return document.getElementById(d); }
//
// Kalender Objekt initialisieren
var Kalender = {

  //
  // Ein Tag hat wieviel Millisekunden?
  //
  oneDay : 86400000,
  destObj: null,
  layout : "%d.%m.%y",
  lastMouseX: 0,
  lastMouseY: 0,

  // Microsoft product
  ismsie: false,

  //
  // Monatsnamen in deutsch
  //
  monate : new Array("Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"),
  mshort : new Array("Jan","Feb","Mär","Apr","Mai","Jun","Jul","Aug","Sep","Okt","Nov","Dez"),

  //
  // Tagesnamen
  //
  weekdays : new Array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag"),

  //
  // Tage pro Monat
  //
  daysinmonth : new Array(31,28,31,30,31,30,31,31,30,31,30,31),

  // 
  // Event programmieren
  //
  eH: function(obj, evType, fn, useCapture) {
    if (obj.addEventListener) {
      obj.addEventListener(evType, fn, useCapture);
      return true;
    } else if (obj.attachEvent) {
      var r = obj.attachEvent('on'+evType,fn);
      return r;
    } else {
      obj['on'+evType] = fn;
    }
  },  

  //
  // Style auf ein Objekt anwenden
  //
  style: function(o, t, v) { eval(o+".style."+t+"='"+v+"';"); },

  //
  // Kalender initialisieren
  //
  init: function() {

    // Kein DOM Support :(
    if (!document.getElementById) return;

    // MSIE?
    if (!window.opera && navigator.userAgent.indexOf("MSIE") !=-1)
      Kalender.ismsie = true;

    // Kalender Objekt in die Seite einfügen
    var b = document.getElementsByTagName("body");
    
    // Fehler auf der Seite - mehr als 1 Body Tag
    if (b.length != 1) return;

    // container_calelemente (DIV) erstellen
    var container_cal = document.createElement("div"); 
    container_cal.id = "container_cal";

    // Kalender erstellen
    var cal = document.createElement("div"); 

    // Header erstellen (DIV)
    var monat = document.createElement("div"); 
    monat.id = "monat";
    var cls   = document.createElement("div"); 

    // Tabelle erstellen
    var tab = document.createElement("table");
    var tb = document.createElement("tbody");
    var tr = document.createElement("tr");
    var td1 = document.createElement("td");
    var td2 = document.createElement("td");
    tr.appendChild(td1); tr.appendChild(td2); 
    tb.appendChild(tr); tab.appendChild(tb); 
    tab.style.background = "#000090";
    if (document.all) tab.style.width = "199px"; 
    else tab.style.width = "200px"; 
    tab.style.border = "0px";
    tab.style.borderRight = "1px solid black";

    // Tagescontainer_cal erstellen
    var days = document.createElement("div"); 

    // Content Element erstellen (DIV)
    var content = document.createElement("div"); 
    content.id = "days";

    // Fenster zusammenfügen und oberstes zurückgeben
    cal.appendChild(tab);
    container_cal.appendChild(cal);

    td2.appendChild(cls); td1.appendChild(monat); 
    cal.appendChild(days); 
    cal.appendChild(content); 

    // Erscheinungsbild definieren
    container_cal.style.zIndex = "9999";
    container_cal.style.padding = "0px";
    container_cal.style.background = "#efefef";
    container_cal.style.width = "205px";
    container_cal.style.top = "100px";
    container_cal.style.left = "30px";
    container_cal.style.position = "absolute";
    container_cal.style.display = "none";
    container_cal.style.borderLeft = "2px solid #f4f4f4";
    container_cal.style.borderTop = "2px solid #f4f4f4";
    container_cal.style.borderRight = "2px solid #2c2c2c";
    container_cal.style.borderBottom = "2px solid #2c2c2c";

    // Kalender Layout
    cal.style.margin = "2px";
    cal.style.borderLeft = "1px solid black";
    cal.style.borderTop = "1px solid black";
    cal.style.borderBottom = "1px solid black";

    // Monatsanzeige
    monat.style.textAlign = "center";
    monat.style.height = "14px";
    monat.style.fontSize = "12px";
    monat.style.color = "white";
    monat.style.background = "#000090";
    monat.style.fontWeight = "bold";
    monat.style.fontFamily = "verdana,arial,sans-serif";

    // Close Option
    cls.style.fontFamily = "verdana,arial,sans-serif";
    cls.style.fontSize = "12px"; 
    cls.style.fontWeight = "bold";
    cls.style.height = "14px"; 
    cls.style.borderRight = "1px solid black";
    cls.style.borderBottom = "1px solid black";
    cls.style.borderLeft = "1px solid #dfdfdf";
    cls.style.borderTop = "1px solid #dfdfdf";
    cls.style.background = "#dfdfdf"; 
    cls.innerHTML = "<a style='text-decoration:none;color:black;' href='JavaScript:Kalender.close()'>&nbsp;X&nbsp;</a>";

    // Wochentage
    days.style.background = "white";
    days.style.borderRight = "1px solid black";
    days.style.borderTop = "1px solid black";
    days.style.fontSize = "12px";
    days.style.width = "199px";
    days.style.fontWeight = "bold";
    days.style.textAlign = "center";
    days.style.fontFamily = "courier new,courier,monospace";
    days.innerHTML = "Son Mon Die Mit Don Fre Sam";

    // Inhalt layouten
    content.style.fontFamily = "courier new,courier,monospace";
    content.style.fontSize = "12px"; 
    content.style.textAlign = "center";
    content.style.width = "199px";
    content.style.borderRight = "1px solid black";
    content.style.borderTop = "1px solid black"; 
    content.style.fontWeight = "bold";
    content.style.background = "#dfdfdf"; 
    content.style.lineHeight = "2.0em";

    // Kalenderobjekt in die Seite einfügen
    b[0].appendChild(container_cal);

    // Aktueller Monat und aktuelles Jahr
    var d = new Date();
    Kalender.curMonat = d.getMonth()+1; 
    Kalender.curJahr = d.getFullYear();

    // Datumsgrenzen
    Kalender.selectionStart = Kalender.selectionEnd = 0;

    // Mausebewegungen abfangen
    Kalender.eH(gE("monat"),'mousedown',Kalender.verschieben,false);

  //  Kalender.eH(document,'mousemove',Kalender.move,false);
  },

  //
  // Kalender nicht länger anzeigen
  //
  close: function() { gE("container_cal").style.display = "none"; },

  //
  // Nächsten Monat anzeigen
  //
  nextMon: function() {
    if (Kalender.curMonat == 12) {
      Kalender.curMonat = 1; Kalender.curJahr++;
    } else Kalender.curMonat++;
    Kalender.anzeige();
  },

  //
  // Vorheriger Monat anzeigen
  //
  prevMon: function() {
    if (Kalender.curMonat == 1) {
      Kalender.curMonat = 12; Kalender.curJahr--;
    } else Kalender.curMonat--;
    Kalender.anzeige();
  },

  //
  // Datum in das entsprechende Objekt einfügen
  //
  setzen: function(ts) {
    var d = new Date(ts);
    if (Kalender.destObj) {
      var m = d.getMonth()+1; var y = d.getDate();
      if (m<10) m = "0"+m; if (y<10) y = "0"+y;
      var z = gE(Kalender.destObj);

      // Layoutstring erzeugen
      var l = Kalender.layout;
      l = l.replace(/%d/g,y);
      l = l.replace(/%m/g,m);
      l = l.replace(/%b/g,Kalender.mshort[d.getMonth()]);
      l = l.replace(/%B/g,Kalender.monate[d.getMonth()]);
      l = l.replace(/%y/g,d.getFullYear());
      l = l.replace(/%a/g,Kalender.weekdays[d.getDay()]);

      z.value = l;
    }

    // Kalender schliessen
    Kalender.close();
  },

  //
  // Kalender für einen bestimmten Monat anzeigen
  // Wenn monat / jahr nicht angegeben wird, wird das jeweils aktuelle genommen
  // obj ist das Objekt in welches später das gewählte Datum geschrieben wird
  // pdays versteht sich als Startoffset für gültige Tage ab dem aktuellen
  // tdays ist der Endoffset für gültige Tage ab dem aktuellen
  //
  anzeige: function(monat, jahr, obj, pdays, tdays, layout) {

    // Monat & Jahr sind angegeben und Monat ist zwischen 1 und 12?
    if ((monat == null) || (jahr == null)) {
      monat = Kalender.curMonat; jahr = Kalender.curJahr;
    }

    // Datumslayout zuweisen
    if (layout) Kalender.layout = layout;

    // Scrollposition auslesen
    if (Kalender.ismsie) {

      // ab MSIE 6
      if (document.documentElement && document.documentElement.scrollTop) {
        var yFromTop = document.documentElement.scrollTop;
      } else {
        var yFromTop = document.body.scrollTop;
      }
    } else if (self.pageYOffset) {
      var yFromTop = self.pageYOffset;
    } else { var yFromTop = 0; }

    // Zielobjekt setzen
    if (obj) {
      Kalender.destObj = obj;

      // container_cal genau auf die Mausposition setzen
      var c = gE("container_cal");
      c.style.left = Kalender.lastMouseX + "px"; 
      c.style.top =  (yFromTop+Kalender.lastMouseY) + "px";
    }

    // Monat ist gueltig?
    if ((isNaN(parseInt(monat))) || ((monat < 1) || (monat > 12))) return;

    // Monat & Jahr setzen
    Kalender.curJahr = jahr; Kalender.curMonat = monat;

    // Monat und Jahr inkl. Links einblenden
    gE("monat").innerHTML = "<a style='text-decoration: none; color: white;' href='JavaScript:Kalender.prevMon()'>&#171;</a> &nbsp;"+Kalender.monate[monat-1]+", "+jahr+"&nbsp; <a style='text-decoration: none; color: white;' href='JavaScript:Kalender.nextMon()'>&#187;</a>";

    // Zeitgrenzen setzen
    if (pdays != null) {
      var h = new Date();
      var n = new Date(h.getFullYear(),h.getMonth(),h.getDate(),0,0,1);
      Kalender.selectionStart = n.getTime()+(Kalender.oneDay*pdays);
      Kalender.selectionEnd = ((tdays == null)||(tdays == 0))?0:tdays;
    }

    // Datumsobjekt initialisieren
    var d = new Date(jahr,monat-1,1,6,0,1); var n = d.getTime(); 
    var f = n; 
    var t = (Kalender.selectionEnd != 0)?Kalender.selectionStart+Kalender.oneDay*Kalender.selectionEnd:0;

    // Tage in den Kalender einfügen
    var o = ""; var j = 1; var l = 0;
    for (var i = 1; i <= d.getDay(); i++) {
      o+= "&nbsp;&nbsp;&nbsp; "; j++;
    }
    o += "<span style='color: #bcbcbc'>";
    var dim = Kalender.daysinmonth[monat-1];

    // Schaltjahr?
    if (dim == 2) {
      if (jahr % 4   == 0) dim++;
      if (jahr % 100 == 0) dim--;
      if (jahr % 400 == 0) dim++;
    }
    for (i = 1; i <= dim; i++) {

      // Datum gültig ab?
      if ((f) && (f >= Kalender.selectionStart)) {
        f = 0; o += "</span>"; l = 1;
      }

      // Datum gültig bis?
      if ((t>0) && (n >= t)) {
        t = -1; l = 0; o += "<span style='color: #bcbcbc'>"; 
      }
      
      // Link einfügen
      if (l) o += "<a style='color: #2c2c2c;text-decoration:none;font-family:courier new,courier,monospace;font-size:11px;' href='JavaScript:Kalender.setzen("+n+")'>";

      // Datum setzen
      o += (i<10)?"&nbsp;":""; o+= i+"&nbsp;"; j++; 
      o += (l)?"</a>":"";
      n+=Kalender.oneDay; f += (f)?Kalender.oneDay:0;
      if (j == 8) {
        j = 1; o += "<br>";
      } else o += " ";
    }
    if (j!=1) for (i = j; i <= 8; i++) o+= "&nbsp;&nbsp;&nbsp; ";
    else o += "<br>";
    if (t == -1) o += "</span>";

    // Daten anzeigen
    gE("days").innerHTML = o;

    // Kalender anzeigen
    gE("container_cal").style.display = "block";    
  },

  //
  // X Position innerhalb eines Objektes finden
  //
  findPos: function(o,x) {
    var l = 0;
    if (o.offsetParent) {
      do {
        l += (x)?o.offsetLeft:o.offsetTop;
      } while (o = o.offsetParent);
    } else if (o.x) {
      l += (x)?o.x:o.y;
    }

    // Position innerhalb des Objektes übergeben
    return l;
  },

  //
  // Kalender wird in der Seite verschoben
  //
  verschieben: function(e) {

    // Daten korrigieren
    e = Kalender.chkEvH(e);

    // Target Element holen
    var t = e.target?e.target:e.srcElement;
    t.style.cursor = "move";

    Kalender.obj = gE("monat");
    Kalender.obj.clickAtX = e.clientX - Kalender.findPos(t,1);
    Kalender.obj.clickAtY = e.clientY - Kalender.findPos(t,0);

    // Mausebewegungen verfolgen
    Kalender.eH(document,'mouseup',Kalender.stop,false);
  },

  //
  // Maus wird bewegt
  //
  move: function(e) {

    // Event zuordnen
    e = Kalender.chkEvH(e);

    // X und Y Position zuweisen
    var x = e.clientX; var y = e.clientY;

    // Objekt holen
    var o = Kalender.obj; if (o == null) {
      Kalender.lastMouseX = x; Kalender.lastMouseY = y;
      return false;
    }

    // Kalenderposition ermitteln
    var kx = o.style.top;
    var ky = o.style.left;

    // Daten nun verarbeiten und Objekt bewegen
    gE("container_cal").style.left = (x-o.clickAtX) + "px";
    gE("container_cal").style.top  = (y-o.clickAtY) + "px";
    o.lastMouseX = x; o.lastMouseY = y;
    return false;
  },

  //
  // Mausbutton wurde losgelassen
  //
  stop: function(e) {
    
    // Eventhandler loeschen
    gE("monat").style.cursor = "auto";
    Kalender.obj = null;
  },

  //
  // DIV Position übergeben
  //
  chkEvH: function(e) {
    if (typeof e == 'undefined') e = window.event;
    if (typeof e.layerX == 'undefined') e.layerX = e.offsetX;
    if (typeof e.layerY == 'undefined') e.layerY = e.offsetY;
    return e;
  }
};

//
// Initialisierung durchführen wenn die Seite geladen wurde
//
Kalender.eH(window,'load',Kalender.init,false);