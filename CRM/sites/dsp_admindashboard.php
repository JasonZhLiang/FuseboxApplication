<?php

if (!empty($_GET["id"])) {
    $Utils->checkVerificationHash(@$_GET['v'], @$_GET['id']);
    $id = $_GET['id'];
} else {
    session_write_close();
    header("Location:" . APP_URL . $XFA["search"]);
    exit();
}

$_SESSION['ADMIN_USER']['curSite']['Site_ID']     = $id;
$category                                   = '';
$managers                                   = array();
$users                                      = array();
$site                                       = array();
$aliases                                    = array();
$now                                        = time();
$siteMgnr                                   = new SiteInfoManager();
$sql                                        = "SELECT
			s.Site_ID,
			s.Parent_ID,
			s.SiteName,
			s.SiteDescription,
			s.HasExternalCameras,
			s.Stories,
			s.StoriesBelowGrade,
			s.SquareFootage,
			s.isPlaceholder,
			s.PriceLevel,
			s.Address_1,
			s.Address_2,
			s.City,
			s.Prov,
			s.Pcode,
			s.Country,
			s.isBase,
			s.PaidNumUsers, 
			s.MaxNumUsers, 
			s.PaidNumContacts, 
			s.MaxNumContacts,
			u.User_ID,
			u.Email,
			CONCAT(u.FirstName, ' ', u.LastName) AS FullName,
			u.Phone_1,
			u.PhoneExt_1,
			u.PhoneType_1,
			u.Phone_2,
			u.PhoneExt_2,
			u.PhoneType_2,
			u.LoginAttempts,
			CONCAT(u.City, ', ', u.Province) AS Region,
			x.AccessLevel,
			x.isNotified,
			x.isBroadCast,
			x.isBroadCastSMS,
			x.isBroadCastVoice,
			x.canInvite,
			t.Document_ID,
			t.SourceRef,
            t.ExpiryDate,
            t.MarkInvalidByOrg,
            t.InvalidReason,
            look.Label_EN AS InvalidReasonText,
            c.PkID,
            c.NotWorking
		FROM sites AS s
		LEFT JOIN site_user_xref AS x ON s.Site_ID = x.Site_ID
		LEFT JOIN users AS u ON x.User_ID = u.User_ID AND u.DeleteFlag = 0
		LEFT JOIN site_info_trespass AS t ON t.Site_ID = s.Site_ID AND t.DeleteFlag = 0
		LEFT JOIN site_info_lookups AS look ON t.InvalidReason = look.PkID
		LEFT JOIN site_cameras AS c ON s.Site_ID = c.Site_ID AND c.DeleteFlag = 0
		WHERE 	s.DeleteFlag = 0
		AND 	s.Site_ID = :Site_ID
		ORDER BY x.AccessLevel ASC
;";

$PDOdb->prepare($sql);
$PDOdb->bind('Site_ID', $id);
$PDOdb->execute();

