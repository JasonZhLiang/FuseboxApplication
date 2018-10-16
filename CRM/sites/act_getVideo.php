<?php
// close session to prevent session lock
session_write_close();

$curSite_ID	= $_SESSION["ADMIN_USER"]["Site_ID"];

// set up feeds
//http://cadsi2.inawire.dynalias.com/adverts/images/org_1945_lib_27.jpg
//$_SESSION['curCamera'] = 0;
$feeds = $_SESSION["ADMIN_USER"]["SiteCameras"]["cameras"];

// get params
// $f = (!empty($_GET['f'])) ? $_GET['f']-1 : 0; // of set it by one
$i = (!empty($_GET['i'])) ? $_GET['i'] : 0; // cache buster &i=2205

if(empty($_SESSION["ADMIN_USER"]["SiteCameras"]['curCamera'])){
    $_SESSION["ADMIN_USER"]["SiteCameras"]['curCamera'] = 0;
}

$f = $_SESSION["ADMIN_USER"]["SiteCameras"]['curCamera'];

//$whichCamera = $feeds[$f] . '&i=' . $i; // 2015-02-23 RM was missing & in front of i= fixed???
// TODO Note when I removed the $i from the end I went from HTTP/1.1 403 Forbidden to OK for HikVision cameras at TO
// this makes sense since it would need to be a ? since it would be the first param. Do we even need this??? 2015-07-29 RM

$curCameraObject = $feeds[$f];//

// timeout issue TODO

$camAccess = New CameraAccess($PDOdb, $XFA, $curSite_ID);//todo: repalce later when modify class

$camAccess->getVideo($curCameraObject);