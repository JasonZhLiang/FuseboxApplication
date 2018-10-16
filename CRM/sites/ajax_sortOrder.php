<?php
/**
 * Created by PhpStorm.
 * User: harun
 * Date: 2017-04-04
 * Time: 3:56 PM
 */

$status = ['success' => false];

$Site_ID = $_SESSION["ADMIN_USER"]['Site_ID'];
$jsonObj = [
    'v' => $_POST['v'],
    'sortType' => $_POST['sortType'],
    'PkID' => $_POST['PkID'],
    'prevOrder' => $_POST['prevOrder'],
    'newOrder' => $_POST['newOrder'],
    'prevGroup' => $_POST['prevGroup'],
    'newGroup' => $_POST['newGroup']
];

/*
 * Case where sorting is done within a group
 */
if ($jsonObj['sortType'] == 1) {
    if ((empty($jsonObj['v'])) || ((md5(SEED . $jsonObj['PkID']) != $jsonObj['v']['rv1'])
            || (md5(SEED . $jsonObj['newGroup']) != $jsonObj['v']['rv2']))) {
        if (!IS_TEST_SERVER) {
            trigger_error('Failed validation check', E_USER_WARNING);
        }
        $status = [
            'success' => true,
            'error' => true,
            'errMsg' => 'Error has occurred, administrators have been notified.'
        ];
        echo(json_encode($status));
        session_write_close();
        exit();
    }
    if ($jsonObj['newGroup'] == $jsonObj['prevGroup']) {
        $groupID = $jsonObj['newGroup'];
        /**
         * This is a typical sort method. All records corresponding to group are offset by -1 or +1 depending the sort
         * direction - not using the swap sorting technique here
         */
        if ($jsonObj['prevOrder'] < $jsonObj['newOrder']) {

            $sql = "
        UPDATE site_cameras AS sc
        SET
            sc.SortOrder = sc.SortOrder - 1
        WHERE
            sc.SortOrder > :preSortOrder AND
            sc.SortOrder <= :newSortOrder AND
            sc.CameraGroup_ID = :CameraGroup_ID AND
            sc.DeleteFlag = 0
            ";
            $PDOdb->prepare($sql);
            $PDOdb->bind('preSortOrder', $jsonObj['prevOrder']);
            $PDOdb->bind('newSortOrder', $jsonObj['newOrder']);
            $PDOdb->bind('CameraGroup_ID', $groupID);
            $PDOdb->execute();

            $sql = "
        UPDATE site_cameras AS sc
        SET
            sc.SortOrder = :newSortOrder
        WHERE
            sc.PkID = :PkID AND
            sc.DeleteFlag = 0
            ";
            $PDOdb->prepare($sql);
            $PDOdb->bind('newSortOrder', $jsonObj['newOrder']);
            $PDOdb->bind('PkID', $jsonObj['PkID']);
            $PDOdb->execute();

        } else {
            $sql = "
        UPDATE site_cameras AS sc
        SET
            sc.SortOrder = sc.SortOrder + 1
        WHERE
            sc.SortOrder < :preSortOrder AND
            sc.SortOrder >= :newSortOrder AND
            sc.CameraGroup_ID = :CameraGroup_ID AND
            sc.DeleteFlag = 0
            ";
            $PDOdb->prepare($sql);
            $PDOdb->bind('preSortOrder', $jsonObj['prevOrder']);
            $PDOdb->bind('newSortOrder', $jsonObj['newOrder']);
            $PDOdb->bind('CameraGroup_ID', $groupID);
            $PDOdb->execute();

            $sql = "
        UPDATE site_cameras AS sc
        SET
            sc.SortOrder = :newSortOrder
        WHERE
            sc.PkID = :PkID AND
            sc.DeleteFlag = 0
            ";
            $PDOdb->prepare($sql);
            $PDOdb->bind('newSortOrder', $jsonObj['newOrder']);
            $PDOdb->bind('PkID', $jsonObj['PkID']);
            $PDOdb->execute();
        }
    } else {
        /**
         * Moving a row from one group to another
         * 1st UPDATE: subtract 1 from SortOrder where SortOrder is more than 'prevOrder' in 'prevGroup'
         */
        $sql = "
        UPDATE site_cameras AS sc
        SET
            sc.SortOrder = sc.SortOrder - 1
        WHERE
            sc.SortOrder > :preSortOrder AND
            sc.CameraGroup_ID = :CameraGroup_ID AND
            sc.DeleteFlag = 0
            ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('preSortOrder', $jsonObj['prevOrder']);
        $PDOdb->bind('CameraGroup_ID', $jsonObj['prevGroup']);
        $PDOdb->execute();

        /**
         * 2nd UPDATE: Add 1 to SortOrder where SortOrder is more than 'newOrder' in 'newGroup' to make room for the added
         * camera record in 'newGroup'
         */
        $sql = "
        UPDATE site_cameras AS sc
        SET
            sc.SortOrder = sc.SortOrder + 1
        WHERE
            sc.SortOrder >= :newSortOrder AND
            sc.CameraGroup_ID = :CameraGroup_ID AND
            sc.DeleteFlag = 0
            ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('newSortOrder', $jsonObj['newOrder']);
        $PDOdb->bind('CameraGroup_ID', $jsonObj['newGroup']);
        $PDOdb->execute();

        /**
         * 3rd and Final UPDATE: Update the SortOrder and CameraGroup_ID of the moved camera record
         */
        $sql = "
        UPDATE site_cameras AS sc
        SET
            sc.SortOrder = :newSortOrder,
            sc.CameraGroup_ID = :CameraGroup_ID
        WHERE
            sc.PkID = :PkID AND
            sc.DeleteFlag = 0
            ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('newSortOrder', $jsonObj['newOrder']);
        $PDOdb->bind('CameraGroup_ID', $jsonObj['newGroup']);
        $PDOdb->bind('PkID', $jsonObj['PkID']);
        $PDOdb->execute();
    }
    $status['success'] = true;
    $status['error'] = false;
    $status['sortType'] = 'row';
    echo(json_encode($status));
    session_write_close();
    exit();

} else { //sorting groups

    if ((empty($jsonObj['v'])) || ((md5(SEED . $jsonObj['prevGroup']) != $jsonObj['v']))){
        if(!IS_TEST_SERVER){
            trigger_error('Failed validation check', E_USER_WARNING);
        }
        $status = [
            'success' => true,
            'error' => true,
            'errMsg' => 'Error has occurred, administrators have been notified.'
        ];
        echo(json_encode($status));
        session_write_close();
        exit();
    }

    if ($jsonObj['prevOrder'] < $jsonObj['newOrder']) {

        $sql = "
        UPDATE site_cameras_groups AS sc
        SET
            sc.SortOrder = sc.SortOrder - 1
        WHERE
            sc.SortOrder > :preSortOrder AND
            sc.SortOrder <= :newSortOrder AND
            sc.Site_ID = :Site_ID AND
            sc.DeleteFlag = 0
            ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('preSortOrder', $jsonObj['prevOrder']);
        $PDOdb->bind('newSortOrder', $jsonObj['newOrder']);
        $PDOdb->bind('Site_ID', $Site_ID);
        $PDOdb->execute();

        $sql = "
        UPDATE site_cameras_groups AS sc
        SET
            sc.SortOrder = :newSortOrder
        WHERE
            sc.PkID = :PkID AND
            sc.DeleteFlag = 0
            ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('newSortOrder', $jsonObj['newOrder']);
        $PDOdb->bind('PkID', $jsonObj['PkID']);
        $PDOdb->execute();

    } else {

        $sql = "
        UPDATE site_cameras_groups AS sc
        SET
            sc.SortOrder = sc.SortOrder + 1
        WHERE
            sc.SortOrder < :preSortOrder AND
            sc.SortOrder >= :newSortOrder AND
            sc.Site_ID = :Site_ID AND
            sc.DeleteFlag = 0
            ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('preSortOrder', $jsonObj['prevOrder']);
        $PDOdb->bind('newSortOrder', $jsonObj['newOrder']);
        $PDOdb->bind('Site_ID', $Site_ID);
        $PDOdb->execute();

        $sql = "
        UPDATE site_cameras_groups AS sc
        SET
            sc.SortOrder = :newSortOrder
        WHERE
            sc.PkID = :PkID AND
            sc.DeleteFlag = 0
            ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('newSortOrder', $jsonObj['newOrder']);
        $PDOdb->bind('PkID', $jsonObj['PkID']);
        $PDOdb->execute();
    }
    $status['success'] = true;
    $status['error'] = false;
    $status['sortType'] = 'table';
    echo(json_encode($status));
    session_write_close();
    exit();
}