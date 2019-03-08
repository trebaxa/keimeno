<?php

/**
 * @package    gearth
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

class gearth_admin_class extends keimeno_class
{
    var $kml_body = "";
    var $kml_header = "";
    var $kml_footer = "";
    var $xml_file = "";

    /**
     * gearth_admin_class::__construct()
     * 
     * @return
     */
    function __construct()
    {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->kml_theme_name = $this->get_domain_name_pure();
        $this->xml_file = CMS_ROOT . $this->kml_theme_name . '.kml';
        $this->xml_sitemap_file = CMS_ROOT . $this->kml_theme_name . '_geo_location.xml';
        $this->sys_path = PATH_CMS;
        $this->kml_header = '<?xml version="1.0" encoding="UTF-8"?>
	<kml xmlns="http://earth.google.com/kml/2.0">
	<Document>
	<name>' . $this->kml_theme_name . '</name>';
        $this->kml_header .= "	";
        $this->kml_footer .= '
		</Document>
</kml>';
        $this->load_file();
    }


    /**
     * gearth_admin_class::load_file()
     * 
     * @return
     */
    function load_file()
    {
        $xml_array = array();
        if (file_exists($this->xml_file)) {
            $xml_array = $this->parse_xmlfile_to_array($this->xml_file);
        }
        $coords = $xml_array['http://earth.google.com/kml/2.0']['DOCUMENT']['PLACEMARK']['POINT']['COORDINATES'];
        list($lon, $lat, $alt) = explode(',', $coords);
        $KML_OBJ = array(
            'xml_array' => $xml_array['http://earth.google.com/kml/2.0']['DOCUMENT'],
            'framelink' => 'https://www.trebaxa.com/gmgen.php?width=100%&zoom=9&point=' . $coords .
                '&address=' . $xml_array['http://earth.google.com/kml/2.0']['DOCUMENT']['PLACEMARK']['NAME'],
            'gm_frame' => '<iframe class="gkmlframe" marginwidth="0" marginheight="0" src="https://www.trebaxa.com/gmgen.php?height=500px&width=100%&zoom=9&point=' .
                $coords . '&address=' . $xml_array['http://earth.google.com/kml/2.0']['DOCUMENT']['PLACEMARK']['NAME'] .
                '" frame scrolling="no"></iframe>',
            'link' => 'http://www.' . FM_DOMAIN . $this->sys_path . basename($this->
                xml_file),
            'sitemaplink' => 'http://www.' . FM_DOMAIN . $this->sys_path . basename($this->
                xml_sitemap_file),
            'coords' => array(
                'lon' => ($lon * 1),
                'lat' => ($lat * 1),
                'alt' => ($alt * 1)));
        $this->smarty->assign('KML_OBJ', $KML_OBJ);
    }

    /**
     * gearth_admin_class::cmd_gen_kml()
     * 
     * @return
     */
    function cmd_gen_kml()
    {
        $FORM = $_POST['FORM'];
        foreach ($FORM as $key => $value)
            $FORM[$key] = trim($value);
        $this->add_point($FORM['lon'], $FORM['lat'], $FORM['alt'], $FORM['title'], $FORM['description'],
            $FORM['sLayer']);
        $this->store_to_file();
        $this->echo_json_fb('reloadkml');
    }

    /**
     * gearth_admin_class::addElement()
     * 
     * @param mixed $kml_element
     * @return
     */
    function addElement($kml_element)
    {
        $this->kml_body .= $kml_element;
    }


    /**
     * gearth_admin_class::export_and_view()
     * 
     * @return
     */
    function export_and_view()
    {
        header('Content-type: application/keyhole');
        header('Content-Disposition:atachment; filename="' . $this->kml_theme_name .
            '.kml"');
        $sKml = $this->kml_header . $this->kml_body . $this->kml_footer;
        header('Content-Length: ' . strlen($sKml));
        header('Expires: 0');
        header('Pragma: cache');
        header('Cache-Control: private');
        echo $sKml;
        die;
    }

    /**
     * gearth_admin_class::store_to_file()
     * 
     * @return
     */
    function store_to_file()
    {
        $this->gen_geo_sitemap();
        file_put_contents($this->xml_file, $this->kml_header . $this->kml_body . $this->
            kml_footer);
    }

    /**
     * gearth_admin_class::gen_geo_sitemap()
     * 
     * @return
     */
    function gen_geo_sitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
	<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:geo="http://www.google.com/geo/schemas/sitemap/1.0">
		<url>
		<loc>http://www.' . FM_DOMAIN . $this->sys_path . basename($this->xml_file) .
            '</loc>
		<geo:geo>
			<geo:format>kml</geo:format>
		</geo:geo>
		</url>
	</urlset>';
        file_put_contents($this->xml_sitemap_file, $xml);
    }

    /**
     * gearth_admin_class::cmd_open_and_view()
     * 
     * @return
     */
    function cmd_open_and_view()
    {
        if (file_exists($this->xml_file)) {
            $sKML = file_get_contents($this->xml_file);
            header('Content-Type: application/vnd.google-earth.kml+xml\n');
            header('Content-Disposition:atachment; filename="' . $this->kml_theme_name .
                '.kml"');
            header('Content-Length: ' . strlen($sKML));
            header("Expires: 0");
            header('Pragma: no-cache');
            header("Cache-Control: no-cache, must-revalidate");
            echo $sKML;
        } else {
            echo 'no generarted XML file. Please create file.';
        }
        die;
    }

    # POINT latitude,longitude,altitude
    /**
     * gearth_admin_class::add_point()
     * 
     * @param mixed $lon
     * @param mixed $lat
     * @param mixed $alt
     * @param mixed $tit
     * @param mixed $des
     * @param string $sLayer
     * @return
     */
    function add_point($lon, $lat, $alt, $tit, $des, $sLayer = '')
    {
        $kml_xml = '
<Placemark>
	<description><![CDATA[' . $des . ']]></description>
	<name>' . strip_tags($tit) . '</name>
	<visibility>1</visibility>
	<styleUrl>#' . $sLayer . '</styleUrl>
	<Point>
			<coordinates>' . ($lon * 1) . ',' . ($lat * 1) . ',' . ($alt * 1) .
            '</coordinates>
		</Point>
	</Placemark>';
        $this->addElement($kml_xml);
    }

    #LINE points array(lat,lon,alt)
    /**
     * gearth_admin_class::addLine()
     * 
     * @param mixed $points
     * @param mixed $tit
     * @param mixed $des
     * @param string $sLayer
     * @return
     */
    function addLine($points, $tit, $des, $sLayer = '')
    {
        $kml_xml = '<Placemark>
	<name>' . $tit . '</name>
	<description>' . $des . '</description>
	<styleUrl>#' . $sLayer . '</styleUrl>
	<LineString>
	<tessellate>1</tessellate>
	<coordinates>';
        $primero = true;
        foreach ($points as $key => $punto) {
            if ($primero) {
                $kml_xml .= $punto['lon'] . "," . $punto['lat'] . "," . $punto['alt'];
                $primero = false;
            } else
                $kml_xml .= " " . $punto['lon'] . "," . $punto['lat'] . "," . $punto['alt'];
        }
        $kml_xml .= '</coordinates>
	</LineString>
	</Placemark>';
        $this->addElement($kml_xml);
    }

    #LINE Polygon array of array(lat,lon,alt)
    /**
     * gearth_admin_class::addPolygon()
     * 
     * @param mixed $points
     * @param mixed $tit
     * @param mixed $des
     * @param string $sLayer
     * @return
     */
    function addPolygon($points, $tit, $des, $sLayer = '')
    {
        $kml_xml = "<Placemark>";
        $kml_xml .= "<name>$tit</name>";
        $kml_xml .= "<styleUrl>#$sLayer</styleUrl>";
        $kml_xml .= "<Polygon>";
        $kml_xml .= "<tessellate>1</tessellate>";
        $kml_xml .= '<outerBoundaryIs>
	<LinearRing>
	<coordinates>
	';
        $primero = true;
        foreach ($points as $key => $punto) {
            if ($primero) {
                $kml_xml .= $punto['lon'] . "," . $punto['lat'] . "," . $punto['alt'];
                $primero = false;
            } else
                $kml_xml .= " " . $punto['lon'] . "," . $punto['lat'] . "," . $punto['alt'];
        }
        $kml_xml .= '</coordinates>
	</LinearRing>
	</outerBoundaryIs>
	</Polygon>
	</Placemark>
	';
        $this->addElement($kml_xml);
    }


    /**
     * gearth_admin_class::addLink()
     * 
     * @return
     */
    function addLink()
    {
        $kml_xml = '
<NetworkLink>
	<name>' . $this->get_domain_name() . ' KML File</name>
	<Url>
	<href>http://www.' . FM_DOMAIN . $this->sys_path . basename($this->xml_file) .
            '</href>
	<refreshMode>onInterval</refreshMode>
	<viewRefreshMode>onRequest</viewRefreshMode>
	</Url>
</NetworkLink>';
        //echo $kml_xml;
        $this->addElement($kml_xml);
    }

    /**
     * gearth_admin_class::addScreenOverlay()
     * 
     * @param mixed $link
     * @param mixed $tit
     * @return
     */
    function addScreenOverlay($link, $tit)
    {
        $aScript = explode('/', $_SERVER[SCRIPT_NAME]);
        array_pop($aScript);
        $sScript = implode('/', $aScript);
        $sLink = "http://" . $_SERVER[SERVER_NAME] . "/" . $sScript . "/$link";
        $kml_xml = "<ScreenOverlay>";
        $kml_xml .= "<name>$tit</name>";
        $kml_xml .= "<Icon>
	<href>$sLink</href>
	</Icon>
	<overlayXY x=\"1\" y=\"1\" xunits=\"fraction\" yunits=\"fraction\"/>
	<screenXY x=\"1\" y=\"1\" xunits=\"fraction\" yunits=\"fraction\"/>
	<rotationXY x=\"0\" y=\"0\" xunits=\"fraction\" yunits=\"fraction\"/>
	<size x=\"0.1\" y=\"0.1\" xunits=\"fraction\" yunits=\"fraction\"/>
	</ScreenOverlay>";
        //echo $kml_xml;
        $this->addElement($kml_xml);
    }
}

?>