<?PHP

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class pdf_class extends keimeno_class {

    var $pdf_filename = "";
    var $gz_link = "";
    var $year_folder = "";
    var $local_pdf_file = "";
    var $pdf_target_folder = "";
    var $rerender;
    var $result_file;


    /**
     * pdf_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->year_folder = date("Y");
        $this->rerender = FALSE;
    }

    /**
     * pdf_class::tidy_html()
     * 
     * @param mixed $html
     * @return
     */
    function tidy_html($html) {
        return utf8_decode(strval(tidy_page($html)));
    }

    /**
     * pdf_class::compile_to_pdf()
     * 
     * @param mixed $html
     * @param string $filename
     * @return
     */
    function compile_to_pdf($html, $filename = "") {
        global $kdb, $gbl_config, $GBL_LANGID;
        $html = smarty_compile($html);
        $html = pure_translation($html, $GBL_LANGID);
        //$html = utf8_decode($html);
        $html = iconv("UTF-8", "CP1252//TRANSLIT//IGNORE", $html); // kein utf8decode wegen euro zeichen
        if ($filename == "") {
            $htmlfile = CMS_ROOT . CACHE . md5(uniqid(rand(), true));
        }
        else {
            $htmlfile = CMS_ROOT . CACHE . $filename;
        }
        $pdf_filename = basename($htmlfile . '.pdf');
        $htmlfile .= '.html';
        file_put_contents($htmlfile, $html);
        $this->pdf_filename = $pdf_filename;
        $this->pdf_target_folder = CMS_ROOT . CACHE;
        return $htmlfile;
    }

    /**
     * pdf_class::HTML2PDF()
     * 
     * @param mixed $htmlfile
     * @return
     */
    function HTML2PDF($htmlfile) {
        flush();
        if ($this->gbl_config['pdf_generator'] == 'HTMLDOC') {
            file_put_contents($htmlfile, file_get_contents($htmlfile));
            putenv("HTMLDOC_NOCGI=1");
            system("htmldoc -t pdf14 --quiet --footer ... --bottom 0cm --top " . (int)$this->gbl_config['pdf_margin_top'] . "cm --right " . (int)$this->gbl_config['pdf_margin_right'] .
                "cm --left " . (int)$this->gbl_config['pdf_margin_left'] . "cm  --bodyfont " . trim($this->gbl_config['pdf_font']) . " --fontsize " . (int)$this->gbl_config['pdf_font_size'] .
                ".0 --no-links --quiet --webpage -f '" . $this->pdf_filename . "' '" . $htmlfile . "'", $filename);
        }
        else {
            file_put_contents($htmlfile, $this->tidy_html(utf8_encode(file_get_contents($htmlfile))));
            #  echo "wkhtmltopdf --margin-bottom 0cm --margin-right " . (int)$this->gbl_config['pdf_margin_right'] . "cm --margin-top " . (int)$this->gbl_config['pdf_margin_top'] .
            #     "cm --margin-left " . (int)$this->gbl_config['pdf_margin_left'] . "cm --disable-smart-shrinking " . $htmlfile . " " . $this->pdf_filename . "";

            system("wkhtmltopdf --margin-bottom 0cm --margin-right " . (int)$this->gbl_config['pdf_margin_right'] . "cm --margin-top " . (int)$this->gbl_config['pdf_margin_top'] .
                "cm --margin-left " . (int)$this->gbl_config['pdf_margin_left'] . "cm --disable-smart-shrinking " . $htmlfile . " " . $this->pdf_filename . "");
            #  die;
        }
        copy($this->pdf_filename, $this->pdf_target_folder . $this->pdf_filename);
        if (file_exists($this->pdf_filename))
            @unlink($this->pdf_filename);
        $this->result_file = $this->pdf_target_folder . $this->pdf_filename;
        return 'OK';
    }

    /**
     * pdf_class::HTML2PDFonfly()
     * 
     * @param mixed $htmlfile
     * @return
     */
    function HTML2PDFonfly($htmlfile) {
        flush();
        header("Content-Type: application/pdf");
        if ($download == true) {
            header("Content-Disposition: attachment; filename=" . basename($this->pdf_filename));
        }
        else {
            header('Content-Disposition: inline; filename="' . basename($this->pdf_filename) . '"');
        }
        header('Content-Transfer-Encoding: binary');
        #header('Content-Length: ' . filesize($file));
        header('Accept-Ranges: bytes');
        if ($this->gbl_config['pdf_generator'] == 'HTMLDOC') {
            file_put_contents($htmlfile, file_get_contents($htmlfile));
            passthru("htmldoc -t pdf14 --quiet --footer ... --bottom 0cm --top " . (int)$this->gbl_config['pdf_margin_top'] . "cm --right " . (int)$this->gbl_config['pdf_margin_right'] .
                "cm --left " . (int)$this->gbl_config['pdf_margin_left'] . "cm  --bodyfont " . trim($this->gbl_config['pdf_font']) . " --fontsize " . (int)$this->gbl_config['pdf_font_size'] .
                ".0 --no-links --webpage " . $htmlfile);
        }
        else {
            file_put_contents($htmlfile, $this->tidy_html(utf8_encode(file_get_contents($htmlfile))));
            passthru("wkhtmltopdf --margin-bottom 0cm --margin-right " . (int)$this->gbl_config['pdf_margin_right'] . "cm --margin-top " . (int)$this->gbl_config['pdf_margin_top'] .
                "cm --margin-left " . (int)$this->gbl_config['pdf_margin_left'] . "cm --disable-smart-shrinking " . $htmlfile . " -");
        }
        if (file_exists($htmlfile))
            @unlink($htmlfile);
        $this->hard_exit();
    }

    /**
     * pdf_class::save2HTMLCompressed()
     * 
     * @param mixed $html
     * @param mixed $local_file_name
     * @return
     */
    function save2HTMLCompressed($html, $local_file_name) {
        $this->pdf_filename = basename($local_file_name . '.pdf');
        $local_file_name .= '.tmp';
        file_put_contents($local_file_name, $html);
        if (file_exists($local_file_name . '.gz'))
            unlink($local_file_name . '.gz');
        exec("gzip " . $local_file_name);
        $this->gz_link = 'http://www.' . FM_DOMAIN . PATH_SHOP . TMP_WEB . basename($local_file_name) . '.gz';
    }

    /**
     * pdf_class::save2HTML()
     * 
     * @param mixed $html
     * @param mixed $fname
     * @return
     */
    function save2HTML($html, $fname) {
        $html = iconv("UTF-8", "CP1252//TRANSLIT//IGNORE", $html);
        $this->pdf_filename = basename($fname . '.pdf');
        $fname .= '.html';
        file_put_contents($fname, $html);
        return $fname;
    }

    /**
     * pdf_class::createPDFFile()
     * 
     * @param mixed $html
     * @param mixed $local_file_name
     * @return
     */
    function createPDFFile($html, $local_file_name) {
        $htmlfile = $this->save2HTML($html, $local_file_name);
        if (empty($this->pdf_target_folder)) {
            $this->pdf_target_folder = CMS_ROOT . CACHE;
        }
        $this->HTML2PDF($htmlfile);
        return $this->result_file;
    }

    /**
     * pdf_class::htmlfile_to_pdf()
     * 
     * @param mixed $htmlfile
     * @return
     */
    function htmlfile_to_pdf($htmlfile) {
        $this->HTML2PDF($htmlfile);
        return $this->result_file;
    }

    /**
     * pdf_class::html_to_pdf_onfly()
     * 
     * @param mixed $html
     * @param mixed $local_file_name
     * @return
     */
    function html_to_pdf_onfly($html, $local_file_name, $download = true) {
        $htmlfile = $this->save2HTML($html, $local_file_name);
        $this->pdf_target_folder = CMS_ROOT . CACHE;
        $this->HTML2PDFonfly($htmlfile, $download);
    }


}
