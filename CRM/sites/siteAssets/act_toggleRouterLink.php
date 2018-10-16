<?php
// Constants
define('LINK',   0);
define('UNLINK', 1);

if (!isset($_GET['router'])) {
    trigger_error('Missing router', E_USER_ERROR);
    exit();
}

if (!isset($_GET['state'])) {
    trigger_error('Missing state', E_USER_ERROR);
    exit();
}

$Utils->checkVerificationHash($_GET['v'], $_GET['router']);

$state     = $_GET['state'];
$router    = $_GET['router'];
$timeStamp = date("Y-m-d H:i:s");

switch ($state) {
    case LINK:
        // add router to site (always create new entry in order to preserve log history)
        $sql = "INSERT INTO erpcorp_admin.site_asset_xref SET
            Site_ID = :Site_ID,
            Router_ID = :Router_ID,
            CreateDate = :CreateDate,
            CreateBy = :CreateBy
         ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('Site_ID', $_SESSION['ADMIN_USER']['curSite']['Site_ID']);
        $PDOdb->bind('Router_ID', $router);
        $PDOdb->bind('CreateDate', $timeStamp);
        $PDOdb->bind('CreateBy', $_SESSION['ADMIN_USER']['User_ID'] );
        $PDOdb->execute();
        break;
    case UNLINK:
        // remove router from site
        $sql = "UPDATE erpcorp_admin.site_asset_xref
              SET `DeleteFlag` = '1',
                  `UpdateBy` = :UpdateBy,				  
                  `UpdateDate` = :UpdateDate
              WHERE Site_ID = :Site_ID
              AND Router_ID = :Router_ID
              AND DeleteFlag = 0
         ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('UpdateBy', $_SESSION['ADMIN_USER']['User_ID']);
        $PDOdb->bind('UpdateDate', $timeStamp);
        $PDOdb->bind('Site_ID', $_SESSION['ADMIN_USER']['curSite']['Site_ID']);
        $PDOdb->bind('Router_ID', $router);
        $PDOdb->execute();
        break;
}
session_write_close();
header("Location: " . APP_URL . $XFA['list'] ."&id=".$_SESSION['ADMIN_USER']['curSite']['Site_ID']."&v=".md5(SEED.$_SESSION['ADMIN_USER']['curSite']['Site_ID']) . "#assets");
exit();
