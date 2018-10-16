<?php
////////////////////////////////////////////////////////////
// File: act_list
//		display/view page
//
// Information:
//		Date		- 2014-09-0-5
//		Author		- Rick	O'S
//		Version		- 2.0
//
// History:
//		- v2.0 2014-09-05: extracted from the dsp_ file 
//
////////////////////////////////////////////////////////////

$admPkID = $_SESSION['ADMIN_USER']['User_ID'];	// for 3pLogin

$listSwitch = (empty($_GET['show_delete'])) ? 0 : 1;

$error =  $searchLastName  = $searchEmail = $like = $qryContains = $qryModifier = '';
$qryStartsWith	= "checked";

if(! empty($_POST["inputFormSubmitted"])){
    $holdFlag = $_POST["inputFormSubmitted"];
}else{
    $holdFlag ="";
}
if (! empty($_GET["initial"])){
    unset($_SESSION['ADMIN_USER']['qs']);
    unset($_SESSION['ADMIN_USER']['curSite']);
    unset($_SESSION['ADMIN_USER']['searchList']);
    unset($_SESSION['tagetUser']);
}

if (isset($_POST['searchLastName'])){
    $_SESSION['ADMIN_USER']['qs']['contacts.list'] = $_POST;
}

$holdFlag = isset($_SESSION['ADMIN_USER']['qs']['contacts.list']['inputFormSubmitted'])?$_SESSION['ADMIN_USER']['qs']['contacts.list']['inputFormSubmitted']:"";
$searchLastName =isset($_SESSION['ADMIN_USER']['qs']['contacts.list']['searchLastName'] )?$_SESSION['ADMIN_USER']['qs']['contacts.list']['searchLastName'] :"";
$searchEmail =isset($_SESSION['ADMIN_USER']['qs']['contacts.list']['searchEmail'] )?$_SESSION['ADMIN_USER']['qs']['contacts.list']['searchEmail'] :"";
$qryCondition =isset($_SESSION['ADMIN_USER']['qs']['contacts.list']['form-qry-cond'] )?$_SESSION['ADMIN_USER']['qs']['contacts.list']['form-qry-cond'] :"";

if ( ! empty($holdFlag) ) {
	if (empty($searchLastName)  && empty($searchEmail)){
		$error = "No search criteria entered. Please try again.";
	}else{
		// Get list of Users based on search criteria
		if($searchLastName == "*" || $searchEmail == "*" ) {
			$like = "";
		} elseif ( ( ! empty($searchLastName) ) && ( is_numeric($searchLastName) ) ){
			//	if the lastname they supplied 
			$like = "  AND  u.User_ID =  '".  $searchLastName ."'";
		} else {
			//	else process the form in the usual fashion ... 
			$like = array();

			switch($qryCondition) {
				case "C":
					$qryContains	= "checked";
					$qryModifier	= "%";
					$qryStartsWith	= "";
					break;
				case "S":
					$qryContains	= "";
					$qryStartsWith	= "checked";
					break;
			}

			if ( ! empty($searchLastName) ) {
				$like[] = "u.LastName LIKE '". $qryModifier . $searchLastName ."%'";
			}
			if ( ! empty($searchEmail) ) {
				$like[]	= "u.Email LIKE '". $qryModifier . $searchEmail ."%'";
			}
			
			// Create the search criteria as a set of optional search terms that are inclusive
			//			$like = " AND ( ". implode(" OR ", $like) ." )";
			// 2014-09-26 RoK as instructed by RM:		- make this an    AND 
			$like = " AND ( ". implode(" AND ", $like) ." )";
		}


		$sql = "SELECT
					u.User_ID, u.LastName, u.FirstName, u.Email, u.LoginAttempts,
					CONCAT(u.LastName, ', ', u.FirstName) AS Fullname,
					CONCAT(u.City, ', ', u.Province) AS Region,
					u.Phone_1,
			        u.PhoneExt_1,
			        u.PhoneType_1,
			        u.isActive,
			        u.isFR,
			        u.Lang
				FROM users AS u
				WHERE u.DeleteFlag = :DeleteFlag
					". $like ."
				ORDER BY Fullname
		;";

        $PDOdb->prepare($sql);
        $PDOdb->bind('DeleteFlag', $listSwitch);
        $PDOdb->execute();
        if ($PDOdb->rowCount() > 0) {
            while ($row = $PDOdb->getRow()) {
				$row['my3pSpecs'] = '&c='. $row['User_ID'] .'&a='. $admPkID .'&v='. md5($row['User_ID'] . $admPkID . SEED)  .'';
				$row['verifyToken'] = md5(SEED.$row['User_ID']);
				if (empty($row["Phone_1"])){
                    $row["Phone"] ="";
                }else{
                    $row["Phone"]       = $row["Phone_1"] . (empty($row["PhoneExt_1"]) ? "" : " ext " . $row["PhoneExt_1"]) . " (" . $row["PhoneType_1"] . ")";
                }
                if($row['Lang'] == '_EN'){
				    $row['Lang'] = 'English';
                }else{
                    $row['Lang'] = 'French';
                }
                $row['Region']= ($row['Region']==', ')?'':$row['Region'];
                $row['isFR']  = (empty($row['isFR']))?'':'FR';
				$users[]			= $row;
			}
		} else {
			$error = "No search results returned. Please try again.";
		}
	}

}

if(!empty($_SESSION['ADMIN_USER']['curSite']['Site_ID'])){
    $sql = "SELECT
					u.User_ID, u.LastName, u.FirstName, u.Email, u.LoginAttempts,
					CONCAT(u.LastName, ', ', u.FirstName) AS Fullname,
					CONCAT(u.City, ', ', u.Province) AS Region,
					u.Phone_1,
			        u.PhoneExt_1,
			        u.PhoneType_1,
			        u.isActive,
			        u.isFR,
			        u.Lang
				FROM users AS u
				INNER JOIN site_user_xref AS x ON u.User_ID = x.User_ID AND x.Site_id = :Site_id
				WHERE u.DeleteFlag = :DeleteFlag
				ORDER BY Fullname
		;";
    $users = [];
    $PDOdb->prepare($sql);
    $PDOdb->bind('Site_id', $_SESSION['ADMIN_USER']['curSite']['Site_ID']);
    $PDOdb->bind('DeleteFlag', $listSwitch);
    $PDOdb->execute();
    if ($PDOdb->rowCount() > 0) {
        while ($row = $PDOdb->getRow()) {
            $row['my3pSpecs'] = '&c='. $row['User_ID'] .'&a='. $admPkID .'&v='. md5($row['User_ID'] . $admPkID . SEED)  .'';
            $row['verifyToken'] = md5(SEED.$row['User_ID']);
            if (empty($row["Phone_1"])){
                $row["Phone"] ="";
            }else{
                $row["Phone"]       = $row["Phone_1"] . (empty($row["PhoneExt_1"]) ? "" : " ext " . $row["PhoneExt_1"]) . " (" . $row["PhoneType_1"] . ")";
            }
            if($row['Lang'] == '_EN'){
                $row['Lang'] = 'English';
            }else{
                $row['Lang'] = 'French';
            }
            $row['Region']= ($row['Region']==', ')?'':$row['Region'];
            $row['isFR']  = (empty($row['isFR']))?'':'FR';
            $users[]			= $row;
        }
    } else {
        $error = "No search results returned. Please try again.";
    }
}
?>