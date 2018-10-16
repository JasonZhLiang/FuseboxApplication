<?php
////////////////////////////////////////////////////////////
// File: act_processGroup.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2016-06-21
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in PhpStorm
//		
//
////////////////////////////////////////////////////////////
$curSite	= $_SESSION["ADMIN_USER"]["Site_ID"];

if ($Fusebox['action'] == 'deleteGroup') {
    // validate user, inputs

    if ( empty($_GET['id']) || empty($_GET['v']) ) {
        trigger_error('Missing id or validation for delete Group', E_USER_ERROR);
    }

    if ( ! is_numeric($_GET['id']) || $_GET['v'] != md5(SEED . $_GET['id'])) {
        trigger_error('Invalid id or validation for delete Group', E_USER_ERROR);
    }

    // get delete ID
    $delete_id  = $_GET['id'];
    $datetime   = date('Y-m-d H:i:s');
    $User_ID    = $_SESSION['ADMIN_USER']['User_ID'];

    // set Group DeleteFlag to 1
    $sql = "UPDATE site_cameras_groups SET
				DeleteFlag  = 1,
				DeleteDate  = :DeleteDate,
				DeleteBy    = :DeleteBy
			WHERE PkID = :PkID
	;";
    $PDOdb->prepare($sql);
    $PDOdb->bind('DeleteDate', $datetime);
    $PDOdb->bind('DeleteBy', $User_ID);
    $PDOdb->bind('PkID', $delete_id);
    $PDOdb->execute();

    // Adjust the SortOrder to prevent any glitches
    $sql = "
			UPDATE site_cameras_groups SET
				SortOrder 		= SortOrder-1
			WHERE SortOrder > (SELECT TargetSort.SortOrder FROM (SELECT sg.SortOrder FROM site_cameras_groups AS sg WHERE sg.PkID = :PkID) AS TargetSort)
			AND	Site_ID = :Site_ID
			AND DeleteFlag = 0
		;";
    $PDOdb->prepare($sql);
    $PDOdb->bind('Site_ID', $curSite);
    $PDOdb->bind('PkID', $delete_id);
    $PDOdb->execute();

    session_write_close();
    header("Location: ".APP_URL . $XFA["return"] ."&sid=" . $curSite ."&vid=". md5(SEED.$curSite));
    exit();

}

$group = [];
$HTMLreq = 'required';

if ( ! empty($_POST['inputFormSubmitted']) ) {

    if (empty($_SESSION['flash']['Group_ID'])) {
        trigger_error('Missing Session element, lost session?', E_USER_WARNING);
        session_write_close();
        header("Location: ".APP_URL . $XFA["return"] ."&sid=" . $curSite ."&vid=". md5(SEED.$curSite));
        exit();
    }

    $group["PkID"] 		    = $_SESSION['flash']['Group_ID'];
    $group["GroupName"] 	= $_POST["Name"];

    $lang = "en";
    $lang = LANG == "_FR" ? "fr": $lang;

    $validator = new Validate_fields();
    $validator->language	= $lang;

    $validator->check_4html = true;

    $validator->add_text_field("Group Name", $group["GroupName"], "text", "y", 100);
    // Validation END

    if ( ! $validator->validation()) {
        $error = $validator->create_msg();
    } else {

        $datetime   = date('Y-m-d H:i:s');
        $User_ID    = $_SESSION['ADMIN_USER']['User_ID'];

        $sqlVal = 	" GroupName 		= :GroupName,
					 Site_ID		= :Site_ID,
                    ModifyDate  = :ModifyDate,
                    ModifyBy    = :ModifyBy
					 "
        ;

        if ( $Fusebox["action"] == "editGroup" ) {
            $sql = "UPDATE site_cameras_groups SET ". $sqlVal ."
					WHERE PKID = :PKID
			;";
            $PDOdb->prepare($sql);
            $PDOdb->bind('GroupName', $group["GroupName"]);
            $PDOdb->bind('Site_ID', $curSite);
            $PDOdb->bind('ModifyDate', $datetime);
            $PDOdb->bind('ModifyBy', $User_ID);
            $PDOdb->bind('PkID', $group["PkID"]);
        }

        if ( $Fusebox["action"] == "addGroup" ) {
            $sql = "INSERT INTO site_cameras_groups SET ". $sqlVal .",
                        CreateDate  = :CreateDate,
                        CreateBy    = :CreateBy,
                        SortOrder = (IFNULL
                                    (
                                        (
                                            SELECT MAX(so.SortOrder) AS SortMax
                                            FROM site_cameras_groups AS so 
                                            WHERE so.Site_ID = :Site_ID
                                            AND so.DeleteFlag = 0
                                        )
                                        , 0
                                    )
                            +1)
			;";
            $PDOdb->prepare($sql);
            $PDOdb->bind('GroupName', $group["GroupName"]);
            $PDOdb->bind('Site_ID', $curSite);
            $PDOdb->bind('CreateDate', $datetime);
            $PDOdb->bind('CreateBy', $User_ID);
            $PDOdb->bind('PkID', $group["PkID"]);
        }

        if ($PDOdb->execute()) {
            unset($_SESSION['flash']);
            session_write_close();
            header("Location: ".APP_URL . $XFA["return"] ."&sid=" . $curSite ."&vid=". md5(SEED.$curSite));
            exit();
        }
    }

} else {
    if ($Fusebox['action'] == 'editGroup') {

        if ( empty($_GET["id"]) || empty($_GET['v']) ) {
            trigger_error('Missing parameters for camera Edit', E_USER_ERROR);
        }

        if ( ! is_numeric($_GET["id"]) || $_GET['v'] != md5(SEED . $_GET["id"]) ) {
            trigger_error('Failed validation of camera id', E_USER_ERROR);
        }

        $id = $_GET["id"];

        $sql = "SELECT
				PkID,
				GroupName
			FROM site_cameras_groups
			WHERE 	DeleteFlag = 0
			AND 	PkID = :PkID
	;";
        $PDOdb->prepare($sql);
        $PDOdb->bind('PkID', $id);
        $PDOdb->execute();
        if ($PDOdb->rowCount() == 1) {
            $row = $PDOdb->getRow();
            $group["PkID"] 		    = $row["PkID"];
            $group["GroupName"] 		    = $row["GroupName"];
            $_SESSION['flash']['Group_ID'] = $row["PkID"];
        }
    }

    if ($Fusebox['action'] == 'addGroup') {
        $group["PkID"] 		    = "";
        $group["GroupName"] 		    = "";
        $_SESSION['flash']['Group_ID'] = 'X';

    }
}