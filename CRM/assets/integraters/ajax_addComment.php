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

$result   = 0;
$id       = $_GET["id"];
$comment  = $_GET["comment"];
$user_ID  = $_SESSION["ADMIN_USER"]["User_ID"];
$dateTime = date("Y-m-d H:i:s");

$userName = $_SESSION["ADMIN_USER"]["FirstName"].' '.$_SESSION["ADMIN_USER"]["LastName"];

$sql = "INSERT INTO erpcorp_admin.integrator_comments SET
			Comment			= :Comment,
			IT_ID			= :IT_ID,
			CreatedBy		= :CreatedBy,
			CreatedDate		= :CreatedDate,
			ModifiedBy		= :ModifiedBy,
			ModifiedDate	= :ModifiedDate
;";

$PDOdb->prepare($sql);
$PDOdb->bind('Comment', $comment);
$PDOdb->bind('IT_ID', $id);
$PDOdb->bind('CreatedBy', $user_ID);
$PDOdb->bind('CreatedDate', $dateTime);
$PDOdb->bind('ModifiedBy', $user_ID);
$PDOdb->bind('ModifiedDate', $dateTime);
$PDOdb->execute();

$date = date('Y-m-d');
$result = $PDOdb->rowCount();
$PkID   = $PDOdb->lastInsertId();
echo(json_encode(array("status" => $result, "id" => $PkID, "user" => $userName, "date"=> $date)));