$cameras = array();
if ($PDOdb->rowCount() > 0) {
    $curUserID = -1;
    $aCurUserID = array();
    $countCam = ['activeCameras'=>0, 'notWorkingCameras'=>0];
    $curCameraID = -1;
    $aCurCamID = array();
    while ($row = $PDOdb->getRow()) {
        $user = array();
        $camera = array();
        if (empty($site)) {
            $site["Site_ID"]            = $row["Site_ID"];
            $site["Parent_ID"]          = $row["Parent_ID"];
            $site["SiteName"]           = $row["SiteName"];
            $site["SiteDescription"]    = $row["SiteDescription"];
            $site["HasExternalCameras"] = $row["HasExternalCameras"];
            $site["Stories"]            = $row["Stories"];
            $site["StoriesBelowGrade"]  = $row["StoriesBelowGrade"];
            $site["SquareFootage"]      = empty($row["SquareFootage"]) ? '' : number_format($row["SquareFootage"]) . ' sq ft';
            $site["PriceLevel"]         = $row["PriceLevel"];
            $site["Address_1"]          = $row["Address_1"];
            $site["Address_2"]          = $row["Address_2"];
            $site["City"]               = $row["City"];
            $site["Prov"]               = $row["Prov"];
            $site["Pcode"]              = $row["Pcode"];
            $site["Country"]            = $row["Country"];
            $site["isBase"]             = $row["isBase"];
            $site['Document_ID']        = $row['Document_ID'];
            $site['SourceRef']          = $row['SourceRef'];
            $site['ExpiryDate']         = $row['ExpiryDate'];
            $site['MarkInvalidByOrg']   = $row['MarkInvalidByOrg'];
            $site['InvalidReason']      = $row['InvalidReason'];
            $site['InvalidReasonText']  = $row['InvalidReasonText'];
            $site['PaidNumUsers']       = $row['PaidNumUsers'];
            $site['MaxNumUsers']        = $row['MaxNumUsers'];
            $site['PaidNumContacts']    = $row['PaidNumContacts'];
            $site['MaxNumContacts']     = $row['MaxNumContacts'];
        }
        $curUserID             = $row["User_ID"];
        if(in_array($curUserID,$aCurUserID) == false){
            $user["User_ID"]       = $row["User_ID"];
            $user["FullName"]      = $row["FullName"];
            $user["Email"]         = $row["Email"];
            $user["LoginAttempts"] = $row["LoginAttempts"];
            $user["Phone_1"]       = $row["Phone_1"];
            $user["PhoneExt_1"]    = $row["PhoneExt_1"];
            $user["PhoneType_1"]   = $row["PhoneType_1"];
            $user["Phone"]         = $user["Phone_1"] . (empty($user["PhoneExt_1"]) ? "" : " ext " . $user["PhoneExt_1"]) . " (" . $user["PhoneType_1"] . ")";
            $user["PhoneAlt"]      = empty($user["Phone_2"]) ? "" : ($user["Phone_2"] . (empty($user["PhoneExt_2"]) ? "" : " ext " . $user["PhoneExt_2"]) . " (" . $user["PhoneType_2"] . ")");
            $user["AccessLevel"]   = $row["AccessLevel"];
            $user["Region"]          = $row["Region"];
            $user["canInvite"]       = $row["canInvite"];
            $user["isBroadCastVoice"]= $row["isBroadCastVoice"];
            $user["isBroadCastSMS"]  = $row["isBroadCastSMS"];
            $user["isBroadCast"]     = $row["isBroadCast"];
            $user["isNotified"]      = $row["isNotified"];
            $user['isVoice']         = $user["isBroadCastVoice"] ? 'green': 'light';
            $user['isSMS']           = $user["isBroadCastSMS"] ? 'green': 'light';
            $user['isEmail']         = $user["isBroadCast"] ? 'green': 'light';
            if (!empty($row['User_ID'])) {
                if ($row['AccessLevel'] < PS_SITE_VIEWER_LEVEL) {
                    $managers[] = $user;
                }
                $users[] = $user;
            }
            $aCurUserID = array_column($users,'User_ID');
        }
        $curCameraID             = $row["PkID"];
        if(in_array($curCameraID,$aCurCamID) == false){
            $camera["PkID"]       = $row["PkID"];
            $camera["NotWorking"] = $row["NotWorking"];
            if (!empty($row['PkID'])) {
                $cameras[] = $camera;
                if ($camera["NotWorking"]==0){
                    $countCam['activeCameras']++;
                }else{
                    $countCam['notWorkingCameras']++;
                }
            }
            $aCurCamID = array_column($cameras,'PkID');
        }
    }
}
$selCameraType         = $site["HasExternalCameras"];
$cameraInfo            = [0 => 'No', 1 => 'Yes', null => 'Unknown'];
$site["AnyCameraInfo"] = $cameraInfo[$selCameraType];
$sql                   = "SELECT PkID FROM site_cameras WHERE DeleteFlag = 0 AND Site_ID = :Site_ID";
$PDOdb->prepare($sql);
$PDOdb->bind('Site_ID', $id);
$PDOdb->execute();
$site["HasERPcamera"]  = 'No';
if ($PDOdb->rowCount() > 0) {
    $site["HasERPcamera"] = 'Yes';
}
$sql = "SELECT Alias FROM site_info_alias WHERE Site_ID = :Site_ID";
$PDOdb->prepare($sql);
$PDOdb->bind('Site_ID', $id);
$PDOdb->execute();
if ($PDOdb->rowCount() > 0) {
    while ($row = $PDOdb->getRow()) {
        $aliases[] = $row["Alias"];
    }
}
$integrators = array();
$sql = "SELECT x.Integrator_ID,
               i.Name,
               CONCAT(i.City, ', ', i.Province) AS Region 
        FROM erpcorp_admin.site_asset_xref AS x
        INNER JOIN erpcorp_admin.integrators AS i ON x.Integrator_ID = i.PkID AND i.DeleteFlag = 0
        WHERE x.DeleteFlag = 0
        AND x.Site_ID = :Site_ID";
