<?php
// Constants
define('LINK', 0);
define('UNLINK', 1);

if (!isset($_GET['sim'])) {
    trigger_error('Missing sim', E_USER_ERROR);
    exit();
}

if (!isset($_GET['state'])) {
    trigger_error('Missing state', E_USER_ERROR);
    exit();
}

$Utils->checkVerificationHash($_GET['v'], $_GET['sim']);
$Utils->checkVerificationHash($_GET['mv'], $_GET['mid']);
$state     = $_GET['state'];
$sim       = $_GET['sim'];
$modem     = $_GET['mid'];
$timeStamp = date("Y-m-d H:i:s");
switch ($state) {
    case LINK:
        $sims = array();
        $sql = "SELECT x.SIMcard_ID
        FROM erpcorp_admin.site_asset_xref AS x
        INNER JOIN erpcorp_admin.SIMList AS s ON x.SIMcard_ID = s.SIMcard_ID AND s.DeleteFlag = 0
        WHERE  x.DeleteFlag = 0 
        AND x.Modem_ID = :Modem_ID
        ;";
        $PDOdb->prepare($sql);
        $PDOdb->bind('Modem_ID', $_GET['mid']);
        $PDOdb->execute();
        if ($PDOdb->rowCount() > 0) {
            header("Location: " . APP_URL . $XFA['return'] .'&mid='.$_GET['mid'].'&mv='.$_GET['mv'].'&simExist=1');
            exit();
        }
        // add sim to modem (always create new entry in order to preserve log history)
        $sql = "INSERT INTO erpcorp_admin.site_asset_xref SET
            Modem_ID = :Modem_ID,
            SIMcard_ID = :SIMcard_ID,
            CreateDate = :CreateDate,
            CreateBy = :CreateBy
         ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('Modem_ID', $modem);
        $PDOdb->bind('SIMcard_ID', $sim);
        $PDOdb->bind('CreateDate', $timeStamp);
        $PDOdb->bind('CreateBy', $_SESSION['ADMIN_USER']['User_ID']);
        $PDOdb->execute();
        break;
    case UNLINK:
        $sql = "UPDATE erpcorp_admin.site_asset_xref
              SET `DeleteFlag` = '1',
                  `UpdateBy` = :UpdateBy,				  
                  `UpdateDate` = :UpdateDate
              WHERE Modem_ID = :Modem_ID
              AND SIMcard_ID = :SIMcard_ID
              AND DeleteFlag = 0
         ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('UpdateBy', $_SESSION['ADMIN_USER']['User_ID']);
        $PDOdb->bind('UpdateDate', $timeStamp);
        $PDOdb->bind('Modem_ID', $modem);
        $PDOdb->bind('SIMcard_ID', $sim);
        $PDOdb->execute();
        break;
}
session_write_close();
header("Location: " . APP_URL . $XFA['list'] .'&fromSite=1');
exit();
