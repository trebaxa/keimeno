<?PHP
$version = '1.0.2.1';

$css = file_get_contents(CMS_ROOT . 'layout.css');
if (!strstr($css,'fieldset.error ')) {
	$css.='
fieldset.error {
 border:   1px solid #ff0000;
 background-color:#FFE3E3;
 display:   block;
 padding: 1em 2em;
 clear:   both; 
 margin:  5px 0 10px 0;
 -moz-border-radius: 6px;
 -webkit-border-radius: 6px;
}

legend {
 font-weight: bold;
 color:#ff0000;
}

label {
 clear: left;
 float: left;
 display: block;
 font-weight: bold;
}

	';	
}
if (!strstr($css,'.searchrow,')) {
	$css.='
.searchrow, #indexsearch {
 float:left;
 width:100%;
 margin-bottom:10px;
}

#indexsearch searchrow a, #indexsearch  h1 {
 color:#1111CC;
 text-decoration:underline;
}

#indexsearch  h1 {
 font-size:12pt;
 margin:0;
 padding.0;
}

#indexsearch h1 a {
 color:#1111CC;
}

.searchrow a.url {
 color:#0E776C;
}
';
}

file_put_contents(CMS_ROOT . 'layout.css', $css);

$this->changeKoll();
$this->delDirWithSubDirs(CMS_ROOT . 'admin/fckeditor'); 

