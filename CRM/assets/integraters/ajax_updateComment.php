<?php
////////////////////////////////////////////////////////////
// File: ajax_updateComment.php
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

$result   = 0;
$id       = $_GET["id"];
$comment  = $_GET["comment"];
$user_ID  = $_SESSION["ADMIN_USER"]["User_ID"];
$dateTime = date("Y-m-d H:i:s");

$sql = "UPDATE erpcorp_admin.integrator_comments SET
			Comment			= :Comment,
			ModifiedBy		= :ModifiedBy,
			ModifiedDate	= :ModifiedDate
		WHERE PkID = :PkID
;";

$PDOdb->prepare($sql);
$PDOdb->bind('Comment', $comment);
$PDOdb->bind('ModifiedBy', $user_ID);
$PDOdb->bind('ModifiedDate', $dateTime);
$PDOdb->bind('PkID', $id);
$PDOdb->execute();

echo($PDOdb->getResultSet());