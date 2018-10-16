<?php
////////////////////////////////////////////////////////////
// File: ajax_addComment.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2014-02-11
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in JetBrains PhpStorm
//		
//
////////////////////////////////////////////////////////////

$data  = 0;
$pcode = isset($_GET["pcode"]) ? $_GET["pcode"] : '';

$sql = "SELECT SortID FROM erpweb_main.zref_province WHERE
			ProvCode	= :ProvCode
;";
$PDOdb->prepare($sql);
$PDOdb->bind('ProvCode', $pcode);
$PDOdb->execute();

if ($PDOdb->rowCount() == 1) {
    $row = $PDOdb->getRow();
    $data = $row['SortID'];
}
echo(json_encode($data));
