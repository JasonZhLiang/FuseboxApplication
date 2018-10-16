<?php
// Constants
define('LINK', 0);
define('UNLINK', 1);

if (!isset($_GET['modem'])) {
    trigger_error('Missing modem', E_USER_ERROR);
    exit();
}

if (!isset($_GET['state'])) {
    trigger_error('Missing state', E_USER_ERROR);
    exit();
}

$Utils->checkVerificationHash($_GET['v'], $_GET['modem']);

$state     = $_GET['state'];
$modem     = $_GET['modem'];
$timeStamp = date("Y-m-d H:i:s");

switch ($state) {
    case LINK:
        // add modem to site (always create new entry in order to preserve log history)
        $sql = "INSERT INTO erpcorp_admin.site_asset_xref SET
            Site_ID = :Site_ID,
            Modem_ID = :Modem_ID,
            CreateDate = :CreateDate,
            CreateBy = :CreateBy
         ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('Site_ID', $_SESSION['ADMIN_USER']['curSite']['Site_ID']);
        $PDOdb->bind('Modem_ID', $modem);
        $PDOdb->bind('CreateDate', $timeStamp);
        $PDOdb->bind('CreateBy', $_SESSION['ADMIN_USER']['User_ID'] );
        $PDOdb->execute();
        break;
    case UNLINK:
        // remove modem from site
        $sql = "UPDATE erpcorp_admin.site_asset_xref
              SET `DeleteFlag` = '1',
                  `UpdateBy` = :UpdateBy,				  
                  `UpdateDate` = :UpdateDate
              WHERE Site_ID = :Site_ID
              AND Modem_ID = :Modem_ID
              AND DeleteFlag = 0
         ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('UpdateBy', $_SESSION['ADMIN_USER']['User_ID']);
        $PDOdb->bind('UpdateDate', $timeStamp);
        $PDOdb->bind('Site_ID', $_SESSION['ADMIN_USER']['curSite']['Site_ID']);
        $PDOdb->bind('Modem_ID', $modem);
        $PDOdb->execute();
        break;
}
session_write_close();
header("Location: " . APP_URL . $XFA['list'] . "&id=" . $_SESSION['ADMIN_USER']['curSite']['Site_ID'] . "&v=" . md5(SEED . $_SESSION['ADMIN_USER']['curSite']['Site_ID']) . "#assets");
exit();
