<?php
$error =  $searchSiteName = $like = '';
$qryStartsWith	= "checked";
if (! empty($_GET["searchSiteName"])){
    $holdFlag = $_GET["searchSiteName"];
}elseif(! empty($_POST["inputFormSubmitted"])){
    $holdFlag = $_POST["inputFormSubmitted"];
}else{
    $holdFlag ="";
}
if (! empty($_GET["initial"])){
    unset($_SESSION['ADMIN_USER']['searchList']);
    unset($_SESSION['ADMIN_USER']['curSite']);
    unset($_SESSION['ADMIN_USER']['qs']);
    unset($_SESSION['tagetUser']);
}
if (! empty($_POST["searchSiteName"])){
    $_SESSION['ADMIN_USER']['searchList']['searchSiteName'] = $_POST["searchSiteName"];
    $holdFlag =$_POST["searchSiteName"];
}
$holdFlag = isset($_SESSION['ADMIN_USER']['searchList']['searchSiteName'])?$_SESSION['ADMIN_USER']['searchList']['searchSiteName']:"";

if ( ! empty($holdFlag) ) {
	$searchSiteName	= isset($_GET["searchSiteName"])?$_GET["searchSiteName"]:isset($_POST["searchSiteName"])?$_POST["searchSiteName"]:"";
    $searchSiteName = isset($_SESSION['ADMIN_USER']['searchList']['searchSiteName'])?$_SESSION['ADMIN_USER']['searchList']['searchSiteName']:$searchSiteName;
	if (empty($searchSiteName)){
		$error = "No search criteria entered. Please try again.";
	}else{
		if($searchSiteName == "*") {
			$like = "";
		} elseif ( ( ! empty($searchSiteName) ) && ( is_numeric($searchSiteName) ) ){
			$like = "  AND  s.Site_ID =  '".  $searchSiteName ."'";
		} else {
			if ( ! empty($searchSiteName) ) {
				$like = "AND (s.SiteName LIKE '%". $searchSiteName ."%'"." OR s.Address_1 LIKE '%". $searchSiteName."%')";
			}
		}
		$sql = "SELECT s.Site_ID, s.SiteName, s.SiteDescription, s.City, s.Address_1, x.AccessLevel, CONCAT(u.LastName, ', ', u.FirstName) AS FullName
				FROM sites AS s
				LEFT JOIN site_user_xref As x ON s.Site_ID = x.Site_ID AND x.AccessLevel IN(". PS_SITE_PRIME_LEVEL .")
				LEFT JOIN users AS u ON x.User_ID = u.User_ID AND u.DeleteFlag = 0
				WHERE s.DeleteFlag = 0
					". $like ."
				ORDER BY s.SiteName
		;";
        $PDOdb->prepare($sql);
        $PDOdb->execute();
		if ($PDOdb->rowCount() > 0) {
			while ($row = $PDOdb->getRow()) {
				$row['verifyToken'] = md5(SEED.$row['Site_ID']);
				$sites[]			= $row;
			}
		} else {
			$error = "No search results returned. Please try again.";
		}
	}
}
if(!empty($_SESSION['tagetUser']['User_ID'])){
    $sites=[];
    $sql = "SELECT s.Site_ID, s.SiteName, s.SiteDescription, s.City, s.Address_1, x.AccessLevel, CONCAT(u.LastName, ', ', u.FirstName) AS FullName
				FROM sites AS s
				LEFT JOIN site_user_xref As x ON s.Site_ID = x.Site_ID
				LEFT JOIN users AS u ON x.User_ID = u.User_ID
				WHERE s.DeleteFlag = 0 AND x.User_ID = :User_ID
				ORDER BY s.SiteName
		;";
    $PDOdb->prepare($sql);
    $PDOdb->bind('User_ID', $_SESSION['tagetUser']['User_ID']);
    $PDOdb->execute();
    if ($PDOdb->rowCount()  > 0) {
        while ($row = $PDOdb->getRow()) {
            $row['verifyToken'] = md5(SEED.$row['Site_ID']);
            $sites[]			= $row;
        }
    }
}
?>