$PDOdb->prepare($sql);
$PDOdb->bind('Site_ID', $id);
$PDOdb->execute();
if ($PDOdb->rowCount() > 0) {
    while ($row = $PDOdb->getRow()) {
        $integrators[] = $row;
    }
}
$routers = array();
$countRouter = 0;
$sql = "SELECT x.Router_ID,
               r.ChassisSN,
               r.MAC
        FROM erpcorp_admin.site_asset_xref AS x
        INNER JOIN erpcorp_admin.RouterList AS r ON x.Router_ID = r.Router_ID AND r.DeleteFlag = 0
        WHERE  x.DeleteFlag = 0
        AND x.Site_ID = :Site_ID";
$PDOdb->prepare($sql);
$PDOdb->bind('Site_ID', $id);
$PDOdb->execute();
if ($PDOdb->rowCount() > 0) {
    while ($row = $PDOdb->getRow()) {
        $routers[] = $row;
        $countRouter++;
    }
}
$modems = array();
$countMod = 0;
$sql = "SELECT x.Modem_ID,
               m.FSN
        FROM erpcorp_admin.site_asset_xref AS x
        INNER JOIN erpcorp_admin.ModemList AS m ON x.Modem_ID = m.Modem_ID AND m.DeleteFlag = 0
        WHERE x.DeleteFlag = 0
        AND x.Site_ID = :Site_ID
        ORDER BY  x.Modem_ID ASC
        ;";
$PDOdb->prepare($sql);
$PDOdb->bind('Site_ID', $id);
$PDOdb->execute();
if ($PDOdb->rowCount() > 0) {
    while ($row = $PDOdb->getRow()) {
        $modems[] = $row;
        $countMod++;
    }
}
if (!empty($modems)){
    foreach ($modems as $idx=>$modem){
        $sql = "SELECT x.SIMcard_ID,
               s.ICCID
        FROM erpcorp_admin.site_asset_xref AS x
        INNER JOIN erpcorp_admin.SIMList AS s ON x.SIMcard_ID = s.SIMcard_ID AND s.DeleteFlag = 0
        WHERE x.DeleteFlag = 0
        AND x.SIMcard_ID != 0
        AND x.Modem_ID = :Modem_ID";
        $PDOdb->prepare($sql);
        $PDOdb->bind('Modem_ID', $modem['Modem_ID']);
        $PDOdb->execute();
        if ($PDOdb->rowCount() > 0) {
            while ($row = $PDOdb->getRow()) {
                $modem['sim'][] = $row;
            }
        }
        $modems[$idx] = $modem;
    }
}
$sims = array();
$countSim = 0;
$sql = "SELECT x.SIMcard_ID,
               s.ICCID
        FROM erpcorp_admin.site_asset_xref AS x
        INNER JOIN erpcorp_admin.SIMList AS s ON x.SIMcard_ID = s.SIMcard_ID AND s.DeleteFlag = 0
        WHERE x.DeleteFlag = 0 
        AND x.Site_ID = :Site_ID";
$PDOdb->prepare($sql);
$PDOdb->bind('Site_ID', $id);
$PDOdb->execute();
if ($PDOdb->rowCount() > 0) {
    while ($row = $PDOdb->getRow()) {
        $sims[] = $row;
        $countSim++;
    }
}
//$site["isBase"] = true;
$objDashboard = new Dashboard_PS($siteMgnr, $trans, $site);
$objDashboard->setIsAdmin(true);
?>

