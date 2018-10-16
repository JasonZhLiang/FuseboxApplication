<?php
////////////////////////////////////////////////////////////
// File: ajax_sortOrder.php
//
// Description:
//
//      - virtual copy of ajax_sortOrder.php, could stand to be centralized
//
//
// Information:
//		Date		- 2014-07-09
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in JetBrains PhpStorm
//		
//
////////////////////////////////////////////////////////////


$id 			= $_GET["id"];
$sortType 		= $_GET["sortType"];
$sortOrder		= $_GET["sortOrder"];
$targetID		= "";
$newSortOrder	= "";

switch($sortType){
	case '-1': // Valid input, fallthrough
		break;
	case '+1': // Valid input, fallthrough
		break;
	default:
		trigger_error('SortOrder Ajax Input Tampering', E_USER_ERROR);
}

if (empty($error)) {

	$sql = "
				SELECT et.PkID, et.SortOrder
				FROM site_cameras_groups AS es
				INNER JOIN site_cameras_groups AS et
					ON es.SortOrder = et.SortOrder:SortType /* TODO: code smell: oh FFS, this is why it works. I'm orienting from the target, not from selected id */
						AND et.Site_ID = es.Site_ID
				WHERE es.PkID = :PkID
				AND et.DeleteFlag = 0
		;";

    $PDOdb->prepare($sql);
    $PDOdb->bind('SortType', $sortType);
    $PDOdb->bind('PkID', $id);
    $PDOdb->execute();

	if ($PDOdb->rowCount() == 1) {
		$row = $PDOdb->getRow();

		$newSortOrder = $row["SortOrder"];
		$targetID     = $row["PkID"];

		$first  = changeSortOrder($id, $newSortOrder);
		$second = changeSortOrder($targetID, $sortOrder);

		if (!$first || !$second) {
			$error = "Problem with sort process.";
		}
	} else {
		$error = "Problem with sort process. Records returned: " . $count;
	}
}

if ( ! empty($error) ) {
	echo($error);
	trigger_error($error, E_USER_WARNING);
} else {
	echo("1");
}

function changeSortOrder($id, $sortOrder){
	global $PDOdb;

	$sql = "UPDATE site_cameras_groups SET
					SortOrder = :SortOrder
				WHERE PkID = :PkID
		;";
    $PDOdb->prepare($sql);
    $PDOdb->bind('SortOrder', $sortOrder);
    $PDOdb->bind('PkID', $id);
    $PDOdb->execute();

	return $PDOdb->getResultSet();

}
