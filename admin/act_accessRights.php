<?php
$Utils->checkVerificationHash(@$_GET['v'], @$_GET['id']);
// safe to get
$id = $_GET['id'];
$v  = $_GET['v'];

$User_Access_Options = [
    'admin'             =>['Nav'=>'Admin',                'Name'=>'Admin Users',      'Hilite'=>1],
    'summary'           =>['Nav'=>'Admin Welcome',        'Name'=>'Dashboard',        'Hilite'=>0],
    'pendingClients'    =>['Nav'=>'--',                   'Name'=>'Pending Clients',   'Hilite'=>0],
    'sites'             =>['Nav'=>'Demo Login [3p]',      'Name'=>'Sites',            'Hilite'=>0],
    'users'             =>['Nav'=>'Admin',                'Name'=>'Users',            'Hilite'=>0],
    'assetsSims'        =>['Nav'=>'Assets Inventory',     'Name'=>'Assets-Sims',      'Hilite'=>0],
    'assetsModems'      =>['Nav'=>'Assets Inventory',     'Name'=>'Assets-Modems',    'Hilite'=>0],
    'assetsRouters'     =>['Nav'=>'Assets Inventory',     'Name'=>'Assets-Routers',   'Hilite'=>0],
    'integraters'       =>['Nav'=>'Assets Inventory',     'Name'=>'Integraters',      'Hilite'=>0],
    'sysTranslations'   =>['Nav'=>'System Maintenence',   'Name'=>'Sys Translations', 'Hilite'=>0],
    'dbTranslations'    =>['Nav'=>'System Maintenence',   'Name'=>'DB Translations',  'Hilite'=>0],
    'sysEmails'         =>['Nav'=>'System Maintenence',   'Name'=>'Sys Emails',       'Hilite'=>0],
    'sysEmailTest'      =>['Nav'=>'System Maintenence',   'Name'=>'Sys Email Test',   'Hilite'=>0],
];


if (!empty($_POST['inputFormSubmitted'])) {
    // whitelist for security
    foreach($User_Access_Options as $key => $value){
        // View
        if(!empty($_POST['AC'][$key.'_view'])){
            $ac[$key.'_view'] = 1;
        }
        // Add
        if(!empty($_POST['AC'][$key.'_add'])){
            $ac[$key.'_add'] = 1;
        }
        // Mod
        if(!empty($_POST['AC'][$key.'_mod'])){
            $ac[$key.'_mod'] = 1;
        }
        // Del
        if(!empty($_POST['AC'][$key.'_del'])){
            $ac[$key.'_del'] = 1;
        }
    }
    // serialize this for storage
    $ac = serialize($ac);

    $sql = "UPDATE erpcorp_admin.AdminUsers AS au
              SET au.User_Access = '". $ac ."'
    		WHERE (au.User_ID = '".$id."')
		";
    // update DB
    $PDOdb->prepare($sql);
    $PDOdb->execute();
    session_write_close();
    header("Location: " . APP_URL . $XFA['return'] . "");
    exit();
}

$output = [];

$sql = "SELECT au.User_Access, u.FirstName, u.LastName
		FROM erpcorp_admin.AdminUsers AS au
		INNER JOIN users AS u ON au.User_ID = u.User_ID
		WHERE (au.User_ID = '".$id."')
		";
$PDOdb->prepare($sql);
$PDOdb->bind('User_ID', $id);
$PDOdb->execute();
while ($row = $PDOdb->getRow()) {
    $output['Name']		   = $row['FirstName'] .' ' . $row['LastName'];
    $output['User_Access'] = deSerialize($row['User_Access']);
}
//print_r($output['User_Access']);




/**
 * Helper Functions
 */

function deSerialize($str){
    if($str == ""){
        return "{}";
    }else{
        return _unserialize($str);
    }
}

function _unserialize($str){
    return unserialize($str);
}


function renderUserAccessOptions($userAccessOptions, $curUserAccess){
    $len = count($userAccessOptions);
    $i = 0;
    $halfWay = ceil($len / 2);

    $str = '';

    foreach($userAccessOptions as $key => $value){
        if($i == 0){// first row
            $str .= '<div class="col-sm-6 erp-right-border">';
            $str .= renderHeaderRow();
        }elseif($i == $halfWay){ // halfway
            $str .= '</div><div class="col-sm-6">';
            $str .= renderHeaderRow(true);
        }
        $str .= renderRow($key, $value, $curUserAccess);
        $i++;
    }
    $str .= '</div>';

    return $str;
}

function renderRow($k, $v, $curUserAccess){
    $bg = '';
    if($v['Hilite']){
        $bg = 'style="background-color:lightgrey"';
    }

    $viewCk = (!empty($curUserAccess[$k .'_view']))? 'checked': '';
    $addCk  = (!empty($curUserAccess[$k .'_add'] ))? 'checked': '';
    $modCk  = (!empty($curUserAccess[$k .'_mod'] ))? 'checked': '';
    $delCk  = (!empty($curUserAccess[$k .'_del'] ))? 'checked': '';
    return '
            <!-- '. $v['Name'] .' -->
            <div class="row" '.$bg.'>
                <div class="col-xs-4">
                    <p class="text-right text-bold">'.$v['Name'].':</p>
                </div>
                <div class="col-xs-2">
                    <div class="text-center">
                        <input type="checkbox" name="AC['.$k.'_view]" '.$viewCk.' >
                    </div>
                </div>
                <div class="col-xs-2">
                    <div class="text-center">
                        <input type="checkbox" name="AC['.$k.'_add]" '.$addCk.' >
                    </div>
                </div>
                <div class="col-xs-2">
                    <div class="text-center">
                        <input type="checkbox" name="AC['.$k.'_mod]" '.$modCk.' >
                    </div>
                </div>
                <div class="col-xs-2">
                    <div class="text-center">
                        <input type="checkbox" name="AC['.$k.'_del]" '.$delCk.' >
                    </div>
                </div>
            </div>
            ';
}

function renderHeaderRow($hide = false){
    $hideXs = '';
    if($hide){
        $hideXs = 'hidden-xs';
    }
    return '
    <!-- Header -->
                        <div class="row '.$hideXs.' ">
                            <div class="col-xs-4">
                                <p> </p>
                            </div>
                            <div class="col-xs-2">
                                <div>
                                    <p class="text-center text-bold"> View</p>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="text-center">
                                    <p class="text-center text-bold"> Add</p>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="text-center">
                                    <p class="text-center text-bold"> Modify</p>
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="text-center">
                                    <p class="text-center text-bold"> Delete</p>
                                </div>
                            </div>
                        </div>
    ';
}