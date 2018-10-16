<?php
$error 	= "";
$curSite	= $_SESSION["ADMIN_USER"]["curSite"];
$id			= $curSite["Site_ID"];
if ( ! empty($_POST["inputFormSubmitted"]) ) {
	$FR_ID 		= $_POST["FR_ID"];
	$category 	= $_POST["category"];
	$sql = "INSERT INTO site_fr_xref SET
				Site_ID = :Site_ID,
				FR_ID	= :FR_ID
	;";
    $PDOdb->prepare($sql);
    $PDOdb->bind('Site_ID', $id);
    $PDOdb->bind('FR_ID', $FR_ID);
	if ($PDOdb->execute()) {
		session_write_close();
		header("Location: ". APP_URL . $XFA["return"]);
		exit();
	} else {
		$error = "Add failed. Try again, or contact the system administrator.";
	}
} else {
	$category = $_GET["cid"];
}

$firstRespondersList = array();

$sql = "SELECT
					fr.PkID,
					fr.Desc". LANG ." AS Description,
					fr.City,
					fr.Category
				FROM fr_orgs AS fr
				WHERE fr.PkID NOT IN (SELECT FR_ID FROM site_fr_xref WHERE Site_ID = :Site_ID)
					AND fr.Category = :Category
		;";
$PDOdb->prepare($sql);
$PDOdb->bind('Site_ID', $id);
$PDOdb->bind('Category', $category);
$PDOdb->execute();

if ($PDOdb->rowCount() > 0) {
    while ($row = $PDOdb->getRow()) {
        $firstRespondersList[]	= $row;
    }
}

$catList = array(
	1=>"Police",
	2=>"Fire Services",
	3=>"Paramedic / Other"
);