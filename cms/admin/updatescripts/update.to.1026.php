<?PHP
$version = '1.0.2.6';

$ADD_INDEX = array(
	TBL_CMS_OTIMER_PROG_LANG => 'pr_prog_id',
	TBL_CMS_OTIMER_DAYWORKTIME => 'dt_mid',
	TBL_CMS_OTIMER => 'kid',
	TBL_CMS_OTIMER => 'mid',
	TBL_CMS_OTIMER => 'prog_id',
	TBL_CMS_OTIMER => 'group_id',
);
foreach ($ADD_INDEX as $table => $index) $this->execSQL("ALTER TABLE ".$table." ADD INDEX ( ".$index." )"); 
foreach ($ADD_INDEX as $table => $index) {
	for ($i=0;$i<=5;$i++) {
		$this->execSQL("ALTER TABLE ".$table." DROP INDEX ".$index."_".$i."");
		#ALTER TABLE `tspit_produkte_content` DROP INDEX `pid_18`
	}
}

$ADD_INDEX = array(
	TBL_CMS_OTIMER_PROG_LANG => 'pr_prog_id',
	TBL_CMS_OTIMER_DAYWORKTIME => 'dt_mid',
	TBL_CMS_OTIMER => 'kid',
	TBL_CMS_OTIMER => 'mid',
	TBL_CMS_OTIMER => 'prog_id',
	TBL_CMS_OTIMER => 'group_id',
);
foreach ($ADD_INDEX as $table => $index) {
	for ($i=0;$i<=5;$i++) {
		$this->execSQL("ALTER TABLE ".$table." DROP INDEX ".$index."_".$i."");
		#ALTER TABLE `tspit_produkte_content` DROP INDEX `pid_18`
	}
}

@unlink(CMS_ROOT . '_htaccess.inc');
@unlink(CMS_ROOT . 'adminupd_para.tmp');

$tpl_rep = array(
	'HTA_FIXLINKS' => 'HTA_CMSFIXLINKS',
	'HTA_SSLLINKS' => 'HTA_CMSSSLLINKS',
	'HTA_CMSFIXLINKS_CMS_CMS_CMS_CMS' => 'HTA_CMSFIXLINKS',
	'$toplevels' => '$cmstoplevels'
	);
#$this->replaceInTemplatesOnlyCustomers($tpl_rep);	
$this->replaceInTemplates($tpl_rep);
			
$this->db->query("UPDATE ".TBL_CMS_CONFIG." SET wert='".$version."'WHERE ID_STR='VERSION' LIMIT 1");
$this->LOGCLASS->addLog('UPDATE','CMS version has been updated to '.$version);
?>