$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 1,`transland` = '1|Deutschland;2|Germany;3|Deutschland;4|Deutschland;5|Deutschland;6|Deutschland;7|Deutschland' WHERE  ".TBL_CMS_LAND.".`id` = 1");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 2,`transland` = '1|Schweiz;2|Switzerland;3|Schweiz;4|Schweiz;5|Schweiz;6|Schweiz;7|Schweiz' WHERE  ".TBL_CMS_LAND.".`id` = 2");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 11,`transland` = '1|Belgien;2|Belgium;3|Belgien;4|Belgien;5|Belgien;6|Belgien;7|Belgien' WHERE  ".TBL_CMS_LAND.".`id` = 11");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 12,`transland` = '1|Frankreich;2|France;3|Frankreich;4|Frankreich;5|Frankreich;6|Frankreich;7|Frankreich' WHERE  ".TBL_CMS_LAND.".`id` = 12");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 13,`transland` = '1|Litauen;2|Lithuania;3|Litauen;4|Litauen;5|Litauen;6|Litauen;7|Litauen' WHERE  ".TBL_CMS_LAND.".`id` = 13");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 14,`transland` = '1|Österreich;2|Austria;3|Österreich;4|Österreich;5|Österreich;6|Österreich;7|Österreich' WHERE  ".TBL_CMS_LAND.".`id` = 14");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 15,`transland` = '1|Spanien;2|Spain;3|Spanien;4|Spanien;5|Spanien;6|Spanien;7|Spanien' WHERE  ".TBL_CMS_LAND.".`id` = 15");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 16,`transland` = '1|Dänemark;2|Denmark;3|Dänemark;4|Dänemark;5|Dänemark;6|Dänemark;7|Dänemark' WHERE  ".TBL_CMS_LAND.".`id` = 16");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 17,`transland` = '1|Griechenland;2|Greece;3|Griechenland;4|Griechenland;5|Griechenland;6|Griechenland;7|Griechenland' WHERE  ".TBL_CMS_LAND.".`id` = 17");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 18,`transland` = '1|Luxemburg;2|Luxembourg;3|Luxemburg;4|Luxemburg;5|Luxemburg;6|Luxemburg;7|Luxemburg' WHERE  ".TBL_CMS_LAND.".`id` = 18");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 19,`transland` = '1|Portugal;2|Portugal;3|Portugal;4|Portugal;5|Portugal;6|Portugal;7|Portugal' WHERE  ".TBL_CMS_LAND.".`id` = 19");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 20,`transland` = '1|Tschechische Republik;2|Czech Republic;3|Tschechische Republik;4|Tschechische Republik;5|Tschechische Republik;6|Tschechische Republik;7|Tschechische Republik' WHERE  ".TBL_CMS_LAND.".`id` = 20");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 21,`transland` = '1|Irland;2|Ireland;3|Irland;4|Irland;5|Irland;6|Irland;7|Irland' WHERE  ".TBL_CMS_LAND.".`id` = 21");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 22,`transland` = '1|Malta;2|Malta;3|Malta;4|Malta;5|Malta;6|Malta;7|Malta' WHERE  ".TBL_CMS_LAND.".`id` = 22");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 23,`transland` = '1|Schweden;2|Swedes;3|Schweden;4|Schweden;5|Schweden;6|Schweden;7|Schweden' WHERE  ".TBL_CMS_LAND.".`id` = 23");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 24,`transland` = '1|Ungarn;2|Hungarians;3|Ungarn;4|Ungarn;5|Ungarn;6|Ungarn;7|Ungarn' WHERE  ".TBL_CMS_LAND.".`id` = 24");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 25,`transland` = '1|Estland;2|Estonia;3|Estland;4|Estland;5|Estland;6|Estland;7|Estland' WHERE  ".TBL_CMS_LAND.".`id` = 25");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 26,`transland` = '1|Italien;2|Italy;3|Italien;4|Italien;5|Italien;6|Italien;7|Italien' WHERE  ".TBL_CMS_LAND.".`id` = 26");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 27,`transland` = '1|Niederlande;2|Netherlands;3|Niederlande;4|Niederlande;5|Niederlande;6|Niederlande;7|Niederlande' WHERE  ".TBL_CMS_LAND.".`id` = 27");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 28,`transland` = '1|Slowakei;2|Slovakia;3|Slowakei;4|Slowakei;5|Slowakei;6|Slowakei;7|Slowakei' WHERE  ".TBL_CMS_LAND.".`id` = 28");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 29,`transland` = '1|Vereinigtes Königreich;2|United Kingdom;3|Vereinigtes Königreich;4|Vereinigtes Königreich;5|Vereinigtes Königreich;6|Vereinigtes Königreich;7|Vereinigtes Königreich' WHERE  ".TBL_CMS_LAND.".`id` = 29");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 30,`transland` = '1|Finland;2|Finland;3|Finland;4|Finland;5|Finland;6|Finland;7|Finland' WHERE  ".TBL_CMS_LAND.".`id` = 30");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 31,`transland` = '1|Lettland;2|Latvia;3|Lettland;4|Lettland;5|Lettland;6|Lettland;7|Lettland' WHERE  ".TBL_CMS_LAND.".`id` = 31");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 32,`transland` = '1|Polen;2|Poles;3|Polen;4|Polen;5|Polen;6|Polen;7|Polen' WHERE  ".TBL_CMS_LAND.".`id` = 32");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 33,`transland` = '1|Slowenien;2|Slovenia;3|Slowenien;4|Slowenien;5|Slowenien;6|Slowenien;7|Slowenien' WHERE  ".TBL_CMS_LAND.".`id` = 33");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 34,`transland` = '1|Zypern;2|Cyprus;3|Zypern;4|Zypern;5|Zypern;6|Zypern;7|Zypern' WHERE  ".TBL_CMS_LAND.".`id` = 34");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 35,`transland` = '1|USA;2|USA;3|USA;4|USA;5|USA;6|USA;7|USA' WHERE  ".TBL_CMS_LAND.".`id` = 35");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 127,`transland` = '1|Afghanistan;2|Afghanistan;3|Afghanistan;4|Afghanistan;5|Afghanistan;6|Afghanistan;7|Afghanistan' WHERE  ".TBL_CMS_LAND.".`id` = 127");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 128,`transland` = '1|Albania;2|Albania;3|Albania;4|Albania;5|Albania;6|Albania;7|Albania' WHERE  ".TBL_CMS_LAND.".`id` = 128");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 129,`transland` = '1|Algeria;2|Algeria;3|Algeria;4|Algeria;5|Algeria;6|Algeria;7|Algeria' WHERE  ".TBL_CMS_LAND.".`id` = 129");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 130,`transland` = '1|American Samoa;2|American of Samoa;3|American Samoa;4|American Samoa;5|American Samoa;6|American Samoa;7|American Samoa' WHERE  ".TBL_CMS_LAND.".`id` = 130");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 131,`transland` = '1|Andorra;2|Andorra;3|Andorra;4|Andorra;5|Andorra;6|Andorra;7|Andorra' WHERE  ".TBL_CMS_LAND.".`id` = 131");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 132,`transland` = '1|Angola;2|Angola;3|Angola;4|Angola;5|Angola;6|Angola;7|Angola' WHERE  ".TBL_CMS_LAND.".`id` = 132");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 133,`transland` = '1|Anguilla;2|Anguilla;3|Anguilla;4|Anguilla;5|Anguilla;6|Anguilla;7|Anguilla' WHERE  ".TBL_CMS_LAND.".`id` = 133");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 134,`transland` = '1|Antarctica;2|Antarctica;3|Antarctica;4|Antarctica;5|Antarctica;6|Antarctica;7|Antarctica' WHERE  ".TBL_CMS_LAND.".`id` = 134");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 135,`transland` = '1|Antigua and Barbuda;2|Antigua and Barbuda;3|Antigua and Barbuda;4|Antigua and Barbuda;5|Antigua and Barbuda;6|Antigua and Barbuda;7|Antigua and Barbuda' WHERE  ".TBL_CMS_LAND.".`id` = 135");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 136,`transland` = '1|Argentina;2|Argentina;3|Argentina;4|Argentina;5|Argentina;6|Argentina;7|Argentina' WHERE  ".TBL_CMS_LAND.".`id` = 136");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 137,`transland` = '1|Armenia;2|Armenia;3|Armenia;4|Armenia;5|Armenia;6|Armenia;7|Armenia' WHERE  ".TBL_CMS_LAND.".`id` = 137");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 138,`transland` = '1|Aruba;2|Aruba;3|Aruba;4|Aruba;5|Aruba;6|Aruba;7|Aruba' WHERE  ".TBL_CMS_LAND.".`id` = 138");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 139,`transland` = '1|Australia;2|Australia;3|Australia;4|Australia;5|Australia;6|Australia;7|Australia' WHERE  ".TBL_CMS_LAND.".`id` = 139");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 140,`transland` = '1|Azerbaijan;2|Azerbaijan;3|Azerbaijan;4|Azerbaijan;5|Azerbaijan;6|Azerbaijan;7|Azerbaijan' WHERE  ".TBL_CMS_LAND.".`id` = 140");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 141,`transland` = '1|Bahamas;2|Bahamas;3|Bahamas;4|Bahamas;5|Bahamas;6|Bahamas;7|Bahamas' WHERE  ".TBL_CMS_LAND.".`id` = 141");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 142,`transland` = '1|Bahrain;2|Bahrain;3|Bahrain;4|Bahrain;5|Bahrain;6|Bahrain;7|Bahrain' WHERE  ".TBL_CMS_LAND.".`id` = 142");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 143,`transland` = '1|Bangladesh;2|Bangladesh;3|Bangladesh;4|Bangladesh;5|Bangladesh;6|Bangladesh;7|Bangladesh' WHERE  ".TBL_CMS_LAND.".`id` = 143");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 144,`transland` = '1|Barbados;2|Barbados;3|Barbados;4|Barbados;5|Barbados;6|Barbados;7|Barbados' WHERE  ".TBL_CMS_LAND.".`id` = 144");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 145,`transland` = '1|Belarus;2|Belarus;3|Belarus;4|Belarus;5|Belarus;6|Belarus;7|Belarus' WHERE  ".TBL_CMS_LAND.".`id` = 145");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 146,`transland` = '1|Belize;2|Belize;3|Belize;4|Belize;5|Belize;6|Belize;7|Belize' WHERE  ".TBL_CMS_LAND.".`id` = 146");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 147,`transland` = '1|Benin;2|Benin;3|Benin;4|Benin;5|Benin;6|Benin;7|Benin' WHERE  ".TBL_CMS_LAND.".`id` = 147");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 148,`transland` = '1|Bermuda;2|the Bermudas;3|Bermuda;4|Bermuda;5|Bermuda;6|Bermuda;7|Bermuda' WHERE  ".TBL_CMS_LAND.".`id` = 148");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 149,`transland` = '1|Bhutan;2|Bhutan;3|Bhutan;4|Bhutan;5|Bhutan;6|Bhutan;7|Bhutan' WHERE  ".TBL_CMS_LAND.".`id` = 149");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 150,`transland` = '1|Bolivia;2|Bolivia;3|Bolivia;4|Bolivia;5|Bolivia;6|Bolivia;7|Bolivia' WHERE  ".TBL_CMS_LAND.".`id` = 150");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 151,`transland` = '1|Bosnia and Herzegowina;2|Bosnia and Herzegovina;3|Bosnia and Herzegowina;4|Bosnia and Herzegowina;5|Bosnia and Herzegowina;6|Bosnia and Herzegowina;7|Bosnia and Herzegowina' WHERE  ".TBL_CMS_LAND.".`id` = 151");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 152,`transland` = '1|Botswana;2|Botswana;3|Botswana;4|Botswana;5|Botswana;6|Botswana;7|Botswana' WHERE  ".TBL_CMS_LAND.".`id` = 152");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 153,`transland` = '1|Bouvet Island;2|Bouvet of Iceland;3|Bouvet Island;4|Bouvet Island;5|Bouvet Island;6|Bouvet Island;7|Bouvet Island' WHERE  ".TBL_CMS_LAND.".`id` = 153");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 154,`transland` = '1|Brazil;2|Brazil;3|Brazil;4|Brazil;5|Brazil;6|Brazil;7|Brazil' WHERE  ".TBL_CMS_LAND.".`id` = 154");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 155,`transland` = '1|British Indian Ocean Territory;2|British Indian Ocean Territory;3|British Indian Ocean Territory;4|British Indian Ocean Territory;5|British Indian Ocean Territory;6|British Indian Ocean Territory;7|British Indian Ocean Territory' WHERE  ".TBL_CMS_LAND.".`id` = 155");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 156,`transland` = '1|Brunei Darussalam;2|Brunei Darussalam;3|Brunei Darussalam;4|Brunei Darussalam;5|Brunei Darussalam;6|Brunei Darussalam;7|Brunei Darussalam' WHERE  ".TBL_CMS_LAND.".`id` = 156");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 157,`transland` = '1|Bulgaria;2|Bulgaria;3|Bulgaria;4|Bulgaria;5|Bulgaria;6|Bulgaria;7|Bulgaria' WHERE  ".TBL_CMS_LAND.".`id` = 157");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 158,`transland` = '1|Burkina Faso;2|Burkina Faso;3|Burkina Faso;4|Burkina Faso;5|Burkina Faso;6|Burkina Faso;7|Burkina Faso' WHERE  ".TBL_CMS_LAND.".`id` = 158");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 159,`transland` = '1|Burundi;2|Burundi;3|Burundi;4|Burundi;5|Burundi;6|Burundi;7|Burundi' WHERE  ".TBL_CMS_LAND.".`id` = 159");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 160,`transland` = '1|Cambodia;2|Cambodia;3|Cambodia;4|Cambodia;5|Cambodia;6|Cambodia;7|Cambodia' WHERE  ".TBL_CMS_LAND.".`id` = 160");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 161,`transland` = '1|Cameroon;2|Cameroon;3|Cameroon;4|Cameroon;5|Cameroon;6|Cameroon;7|Cameroon' WHERE  ".TBL_CMS_LAND.".`id` = 161");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 162,`transland` = '1|Canada;2|Canada;3|Canada;4|Canada;5|Canada;6|Canada;7|Canada' WHERE  ".TBL_CMS_LAND.".`id` = 162");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 163,`transland` = '1|Cape Verde;2|Chad;3|Cape Verde;4|Cape Verde;5|Cape Verde;6|Cape Verde;7|Cape Verde' WHERE  ".TBL_CMS_LAND.".`id` = 163");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 164,`transland` = '1|Cayman Islands;2|Cayman Islands;3|Cayman Islands;4|Cayman Islands;5|Cayman Islands;6|Cayman Islands;7|Cayman Islands' WHERE  ".TBL_CMS_LAND.".`id` = 164");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 165,`transland` = '1|Central African Republic;2|Central African Republic;3|Central African Republic;4|Central African Republic;5|Central African Republic;6|Central African Republic;7|Central African Republic' WHERE  ".TBL_CMS_LAND.".`id` = 165");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 166,`transland` = '1|Chad;2|Chad;3|Chad;4|Chad;5|Chad;6|Chad;7|Chad' WHERE  ".TBL_CMS_LAND.".`id` = 166");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 167,`transland` = '1|Chile;2|Chile;3|Chile;4|Chile;5|Chile;6|Chile;7|Chile' WHERE  ".TBL_CMS_LAND.".`id` = 167");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 168,`transland` = '1|China;2|China;3|China;4|China;5|China;6|China;7|China' WHERE  ".TBL_CMS_LAND.".`id` = 168");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 169,`transland` = '1|Christmas Island;2|Christmas Iceland;3|Christmas Island;4|Christmas Island;5|Christmas Island;6|Christmas Island;7|Christmas Island' WHERE  ".TBL_CMS_LAND.".`id` = 169");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 170,`transland` = '1|Cocos (Keeling) Islands;2|Cocos (Keeling) of Iceland;3|Cocos (Keeling) Islands;4|Cocos (Keeling) Islands;5|Cocos (Keeling) Islands;6|Cocos (Keeling) Islands;7|Cocos (Keeling) Islands' WHERE  ".TBL_CMS_LAND.".`id` = 170");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 171,`transland` = '1|Colombia;2|Colombia;3|Colombia;4|Colombia;5|Colombia;6|Colombia;7|Colombia' WHERE  ".TBL_CMS_LAND.".`id` = 171");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 172,`transland` = '1|Comoros;2|Comoros;3|Comoros;4|Comoros;5|Comoros;6|Comoros;7|Comoros' WHERE  ".TBL_CMS_LAND.".`id` = 172");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 173,`transland` = '1|Congo;2|Congo;3|Congo;4|Congo;5|Congo;6|Congo;7|Congo' WHERE  ".TBL_CMS_LAND.".`id` = 173");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 174,`transland` = '1|Cook Islands;2|Cook of Iceland;3|Cook Islands;4|Cook Islands;5|Cook Islands;6|Cook Islands;7|Cook Islands' WHERE  ".TBL_CMS_LAND.".`id` = 174");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 175,`transland` = '1|Costa Rica;2|Costa Rica;3|Costa Rica;4|Costa Rica;5|Costa Rica;6|Costa Rica;7|Costa Rica' WHERE  ".TBL_CMS_LAND.".`id` = 175");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 176,`transland` = '1|Cote DIvoire;2|Cote DIvoire;3|Cote DIvoire;4|Cote DIvoire;5|Cote DIvoire;6|Cote DIvoire;7|Cote DIvoire' WHERE  ".TBL_CMS_LAND.".`id` = 176");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 177,`transland` = '1|Croatia;2|Croatia;3|Croatia;4|Croatia;5|Croatia;6|Croatia;7|Croatia' WHERE  ".TBL_CMS_LAND.".`id` = 177");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 178,`transland` = '1|Cuba;2|Cuba;3|Cuba;4|Cuba;5|Cuba;6|Cuba;7|Cuba' WHERE  ".TBL_CMS_LAND.".`id` = 178");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 179,`transland` = '1|Djibouti;2|Djibouti;3|Djibouti;4|Djibouti;5|Djibouti;6|Djibouti;7|Djibouti' WHERE  ".TBL_CMS_LAND.".`id` = 179");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 180,`transland` = '1|Dominica;2|Dominica;3|Dominica;4|Dominica;5|Dominica;6|Dominica;7|Dominica' WHERE  ".TBL_CMS_LAND.".`id` = 180");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 181,`transland` = '1|Dominican Republic;2|Dominican Republic;3|Dominican Republic;4|Dominican Republic;5|Dominican Republic;6|Dominican Republic;7|Dominican Republic' WHERE  ".TBL_CMS_LAND.".`id` = 181");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 182,`transland` = '1|East Timor;2|East of Timor;3|East Timor;4|East Timor;5|East Timor;6|East Timor;7|East Timor' WHERE  ".TBL_CMS_LAND.".`id` = 182");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 183,`transland` = '1|Ecuador;2|Ecuador;3|Ecuador;4|Ecuador;5|Ecuador;6|Ecuador;7|Ecuador' WHERE  ".TBL_CMS_LAND.".`id` = 183");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 184,`transland` = '1|Egypt;2|Egypt;3|Egypt;4|Egypt;5|Egypt;6|Egypt;7|Egypt' WHERE  ".TBL_CMS_LAND.".`id` = 184");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 185,`transland` = '1|El Salvador;2|El Salvador;3|El Salvador;4|El Salvador;5|El Salvador;6|El Salvador;7|El Salvador' WHERE  ".TBL_CMS_LAND.".`id` = 185");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 186,`transland` = '1|Equatorial Guinea;2|Equatorial of Guinea;3|Equatorial Guinea;4|Equatorial Guinea;5|Equatorial Guinea;6|Equatorial Guinea;7|Equatorial Guinea' WHERE  ".TBL_CMS_LAND.".`id` = 186");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 187,`transland` = '1|Eritrea;2|Eritrea;3|Eritrea;4|Eritrea;5|Eritrea;6|Eritrea;7|Eritrea' WHERE  ".TBL_CMS_LAND.".`id` = 187");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 188,`transland` = '1|Estonia;2|Estonia;3|Estonia;4|Estonia;5|Estonia;6|Estonia;7|Estonia' WHERE  ".TBL_CMS_LAND.".`id` = 188");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 189,`transland` = '1|Ethiopia;2|Ethiopia;3|Ethiopia;4|Ethiopia;5|Ethiopia;6|Ethiopia;7|Ethiopia' WHERE  ".TBL_CMS_LAND.".`id` = 189");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 190,`transland` = '1|Falkland Islands (Malvinas);2|Falkland of Iceland (Malvinas);3|Falkland Islands (Malvinas);4|Falkland Islands (Malvinas);5|Falkland Islands (Malvinas);6|Falkland Islands (Malvinas);7|Falkland Islands (Malvinas)' WHERE  ".TBL_CMS_LAND.".`id` = 190");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 191,`transland` = '1|Faroe Islands;2|Faroe of Iceland;3|Faroe Islands;4|Faroe Islands;5|Faroe Islands;6|Faroe Islands;7|Faroe Islands' WHERE  ".TBL_CMS_LAND.".`id` = 191");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 192,`transland` = '1|Fiji;2|Fiji;3|Fiji;4|Fiji;5|Fiji;6|Fiji;7|Fiji' WHERE  ".TBL_CMS_LAND.".`id` = 192");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 193,`transland` = '1|France, Metropolitan;2|France, Metropolitan;3|France, Metropolitan;4|France, Metropolitan;5|France, Metropolitan;6|France, Metropolitan;7|France, Metropolitan' WHERE  ".TBL_CMS_LAND.".`id` = 193");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 194,`transland` = '1|French Guiana;2|French Guiana;3|French Guiana;4|French Guiana;5|French Guiana;6|French Guiana;7|French Guiana' WHERE  ".TBL_CMS_LAND.".`id` = 194");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 195,`transland` = '1|French Polynesia;2|French Polynesia;3|French Polynesia;4|French Polynesia;5|French Polynesia;6|French Polynesia;7|French Polynesia' WHERE  ".TBL_CMS_LAND.".`id` = 195");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 196,`transland` = '1|French Southern Territories;2|French Southern Territories;3|French Southern Territories;4|French Southern Territories;5|French Southern Territories;6|French Southern Territories;7|French Southern Territories' WHERE  ".TBL_CMS_LAND.".`id` = 196");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 197,`transland` = '1|Gabon;2|Gabon;3|Gabon;4|Gabon;5|Gabon;6|Gabon;7|Gabon' WHERE  ".TBL_CMS_LAND.".`id` = 197");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 198,`transland` = '1|Gambia;2|Greenland;3|Gambia;4|Gambia;5|Gambia;6|Gambia;7|Gambia' WHERE  ".TBL_CMS_LAND.".`id` = 198");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 199,`transland` = '1|Georgia;2|Georgia;3|Georgia;4|Georgia;5|Georgia;6|Georgia;7|Georgia' WHERE  ".TBL_CMS_LAND.".`id` = 199");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 200,`transland` = '1|Ghana;2|Ghana;3|Ghana;4|Ghana;5|Ghana;6|Ghana;7|Ghana' WHERE  ".TBL_CMS_LAND.".`id` = 200");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 201,`transland` = '1|Gibraltar;2|Gibraltar;3|Gibraltar;4|Gibraltar;5|Gibraltar;6|Gibraltar;7|Gibraltar' WHERE  ".TBL_CMS_LAND.".`id` = 201");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 202,`transland` = '1|Greenland;2|Greenland;3|Greenland;4|Greenland;5|Greenland;6|Greenland;7|Greenland' WHERE  ".TBL_CMS_LAND.".`id` = 202");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 203,`transland` = '1|Grenada;2|Grenada;3|Grenada;4|Grenada;5|Grenada;6|Grenada;7|Grenada' WHERE  ".TBL_CMS_LAND.".`id` = 203");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 204,`transland` = '1|Guadeloupe;2|Guadeloupe;3|Guadeloupe;4|Guadeloupe;5|Guadeloupe;6|Guadeloupe;7|Guadeloupe' WHERE  ".TBL_CMS_LAND.".`id` = 204");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 205,`transland` = '1|Guam;2|Guam;3|Guam;4|Guam;5|Guam;6|Guam;7|Guam' WHERE  ".TBL_CMS_LAND.".`id` = 205");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 206,`transland` = '1|Guatemala;2|Guatemala;3|Guatemala;4|Guatemala;5|Guatemala;6|Guatemala;7|Guatemala' WHERE  ".TBL_CMS_LAND.".`id` = 206");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 207,`transland` = '1|Guinea;2|Guinea;3|Guinea;4|Guinea;5|Guinea;6|Guinea;7|Guinea' WHERE  ".TBL_CMS_LAND.".`id` = 207");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 208,`transland` = '1|Guinea-bissau;2|Guinea-Bissau;3|Guinea-bissau;4|Guinea-bissau;5|Guinea-bissau;6|Guinea-bissau;7|Guinea-bissau' WHERE  ".TBL_CMS_LAND.".`id` = 208");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 209,`transland` = '1|Guyana;2|Guyana;3|Guyana;4|Guyana;5|Guyana;6|Guyana;7|Guyana' WHERE  ".TBL_CMS_LAND.".`id` = 209");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 210,`transland` = '1|Haiti;2|Haiti;3|Haiti;4|Haiti;5|Haiti;6|Haiti;7|Haiti' WHERE  ".TBL_CMS_LAND.".`id` = 210");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 211,`transland` = '1|Heard and Mc Donald Islands;2|Heard and Mc of Donald Islands;3|Heard and Mc Donald Islands;4|Heard and Mc Donald Islands;5|Heard and Mc Donald Islands;6|Heard and Mc Donald Islands;7|Heard and Mc Donald Islands' WHERE  ".TBL_CMS_LAND.".`id` = 211");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 212,`transland` = '1|Honduras;2|Honduras;3|Honduras;4|Honduras;5|Honduras;6|Honduras;7|Honduras' WHERE  ".TBL_CMS_LAND.".`id` = 212");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 213,`transland` = '1|Hong Kong;2|Hong Kong;3|Hong Kong;4|Hong Kong;5|Hong Kong;6|Hong Kong;7|Hong Kong' WHERE  ".TBL_CMS_LAND.".`id` = 213");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 214,`transland` = '1|Iceland;2|Iceland;3|Iceland;4|Iceland;5|Iceland;6|Iceland;7|Iceland' WHERE  ".TBL_CMS_LAND.".`id` = 214");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 215,`transland` = '1|India;2|India;3|India;4|India;5|India;6|India;7|India' WHERE  ".TBL_CMS_LAND.".`id` = 215");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 216,`transland` = '1|Indonesia;2|Indonesia;3|Indonesia;4|Indonesia;5|Indonesia;6|Indonesia;7|Indonesia' WHERE  ".TBL_CMS_LAND.".`id` = 216");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 217,`transland` = '1|Iran (Islamic Republic of);2|Iran (Islamic Republic of);3|Iran (Islamic Republic of);4|Iran (Islamic Republic of);5|Iran (Islamic Republic of);6|Iran (Islamic Republic of);7|Iran (Islamic Republic of)' WHERE  ".TBL_CMS_LAND.".`id` = 217");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 218,`transland` = '1|Iraq;2|Iraq;3|Iraq;4|Iraq;5|Iraq;6|Iraq;7|Iraq' WHERE  ".TBL_CMS_LAND.".`id` = 218");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 219,`transland` = '1|Israel;2|Israel;3|Israel;4|Israel;5|Israel;6|Israel;7|Israel' WHERE  ".TBL_CMS_LAND.".`id` = 219");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 220,`transland` = '1|Jamaica;2|Jamaica;3|Jamaica;4|Jamaica;5|Jamaica;6|Jamaica;7|Jamaica' WHERE  ".TBL_CMS_LAND.".`id` = 220");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 221,`transland` = '1|Japan;2|Japan;3|Japan;4|Japan;5|Japan;6|Japan;7|Japan' WHERE  ".TBL_CMS_LAND.".`id` = 221");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 222,`transland` = '1|Jordan;2|Jordan;3|Jordan;4|Jordan;5|Jordan;6|Jordan;7|Jordan' WHERE  ".TBL_CMS_LAND.".`id` = 222");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 223,`transland` = '1|Kazakhstan;2|Kazakhstan;3|Kazakhstan;4|Kazakhstan;5|Kazakhstan;6|Kazakhstan;7|Kazakhstan' WHERE  ".TBL_CMS_LAND.".`id` = 223");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 224,`transland` = '1|Kenya;2|Kenya;3|Kenya;4|Kenya;5|Kenya;6|Kenya;7|Kenya' WHERE  ".TBL_CMS_LAND.".`id` = 224");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 225,`transland` = '1|Kiribati;2|Kiribati;3|Kiribati;4|Kiribati;5|Kiribati;6|Kiribati;7|Kiribati' WHERE  ".TBL_CMS_LAND.".`id` = 225");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 226,`transland` = '1|Korea, Democratic Peoples Rep;2|Korea, Democratic Peoples Rep;3|Korea, Democratic Peoples Rep;4|Korea, Democratic Peoples Rep;5|Korea, Democratic Peoples Rep;6|Korea, Democratic Peoples Rep;7|Korea, Democratic Peoples Rep' WHERE  ".TBL_CMS_LAND.".`id` = 226");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 227,`transland` = '1|Korea, Republic of;2|Korea, Republic of;3|Korea, Republic of;4|Korea, Republic of;5|Korea, Republic of;6|Korea, Republic of;7|Korea, Republic of' WHERE  ".TBL_CMS_LAND.".`id` = 227");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 228,`transland` = '1|Kuwait;2|Kuweit;3|Kuwait;4|Kuwait;5|Kuwait;6|Kuwait;7|Kuwait' WHERE  ".TBL_CMS_LAND.".`id` = 228");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 229,`transland` = '1|Kyrgyzstan;2|Kyrgyzstan;3|Kyrgyzstan;4|Kyrgyzstan;5|Kyrgyzstan;6|Kyrgyzstan;7|Kyrgyzstan' WHERE  ".TBL_CMS_LAND.".`id` = 229");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 230,`transland` = '1|Lao Peoples Democratic Republ;2|Lao Peoples Democratic Republ;3|Lao Peoples Democratic Republ;4|Lao Peoples Democratic Republ;5|Lao Peoples Democratic Republ;6|Lao Peoples Democratic Republ;7|Lao Peoples Democratic Republ' WHERE  ".TBL_CMS_LAND.".`id` = 230");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 231,`transland` = '1|Lebanon;2|Lebanon;3|Lebanon;4|Lebanon;5|Lebanon;6|Lebanon;7|Lebanon' WHERE  ".TBL_CMS_LAND.".`id` = 231");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 232,`transland` = '1|Lesotho;2|Lesotho;3|Lesotho;4|Lesotho;5|Lesotho;6|Lesotho;7|Lesotho' WHERE  ".TBL_CMS_LAND.".`id` = 232");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 233,`transland` = '1|Liberia;2|Liberia;3|Liberia;4|Liberia;5|Liberia;6|Liberia;7|Liberia' WHERE  ".TBL_CMS_LAND.".`id` = 233");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 234,`transland` = '1|Libyan Arab Jamahiriya;2|Libyan Arab The moulders Yugoslav;3|Libyan Arab Jamahiriya;4|Libyan Arab Jamahiriya;5|Libyan Arab Jamahiriya;6|Libyan Arab Jamahiriya;7|Libyan Arab Jamahiriya' WHERE  ".TBL_CMS_LAND.".`id` = 234");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 235,`transland` = '1|Lichtenstein;2|Lichtenstein;3|Lichtenstein;4|Lichtenstein;5|Lichtenstein;6|Lichtenstein;7|Lichtenstein' WHERE  ".TBL_CMS_LAND.".`id` = 235");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 236,`transland` = '1|Macau;2|Macau;3|Macau;4|Macau;5|Macau;6|Macau;7|Macau' WHERE  ".TBL_CMS_LAND.".`id` = 236");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 237,`transland` = '1|Macedonia, The Former Yugoslav;2|Macedonia, The Former Yugoslav;3|Macedonia, The Former Yugoslav;4|Macedonia, The Former Yugoslav;5|Macedonia, The Former Yugoslav;6|Macedonia, The Former Yugoslav;7|Macedonia, The Former Yugoslav' WHERE  ".TBL_CMS_LAND.".`id` = 237");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 238,`transland` = '1|Madagascar;2|Madagascar;3|Madagascar;4|Madagascar;5|Madagascar;6|Madagascar;7|Madagascar' WHERE  ".TBL_CMS_LAND.".`id` = 238");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 239,`transland` = '1|Malawi;2|Malawi;3|Malawi;4|Malawi;5|Malawi;6|Malawi;7|Malawi' WHERE  ".TBL_CMS_LAND.".`id` = 239");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 240,`transland` = '1|Malaysia;2|Malaysia;3|Malaysia;4|Malaysia;5|Malaysia;6|Malaysia;7|Malaysia' WHERE  ".TBL_CMS_LAND.".`id` = 240");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 241,`transland` = '1|Maldives;2|Maldives;3|Maldives;4|Maldives;5|Maldives;6|Maldives;7|Maldives' WHERE  ".TBL_CMS_LAND.".`id` = 241");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 242,`transland` = '1|Mali;2|Mali;3|Mali;4|Mali;5|Mali;6|Mali;7|Mali' WHERE  ".TBL_CMS_LAND.".`id` = 242");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 243,`transland` = '1|Marshall Islands;2|Marshall of Iceland;3|Marshall Islands;4|Marshall Islands;5|Marshall Islands;6|Marshall Islands;7|Marshall Islands' WHERE  ".TBL_CMS_LAND.".`id` = 243");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 244,`transland` = '1|Martinique;2|Martinique;3|Martinique;4|Martinique;5|Martinique;6|Martinique;7|Martinique' WHERE  ".TBL_CMS_LAND.".`id` = 244");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 245,`transland` = '1|Mauritania;2|Mauritania;3|Mauritania;4|Mauritania;5|Mauritania;6|Mauritania;7|Mauritania' WHERE  ".TBL_CMS_LAND.".`id` = 245");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 246,`transland` = '1|Mauritius;2|Mauritius;3|Mauritius;4|Mauritius;5|Mauritius;6|Mauritius;7|Mauritius' WHERE  ".TBL_CMS_LAND.".`id` = 246");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 247,`transland` = '1|Mayotte;2|Mayotte;3|Mayotte;4|Mayotte;5|Mayotte;6|Mayotte;7|Mayotte' WHERE  ".TBL_CMS_LAND.".`id` = 247");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 248,`transland` = '1|Mexico;2|Mexico;3|Mexico;4|Mexico;5|Mexico;6|Mexico;7|Mexico' WHERE  ".TBL_CMS_LAND.".`id` = 248");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 249,`transland` = '1|Micronesia, Federated States o;2|Micronesia, Federated States o;3|Micronesia, Federated States o;4|Micronesia, Federated States o;5|Micronesia, Federated States o;6|Micronesia, Federated States o;7|Micronesia, Federated States o' WHERE  ".TBL_CMS_LAND.".`id` = 249");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 250,`transland` = '1|Moldova, Republic of;2|Moldova, Republic of;3|Moldova, Republic of;4|Moldova, Republic of;5|Moldova, Republic of;6|Moldova, Republic of;7|Moldova, Republic of' WHERE  ".TBL_CMS_LAND.".`id` = 250");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 251,`transland` = '1|Monaco;2|Monaco;3|Monaco;4|Monaco;5|Monaco;6|Monaco;7|Monaco' WHERE  ".TBL_CMS_LAND.".`id` = 251");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 252,`transland` = '1|Mongolia;2|Mongolia;3|Mongolia;4|Mongolia;5|Mongolia;6|Mongolia;7|Mongolia' WHERE  ".TBL_CMS_LAND.".`id` = 252");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 253,`transland` = '1|Montserrat;2|Montserrat;3|Montserrat;4|Montserrat;5|Montserrat;6|Montserrat;7|Montserrat' WHERE  ".TBL_CMS_LAND.".`id` = 253");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 254,`transland` = '1|Morocco;2|Morocco;3|Morocco;4|Morocco;5|Morocco;6|Morocco;7|Morocco' WHERE  ".TBL_CMS_LAND.".`id` = 254");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 255,`transland` = '1|Mozambique;2|Mozambique;3|Mozambique;4|Mozambique;5|Mozambique;6|Mozambique;7|Mozambique' WHERE  ".TBL_CMS_LAND.".`id` = 255");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 256,`transland` = '1|Myanmar;2|Myanmar;3|Myanmar;4|Myanmar;5|Myanmar;6|Myanmar;7|Myanmar' WHERE  ".TBL_CMS_LAND.".`id` = 256");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 257,`transland` = '1|Namibia;2|Namibia;3|Namibia;4|Namibia;5|Namibia;6|Namibia;7|Namibia' WHERE  ".TBL_CMS_LAND.".`id` = 257");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 258,`transland` = '1|Nauru;2|Nauru;3|Nauru;4|Nauru;5|Nauru;6|Nauru;7|Nauru' WHERE  ".TBL_CMS_LAND.".`id` = 258");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 259,`transland` = '1|Nepal;2|Nepal;3|Nepal;4|Nepal;5|Nepal;6|Nepal;7|Nepal' WHERE  ".TBL_CMS_LAND.".`id` = 259");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 260,`transland` = '1|Netherlands Antilles;2|Netherlands Antilles;3|Netherlands Antilles;4|Netherlands Antilles;5|Netherlands Antilles;6|Netherlands Antilles;7|Netherlands Antilles' WHERE  ".TBL_CMS_LAND.".`id` = 260");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 261,`transland` = '1|New Caledonia;2|New Caledonia;3|New Caledonia;4|New Caledonia;5|New Caledonia;6|New Caledonia;7|New Caledonia' WHERE  ".TBL_CMS_LAND.".`id` = 261");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 262,`transland` = '1|New Zealand;2|New Zealand;3|New Zealand;4|New Zealand;5|New Zealand;6|New Zealand;7|New Zealand' WHERE  ".TBL_CMS_LAND.".`id` = 262");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 263,`transland` = '1|Nicaragua;2|Nicaragua;3|Nicaragua;4|Nicaragua;5|Nicaragua;6|Nicaragua;7|Nicaragua' WHERE  ".TBL_CMS_LAND.".`id` = 263");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 264,`transland` = '1|Niger;2|Niger;3|Niger;4|Niger;5|Niger;6|Niger;7|Niger' WHERE  ".TBL_CMS_LAND.".`id` = 264");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 265,`transland` = '1|Nigeria;2|Nigeria;3|Nigeria;4|Nigeria;5|Nigeria;6|Nigeria;7|Nigeria' WHERE  ".TBL_CMS_LAND.".`id` = 265");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 266,`transland` = '1|Niue;2|Niue;3|Niue;4|Niue;5|Niue;6|Niue;7|Niue' WHERE  ".TBL_CMS_LAND.".`id` = 266");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 267,`transland` = '1|Norfolk Island;2|Norfolk of Iceland;3|Norfolk Island;4|Norfolk Island;5|Norfolk Island;6|Norfolk Island;7|Norfolk Island' WHERE  ".TBL_CMS_LAND.".`id` = 267");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 268,`transland` = '1|Northern Mariana Islands;2|Northern Mariana Islands;3|Northern Mariana Islands;4|Northern Mariana Islands;5|Northern Mariana Islands;6|Northern Mariana Islands;7|Northern Mariana Islands' WHERE  ".TBL_CMS_LAND.".`id` = 268");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 269,`transland` = '1|Norway;2|Norway;3|Norway;4|Norway;5|Norway;6|Norway;7|Norway' WHERE  ".TBL_CMS_LAND.".`id` = 269");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 270,`transland` = '1|Oman;2|Papua New of Guinea;3|Oman;4|Oman;5|Oman;6|Oman;7|Oman' WHERE  ".TBL_CMS_LAND.".`id` = 270");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 271,`transland` = '1|Pakistan;2|Pakistan;3|Pakistan;4|Pakistan;5|Pakistan;6|Pakistan;7|Pakistan' WHERE  ".TBL_CMS_LAND.".`id` = 271");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 272,`transland` = '1|Palau;2|Palau;3|Palau;4|Palau;5|Palau;6|Palau;7|Palau' WHERE  ".TBL_CMS_LAND.".`id` = 272");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 273,`transland` = '1|Panama;2|Panama;3|Panama;4|Panama;5|Panama;6|Panama;7|Panama' WHERE  ".TBL_CMS_LAND.".`id` = 273");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 274,`transland` = '1|Papua New Guinea;2|Papua New Guinea;3|Papua New Guinea;4|Papua New Guinea;5|Papua New Guinea;6|Papua New Guinea;7|Papua New Guinea' WHERE  ".TBL_CMS_LAND.".`id` = 274");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 275,`transland` = '1|Paraguay;2|Paraguay;3|Paraguay;4|Paraguay;5|Paraguay;6|Paraguay;7|Paraguay' WHERE  ".TBL_CMS_LAND.".`id` = 275");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 276,`transland` = '1|Peru;2|Peru;3|Peru;4|Peru;5|Peru;6|Peru;7|Peru' WHERE  ".TBL_CMS_LAND.".`id` = 276");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 277,`transland` = '1|Philippines;2|Philippines;3|Philippines;4|Philippines;5|Philippines;6|Philippines;7|Philippines' WHERE  ".TBL_CMS_LAND.".`id` = 277");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 278,`transland` = '1|Pitcairn;2|Pitcairn;3|Pitcairn;4|Pitcairn;5|Pitcairn;6|Pitcairn;7|Pitcairn' WHERE  ".TBL_CMS_LAND.".`id` = 278");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 279,`transland` = '1|Puerto Rico;2|Puerto Rico;3|Puerto Rico;4|Puerto Rico;5|Puerto Rico;6|Puerto Rico;7|Puerto Rico' WHERE  ".TBL_CMS_LAND.".`id` = 279");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 280,`transland` = '1|Qatar;2|Qatar;3|Qatar;4|Qatar;5|Qatar;6|Qatar;7|Qatar' WHERE  ".TBL_CMS_LAND.".`id` = 280");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 281,`transland` = '1|Reunion;2|Reunion;3|Reunion;4|Reunion;5|Reunion;6|Reunion;7|Reunion' WHERE  ".TBL_CMS_LAND.".`id` = 281");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 282,`transland` = '1|Romania;2|Romania;3|Romania;4|Romania;5|Romania;6|Romania;7|Romania' WHERE  ".TBL_CMS_LAND.".`id` = 282");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 283,`transland` = '1|Russian Federation;2|Russian Federation;3|Russian Federation;4|Russian Federation;5|Russian Federation;6|Russian Federation;7|Russian Federation' WHERE  ".TBL_CMS_LAND.".`id` = 283");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 284,`transland` = '1|Rwanda;2|Rwanda;3|Rwanda;4|Rwanda;5|Rwanda;6|Rwanda;7|Rwanda' WHERE  ".TBL_CMS_LAND.".`id` = 284");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 285,`transland` = '1|Saint Kitts and Nevis;2|Saint of putty and Nevis;3|Saint Kitts and Nevis;4|Saint Kitts and Nevis;5|Saint Kitts and Nevis;6|Saint Kitts and Nevis;7|Saint Kitts and Nevis' WHERE  ".TBL_CMS_LAND.".`id` = 285");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 286,`transland` = '1|Saint Lucia;2|Saint of Lucia;3|Saint Lucia;4|Saint Lucia;5|Saint Lucia;6|Saint Lucia;7|Saint Lucia' WHERE  ".TBL_CMS_LAND.".`id` = 286");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 287,`transland` = '1|Saint Vincent and the Grenadin;2|Saint Vincent and the Grenadin;3|Saint Vincent and the Grenadin;4|Saint Vincent and the Grenadin;5|Saint Vincent and the Grenadin;6|Saint Vincent and the Grenadin;7|Saint Vincent and the Grenadin' WHERE  ".TBL_CMS_LAND.".`id` = 287");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 288,`transland` = '1|Samoa;2|Samoa;3|Samoa;4|Samoa;5|Samoa;6|Samoa;7|Samoa' WHERE  ".TBL_CMS_LAND.".`id` = 288");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 289,`transland` = '1|San Marino;2|San Marino;3|San Marino;4|San Marino;5|San Marino;6|San Marino;7|San Marino' WHERE  ".TBL_CMS_LAND.".`id` = 289");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 290,`transland` = '1|Sao Tome and Principe;2|Sao Tome and Principe;3|Sao Tome and Principe;4|Sao Tome and Principe;5|Sao Tome and Principe;6|Sao Tome and Principe;7|Sao Tome and Principe' WHERE  ".TBL_CMS_LAND.".`id` = 290");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 291,`transland` = '1|Saudi Arabia;2|Saudi Arabia;3|Saudi Arabia;4|Saudi Arabia;5|Saudi Arabia;6|Saudi Arabia;7|Saudi Arabia' WHERE  ".TBL_CMS_LAND.".`id` = 291");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 292,`transland` = '1|Senegal;2|Senegal;3|Senegal;4|Senegal;5|Senegal;6|Senegal;7|Senegal' WHERE  ".TBL_CMS_LAND.".`id` = 292");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 293,`transland` = '1|Seychelles;2|Seychelles;3|Seychelles;4|Seychelles;5|Seychelles;6|Seychelles;7|Seychelles' WHERE  ".TBL_CMS_LAND.".`id` = 293");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 294,`transland` = '1|Sierra Leone;2|Sierra Leone;3|Sierra Leone;4|Sierra Leone;5|Sierra Leone;6|Sierra Leone;7|Sierra Leone' WHERE  ".TBL_CMS_LAND.".`id` = 294");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 295,`transland` = '1|Singapore;2|Singapore;3|Singapore;4|Singapore;5|Singapore;6|Singapore;7|Singapore' WHERE  ".TBL_CMS_LAND.".`id` = 295");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 296,`transland` = '1|Solomon Islands;2|Solomon Islands;3|Solomon Islands;4|Solomon Islands;5|Solomon Islands;6|Solomon Islands;7|Solomon Islands' WHERE  ".TBL_CMS_LAND.".`id` = 296");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 297,`transland` = '1|Somalia;2|Somalia;3|Somalia;4|Somalia;5|Somalia;6|Somalia;7|Somalia' WHERE  ".TBL_CMS_LAND.".`id` = 297");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 298,`transland` = '1|South Africa;2|South Africa;3|South Africa;4|South Africa;5|South Africa;6|South Africa;7|South Africa' WHERE  ".TBL_CMS_LAND.".`id` = 298");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 299,`transland` = '1|South Georgia and the South Sa;2|South Georgia and the South Sa;3|South Georgia and the South Sa;4|South Georgia and the South Sa;5|South Georgia and the South Sa;6|South Georgia and the South Sa;7|South Georgia and the South Sa' WHERE  ".TBL_CMS_LAND.".`id` = 299");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 300,`transland` = '1|Sri Lanka;2|Sri Lanka;3|Sri Lanka;4|Sri Lanka;5|Sri Lanka;6|Sri Lanka;7|Sri Lanka' WHERE  ".TBL_CMS_LAND.".`id` = 300");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 301,`transland` = '1|St. Helena;2|St. Helena;3|St. Helena;4|St. Helena;5|St. Helena;6|St. Helena;7|St. Helena' WHERE  ".TBL_CMS_LAND.".`id` = 301");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 302,`transland` = '1|St. Pierre and Miquelon;2|Saint. Pierre and Miquelon;3|St. Pierre and Miquelon;4|St. Pierre and Miquelon;5|St. Pierre and Miquelon;6|St. Pierre and Miquelon;7|St. Pierre and Miquelon' WHERE  ".TBL_CMS_LAND.".`id` = 302");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 303,`transland` = '1|Sudan;2|Sudan;3|Sudan;4|Sudan;5|Sudan;6|Sudan;7|Sudan' WHERE  ".TBL_CMS_LAND.".`id` = 303");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 304,`transland` = '1|Suriname;2|Suriname;3|Suriname;4|Suriname;5|Suriname;6|Suriname;7|Suriname' WHERE  ".TBL_CMS_LAND.".`id` = 304");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 305,`transland` = '1|Svalbard and Jan Mayen Islands;2|Svalbard and Jan Mayen Islands;3|Svalbard and Jan Mayen Islands;4|Svalbard and Jan Mayen Islands;5|Svalbard and Jan Mayen Islands;6|Svalbard and Jan Mayen Islands;7|Svalbard and Jan Mayen Islands' WHERE  ".TBL_CMS_LAND.".`id` = 305");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 306,`transland` = '1|Swaziland;2|Swaziland;3|Swaziland;4|Swaziland;5|Swaziland;6|Swaziland;7|Swaziland' WHERE  ".TBL_CMS_LAND.".`id` = 306");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 307,`transland` = '1|Syrian Arab Republic;2|Syrian Arab Republic;3|Syrian Arab Republic;4|Syrian Arab Republic;5|Syrian Arab Republic;6|Syrian Arab Republic;7|Syrian Arab Republic' WHERE  ".TBL_CMS_LAND.".`id` = 307");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 308,`transland` = '1|Taiwan;2|Taiwan;3|Taiwan;4|Taiwan;5|Taiwan;6|Taiwan;7|Taiwan' WHERE  ".TBL_CMS_LAND.".`id` = 308");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 309,`transland` = '1|Tajikistan;2|Tajikistan;3|Tajikistan;4|Tajikistan;5|Tajikistan;6|Tajikistan;7|Tajikistan' WHERE  ".TBL_CMS_LAND.".`id` = 309");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 310,`transland` = '1|Tanzania, United Republic of;2|Tanzania, United Republic of;3|Tanzania, United Republic of;4|Tanzania, United Republic of;5|Tanzania, United Republic of;6|Tanzania, United Republic of;7|Tanzania, United Republic of' WHERE  ".TBL_CMS_LAND.".`id` = 310");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 311,`transland` = '1|Thailand;2|Thailand;3|Thailand;4|Thailand;5|Thailand;6|Thailand;7|Thailand' WHERE  ".TBL_CMS_LAND.".`id` = 311");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 312,`transland` = '1|Togo;2|Togo;3|Togo;4|Togo;5|Togo;6|Togo;7|Togo' WHERE  ".TBL_CMS_LAND.".`id` = 312");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 313,`transland` = '1|Tokelau;2|Tokelau;3|Tokelau;4|Tokelau;5|Tokelau;6|Tokelau;7|Tokelau' WHERE  ".TBL_CMS_LAND.".`id` = 313");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 314,`transland` = '1|Tonga;2|Tonga;3|Tonga;4|Tonga;5|Tonga;6|Tonga;7|Tonga' WHERE  ".TBL_CMS_LAND.".`id` = 314");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 315,`transland` = '1|Trinidad and Tobago;2|Trinidad and Tobago;3|Trinidad and Tobago;4|Trinidad and Tobago;5|Trinidad and Tobago;6|Trinidad and Tobago;7|Trinidad and Tobago' WHERE  ".TBL_CMS_LAND.".`id` = 315");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 316,`transland` = '1|Tunisia;2|Tunisia;3|Tunisia;4|Tunisia;5|Tunisia;6|Tunisia;7|Tunisia' WHERE  ".TBL_CMS_LAND.".`id` = 316");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 317,`transland` = '1|Turkey;2|Turkey;3|Turkey;4|Turkey;5|Turkey;6|Turkey;7|Turkey' WHERE  ".TBL_CMS_LAND.".`id` = 317");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 318,`transland` = '1|Turkmenistan;2|Turkmenistan;3|Turkmenistan;4|Turkmenistan;5|Turkmenistan;6|Turkmenistan;7|Turkmenistan' WHERE  ".TBL_CMS_LAND.".`id` = 318");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 319,`transland` = '1|Turks and Caicos Islands;2|Turks and Caicos of Iceland;3|Turks and Caicos Islands;4|Turks and Caicos Islands;5|Turks and Caicos Islands;6|Turks and Caicos Islands;7|Turks and Caicos Islands' WHERE  ".TBL_CMS_LAND.".`id` = 319");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 320,`transland` = '1|Tuvalu;2|Tuvalu;3|Tuvalu;4|Tuvalu;5|Tuvalu;6|Tuvalu;7|Tuvalu' WHERE  ".TBL_CMS_LAND.".`id` = 320");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 321,`transland` = '1|Uganda;2|Uganda;3|Uganda;4|Uganda;5|Uganda;6|Uganda;7|Uganda' WHERE  ".TBL_CMS_LAND.".`id` = 321");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 322,`transland` = '1|Ukraine;2|Ukraine;3|Ukraine;4|Ukraine;5|Ukraine;6|Ukraine;7|Ukraine' WHERE  ".TBL_CMS_LAND.".`id` = 322");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 323,`transland` = '1|United Arab Emirates;2|United Arab of emirate;3|United Arab Emirates;4|United Arab Emirates;5|United Arab Emirates;6|United Arab Emirates;7|United Arab Emirates' WHERE  ".TBL_CMS_LAND.".`id` = 323");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 324,`transland` = '1|United States;2|United States;3|United States;4|United States;5|United States;6|United States;7|United States' WHERE  ".TBL_CMS_LAND.".`id` = 324");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 325,`transland` = '1|United States Minor Outlying I;2|United States Minor Outlying I;3|United States Minor Outlying I;4|United States Minor Outlying I;5|United States Minor Outlying I;6|United States Minor Outlying I;7|United States Minor Outlying I' WHERE  ".TBL_CMS_LAND.".`id` = 325");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 326,`transland` = '1|Uruguay;2|Uruguay;3|Uruguay;4|Uruguay;5|Uruguay;6|Uruguay;7|Uruguay' WHERE  ".TBL_CMS_LAND.".`id` = 326");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 327,`transland` = '1|Uzbekistan;2|Uzbekistan;3|Uzbekistan;4|Uzbekistan;5|Uzbekistan;6|Uzbekistan;7|Uzbekistan' WHERE  ".TBL_CMS_LAND.".`id` = 327");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 328,`transland` = '1|Vanuatu;2|Vanuatu;3|Vanuatu;4|Vanuatu;5|Vanuatu;6|Vanuatu;7|Vanuatu' WHERE  ".TBL_CMS_LAND.".`id` = 328");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 329,`transland` = '1|Vatican City State (Holy See);2|Vatican City State (Holy lake);3|Vatican City State (Holy See);4|Vatican City State (Holy See);5|Vatican City State (Holy See);6|Vatican City State (Holy See);7|Vatican City State (Holy See)' WHERE  ".TBL_CMS_LAND.".`id` = 329");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 330,`transland` = '1|Venezuela;2|Venezuela;3|Venezuela;4|Venezuela;5|Venezuela;6|Venezuela;7|Venezuela' WHERE  ".TBL_CMS_LAND.".`id` = 330");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 331,`transland` = '1|Viet Nam;2|Viet Nam;3|Viet Nam;4|Viet Nam;5|Viet Nam;6|Viet Nam;7|Viet Nam' WHERE  ".TBL_CMS_LAND.".`id` = 331");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 332,`transland` = '1|Virgin Islands (British);2|Virgin Iceland (British);3|Virgin Islands (British);4|Virgin Islands (British);5|Virgin Islands (British);6|Virgin Islands (British);7|Virgin Islands (British)' WHERE  ".TBL_CMS_LAND.".`id` = 332");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 333,`transland` = '1|Virgin Islands (U.S.);2|Virgin Islands (U.S.);3|Virgin Islands (U.S.);4|Virgin Islands (U.S.);5|Virgin Islands (U.S.);6|Virgin Islands (U.S.);7|Virgin Islands (U.S.)' WHERE  ".TBL_CMS_LAND.".`id` = 333");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 334,`transland` = '1|Wallis and Futuna Islands;2|Valais and Futuna of Iceland;3|Wallis and Futuna Islands;4|Wallis and Futuna Islands;5|Wallis and Futuna Islands;6|Wallis and Futuna Islands;7|Wallis and Futuna Islands' WHERE  ".TBL_CMS_LAND.".`id` = 334");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 335,`transland` = '1|Western Sahara;2|western of Sahara;3|Western Sahara;4|Western Sahara;5|Western Sahara;6|Western Sahara;7|Western Sahara' WHERE  ".TBL_CMS_LAND.".`id` = 335");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 336,`transland` = '1|Yemen;2|Yemen;3|Yemen;4|Yemen;5|Yemen;6|Yemen;7|Yemen' WHERE  ".TBL_CMS_LAND.".`id` = 336");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 337,`transland` = '1|Yugoslavia;2|Yugoslavia;3|Yugoslavia;4|Yugoslavia;5|Yugoslavia;6|Yugoslavia;7|Yugoslavia' WHERE  ".TBL_CMS_LAND.".`id` = 337");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 338,`transland` = '1|Zaire;2|Zaire;3|Zaire;4|Zaire;5|Zaire;6|Zaire;7|Zaire' WHERE  ".TBL_CMS_LAND.".`id` = 338");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 339,`transland` = '1|Zambia;2|Zambia;3|Zambia;4|Zambia;5|Zambia;6|Zambia;7|Zambia' WHERE  ".TBL_CMS_LAND.".`id` = 339");
$this->db->query("UPDATE ".TBL_CMS_LAND." SET `id` = 340,`transland` = '1|Zimbabwe;2|Zimbabwe;3|Zimbabwe;4|Zimbabwe;5|Zimbabwe;6|Zimbabwe;7|Zimbabwe' WHERE  ".TBL_CMS_LAND.".`id` = 340");

file_put_contents(CMS_ROOT . 'html_cache/.htaccess','Deny from all');

$this->delDirWithSubDirs(CMS_ROOT . 'fckeditor/');
$this->upt_tar_files('ckedt.tar.gz', 'ckeditor');
delete_file(CMS_ROOT . 'admin/webexplorer.php');

$this->db->query("UPDATE ".TBL_CMS_CONFIG." SET wert='".$version."'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE','CMS version has been updated to '.$version);
?>