<div class="">
    <div class="widget-box widget-color-dark light-border">
        <div class="widget-header">
            <h6 class="widget-title bigger-110"><?php $trans->et('Location Data') ?></h6>
            <a href="<?php echo(APP_URL . $XFA['editSite'] . "&id=" . $site["Site_ID"] . "&v=" . md5(SEED . $site['Site_ID'])); ?>">
                <div class="widget-toolbar">
                    <i class="ace-icon fa fa-cog light-blue"></i>
                </div>
            </a>
        </div>
        <div class="widget-body">
            <div class="widget-main">
                <div class="row">
                    <div class="col-sm-6">
                        <?php $objDashboard->renderSiteInformation(); ?>
                        <?php if ($site["isBase"]) $objDashboard->renderSiteAliases($aliases); ?>
                    </div>
                    <div class="col-sm-6">
                        <?php $objDashboard->renderSiteContactSection($managers); ?>
                        <?php $objDashboard->renderCameraInfoSection(); ?>
                        <?php $objDashboard->renderSiteHazardIcons(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <a href="<?php echo(APP_URL . $XFA['search']); ?>" class="btn btn-xs"><i class="ace-icon fa fa-rotate-left"></i> <?php $trans->et('Return') ?></a>
                    </div>
                </div>
                <div class="space-4"></div>
            </div>
        </div>
    </div>
</div>

<div class="space-6"></div>

<div class="hr hr-18 dotted hr-double"></div>

<div class="row">
    <div class="col-sm-6">
        <div class="widget-box widget-color-dark light-border">
            <div class="widget-header">
                <h6 class="widget-title bigger-110">Users</h6>
            </div>
            <div class="widget-body">
                <div class="widget-main">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="well well-sm">
                                <?php if(!empty($users)){?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="TblHeader">Contact Information</th>
                                            <th class="TblHeader">Region</th>
                                            <th class="TblHeader">Privileges</th>
                                            <th class="text-center" width="10%">Can Invite Others</th>
                                            <th class="text-center" width="10%">Emergency Services Pierce Notifications</th>
                                            <th class="text-center" width="10%">Emergency Services Broadcast</th>
                                        </tr>
                                    </thead>
                                    <?php foreach ($users as $user) { ?>
                                        <tr>
                                            <td>
                                                <?php echo($user["FullName"]); ?>&nbsp;<span class="text-muted">[<?php echo($user["User_ID"]); ?>]</span>
                                                <i class="ace-icon fa fa-phone blue">&nbsp;<?php echo($user["Phone"]); ?></i>
                                                <p class="btn btn-white btn-info btn-xs"><?php echo($user["Email"]); ?></p>
                                            </td>
                                            <td><?php echo($user['Region']); ?>&nbsp;</td>
                                            <td><?php echo(array_search($user['AccessLevel'], $aAccessLevels)); ?>&nbsp;</td>
                                            <td class="text-center"><?php if(empty($user['canInvite'])) echo('No'); else echo('Yes'); ?></td>
                                            <td class="text-center"><?php if(empty($user['isNotified'])) echo('No'); else echo('Yes'); ?></td>
                                            <td class="text-center">
                                                <span class="ace-icon fa fa-comment-o <?php echo($user['isVoice']); ?> bigger-140"></i></span>&nbsp;
                                                <span class="ace-icon fa fa-mobile <?php echo($user['isSMS']); ?> bigger-140"></i></span>&nbsp;
                                                <span class="ace-icon fa fa-envelope <?php echo($user['isEmail'] ); ?> bigger-140"></i></span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </table>
                                <?php }else{?>
                                    <div><i class="text-muted">No associated user(s) linked to this site.</i></div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="text-center col-xs-12 margin-bottom-10">
                            <b><?php $trans->et('Notification Legend') ?></b>: &nbsp;
                            <?php $trans->et('Voice') ?>: <span data-rel="tooltip" data-original-title="Contact by voicemail"><i class="ace-icon fa fa-comment-o green bigger-140"></i></span>&nbsp;
                            <?php $trans->et('SMS') ?>: <span data-rel="tooltip" data-original-title="Contact by SMS"><i class="ace-icon fa fa-mobile green bigger-140"></i></span>&nbsp;
                            <?php $trans->et('Email') ?>: <span data-rel="tooltip" data-original-title="Contact by email"><i class="ace-icon fa fa-envelope green bigger-140"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <?php $objDashboard->renderRegisteredFirstRespondersSection(); ?>
    </div>
    <div class="col-sm-6">
        <div class="widget-box widget-color-dark light-border">
            <div class="widget-header">
                <a name="assets"></a>
                <h6 class="widget-title bigger-110">Assets</h6>
            </div>
            <div class="widget-body">
                <div class="widget-main">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="well well-sm">
                                <a href="<?php echo(APP_URL . $XFA['editCameras']."&sid=" . $site['Site_ID'] ."&vid=". md5(SEED.$site['Site_ID'])); ?>">
                                    <div class="widget-toolbar">
                                        <i class="ace-icon fa fa-cog light-blue"></i>
                                    </div>
                                </a>
                                <h5 class="blue">Cameras</h5>
                                <?php if(!empty($cameras)){?>
                                    <div>Site Active Cameras : <?php echo($countCam['activeCameras']); ?></div>
                                    <div>Site Inactive Cameras : <?php echo($countCam['notWorkingCameras']); ?></div>
                                <?php }else{?>
                                    <div><i class="smaller-85 text-muted">No associated camera(s) linked to this site.</i></div>
                                <?php }?>
                            </div>
                            <div class="well well-sm">
                                <a href="<?php echo(APP_URL . $XFA['editIntegrator'].'&fromSite=1'); ?>" class="js_highlight">
                                    <div class="widget-toolbar">
                                        <i class="ace-icon fa fa-cog light-blue"></i>
                                    </div>
                                </a>
                                <h5 class="blue">Integrators</h5>
                                <?php if(!empty($integrators)){?>
                                    <?php foreach ($integrators as $integrator) {?>
                                        <div><?php echo($integrator["Name"]); ?>&nbsp;</div>
                                    <?php }?>
                                <?php }else{?>
                                    <div><i class="smaller-85 text-muted">No associated integrator(s) linked to this site.</i></div>
                                <?php }?>
                            </div>
                            <div class="well well-sm">
                                <a href="<?php echo(APP_URL . $XFA['editRouters'].'&fromSite=1'); ?>">
                                    <div class="widget-toolbar">
                                        <i class="ace-icon fa fa-cog light-blue"></i>
                                    </div>
                                </a>
                                <h5 class="blue">Routers</h5>
                                <?php if(!empty($routers)){?>
                                    <?php foreach ($routers as $router) {?>
                                        <div><?php echo($router['ChassisSN']); ?></div>
                                    <?php }?>
                                <?php }else{?>
                                    <div><i class="smaller-85 text-muted">No associated Router(s) linked to this site.</i></div>
                                <?php }?>
                            </div>
                            <div class="well well-sm">
                                <a href="<?php echo(APP_URL . $XFA['editModems'].'&fromSite=1'); ?>">
                                    <div class="widget-toolbar">
                                        <i class="ace-icon fa fa-cog light-blue"></i>
                                    </div>
                                </a>
                                <h5 class="blue">Modems</h5>
                                <?php if(!empty($modems)){?>
                                    <?php foreach($modems as $modem) {?>
                                        <div><?php echo($modem['FSN']); ?></div>
                                        <?php if (!empty($modem['sim'])){foreach($modem['sim'] as $sim) {?>
                                            <div class="col-xs-offset-1 text-info">SIM card ICCID: <?php echo($sim['ICCID']); ?></div>
                                        <?php }}else{?>
                                            <div class="col-xs-offset-1"><i class="smaller-85 text-muted">No associated SIMcard linked to this modem.</i></div>
                                        <?php }?>
                                    <?php }?>
                                <?php }else{?>
                                    <div><i class="smaller-85 text-muted">No associated modem(s) linked to this site.</i></div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('[data-rel=popover]').on("click", function (e) {
            e.preventDefault();
        }).popover({html: true});
        $('[data-toggle=popover]').on("click", function (e) {
            e.preventDefault();
        }).popover({html: true});
        $.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
            _title: function (title) {
                var $title = this.options.title || '&nbsp;'
                if (("title_html" in this.options) && this.options.title_html == true) {
                    title.html($title);
                } else {
                    title.text($title);

                }
            }
        }));

        $(".js_deleteEOS").on('click', function (e) {
            e.preventDefault();
            var id = $(this).data("eos");
            // delete AJAX function, if doing by AJAX

            var dialog = $("#dialog-confirm-eos-close").removeClass('hide').dialog({
                resizable: false,
                modal: true,
                title: "<div class='widget-header'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle red'></i> <?php $trans->etjs('Close This Code?') ?></h4></div>",
                title_html: true,
                buttons: [
                    {
                        html: "<i class='ace-icon fa fa-times bigger-110'></i>&nbsp; <?php $trans->etjs('Cancel') ?>",
                        "class": "btn btn-xs pull-right",
                        click: function () {
                            $(this).dialog("close");
                        }
                    },
                    {
                        html: "<i class='ace-icon fa fa-ban bigger-110'></i>&nbsp; <?php $trans->etjs('Close Code') ?>",
                        "class": "btn btn-danger btn-xs pull-right",
                        click: function () {
                            $("#closeEOS").val(id);
                            $("#deleteFormEOS").submit();
                        }
                    }
                ]
            });
        });
        $('.redeemed').tooltip();

    });
</script>
