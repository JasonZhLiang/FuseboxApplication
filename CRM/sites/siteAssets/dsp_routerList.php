<?php
////////////////////////////////////////////////////////////
// File: dsp_list
//
// Description:
//		Displays list of items
//
// Information:
//		Date	- 2018-02-02
//		Author	- Jasonzh L
//		Version	- 1.0
//
// History:
//		- v1.0 initial development
//
////////////////////////////////////////////////////////////
// get args from the query string  ... or Initialize
$orderBy = (isset($_GET['order_by'])) ? $_GET['order_by'] : "";
$siteID = $_SESSION['ADMIN_USER']['curSite']['Site_ID'];
?>
<div class="headline">
    <table class="margin-6" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td>
                <strong>Routers Management<?php if(!empty($_GET['fromSite'])) echo(' for Site # '. $_SESSION['ADMIN_USER']['curSite']['Site_ID']) ?></strong>
                <div class="muted">Manage Routers</div>
                <hr noshade size="1">
            </td>
        </tr>
    </table>
</div>
<div class="space-12"></div>
<div class="row">
    <div class="text-center">
        <label><span class="lbl infobox-blue">List of Routers Assigned to Current Site</span></label>
    </div>
</div>
<?php renderRouterList ($orderBy,$siteID,true)?>
    <div class="space-12"></div>
<div class="row">
    <div class="text-center">
        <label><span class="lbl infobox-blue">List of Unassigned Routers</span></label>
    </div>
</div>
<?php renderRouterList ($orderBy,$siteID,false)?>
<div class="row">
    <div class="col-xs-12 text-center">
        <a class="btn btn-xs" href="<?php echo(APP_URL . $XFA["return"]."&id=".$_SESSION['ADMIN_USER']['curSite']['Site_ID']."&v=".md5(SEED.$_SESSION['ADMIN_USER']['curSite']['Site_ID'])."#assets"); ?>"><i class="ace-icon fa fa-rotate-right"></i> <?php $trans->et('Return') ?></a>
    </div>
</div>
<div class="space-12"></div>
<div id="dialog-toggle-confirm" class="hide">
    <p>This action will associate this Router with the current site. Do you wish to continue?</p>
</div>
<div id="dialog-toggle-delete" class="hide">
    <p>This action will disassociate this Router with the current site. Do you wish to continue?</p>
</div>
<script>
    $(function () {
        $('.js-toggle').on('click', function(e){
            e.stopPropagation();
            e.preventDefault();
            var text = $(this).data('text');
            var href = $(this).attr('href');
            if (text != ''){
                $('#dialog-toggle-confirm')
                    .removeClass('hide')
                    .dialog({
                        modal: true,
                        width: 400,
                        title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='fa fa-warning ace-icon'></i> Add Router to Site</h4></div>",
                        title_html: true,
                        buttons: [
                            {
                                html: "<i class='fa ace-icon fa-undo bigger-110'></i> Cancel",
                                "class" : "btn btn-default pull-right btn-xs",
                                click: function() {
                                    $( this ).dialog( "close" );
                                }
                            },
                            {
                                html: "<i class='ace-icon fa fa-certificate bigger-110'></i>&nbsp;Confirm",
                                "class" : "btn btn-xs btn-primary",
                                click: function() {
                                    $( this ).dialog( "close" );
                                    window.location.href = href;
                                }

                            }
                        ]
                    });
            }else{
                $('#dialog-toggle-delete')
                    .removeClass('hide')
                    .dialog({
                        modal: true,
                        width: 400,
                        title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='fa fa-warning ace-icon red'></i> Remove Router from Site</h4></div>",
                        title_html: true,
                        buttons: [
                            {
                                html: "<i class='fa ace-icon fa-undo bigger-110'></i> Cancel",
                                "class" : "btn btn-default pull-right btn-xs",
                                click: function() {
                                    $( this ).dialog( "close" );
                                }
                            },
                            {
                                html: "<i class='ace-icon fa fa-certificate bigger-110'></i>&nbsp;Confirm",
                                "class" : "btn btn-xs btn-primary",
                                click: function() {
                                    $( this ).dialog( "close" );
                                    window.location.href = href;
                                }

                            }
                        ]
                    });

            }
        });
    });
</script>
<?php
function renderRouterList ($orderBy,$siteID,$assignedToSites){
    global $Utils,$PDOdb,$XFA;
    $modelsA = [];
    switch ($orderBy) {
        case 'ChassisSN':
            $orderBy = "ChassisSN DESC ";
            break;
        case 'RouterStatus':
            $orderBy = "RouterStatus ASC ";
            break;
        case 'CreateDate':
            $orderBy = "CreateDate DESC ";
            break;
        case 'Provider':
            $orderBy = "Provider ASC ";
            break;
        default:
            $orderBy = "Router_ID ASC ";
            break;
    }
    $sql = "SELECT
                rou.Router_ID,
                rou.ChassisSN,
                DATE_FORMAT(rou.CreateDate, '%Y-%m-%d') AS CreateDate,
                lp.Label_EN AS `Provider`,
                ls.Label_EN AS `RouterStatus`,
                u.FirstName,
                u.LastName,
                x.Router_ID AS RID,
                x.Site_ID
                FROM
                erpcorp_admin.RouterList AS rou
                INNER JOIN erpcorp_admin._lookups AS lp ON lp.PkID = rou.Provider
                INNER JOIN erpcorp_admin._lookups AS ls ON ls.PkID = rou.RouterStatus
                INNER JOIN users AS u ON u.User_ID = rou.CreateBy
                LEFT JOIN erpcorp_admin.site_asset_xref AS x ON rou.Router_ID = x.Router_ID 
                    AND x.DeleteFlag = 0 
                WHERE rou.DeleteFlag = 0
                ORDER BY  " . $orderBy . ";";
    $PDOdb->prepare($sql);
    $PDOdb->execute();
    if ($PDOdb->rowCount() > 0) {
        while ($row = $PDOdb->getRow()) {
            // enhance row object if needed
            $row['EnteredBy'] = $row['FirstName'] .' '. $row['LastName'];
            $row['vHash']     = $Utils->createVerificationHash($row['Router_ID']);
            $row['bIsLinked'] = empty($row['RID'])?false:true;
            $row['bIsLinkedToCurrentSite'] = ($row['Site_ID']==$siteID)?true:false;
            $modelsA[]         = $row;
        }
    }
    $models = array();
    if (!empty($modelsA)){
        foreach ($modelsA as $model){
            if($assignedToSites){
                if ($model['bIsLinked']&&$model['bIsLinkedToCurrentSite']){
                    $models[] = $model;
                }
            }else{
                if (!$model['bIsLinked']){
                    $models[] = $model;
                }
            }
        }
    }
    ?>
    <?php if (empty($models)) { ?>
        <div class="row">
            <div class="col-xs-offset-3 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h3 class="text-center">No Records Found</h3>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <table class="table table-hover">
            <thead>
            <tr>
                <th width="150" class="text-center">Link</th>
                <th width="130">
                    <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=ChassisSN<?= QS_DEBUG ?>">ChassisSN</a>
                </th>
                <th width="130">
                    <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=Provider<?= QS_DEBUG ?>">Provider</a>
                </th>
                <th width="125">
                    <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=RouterStatus<?= QS_DEBUG ?>">Status</a>
                </th>
                <th width="160">Entered by</th>
                <th width="130">
                    <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=CreateDate<?= QS_DEBUG ?>">Entered Date</a>
                </th>
                <th width="80" class="text-center">Assigned Site_ID</th>
                <th width="150" class="text-center">Unlink</th>
            </tr>
            </thead>
            <?php foreach ($models as $model) { ?>
                <tr valign="top">
                    <td align="center">
                        <?php if (!$model['bIsLinked'])  { ?>
                            <a href="<?= APP_URL . $XFA['toggleLink'] . '&router=' . $model['Router_ID'] . '&v=' . $model['vHash'] . '&state=0'?> " class="js-toggle" data-text="true">
                                <i class="ace-icon fa fa-chain green text-center"></i>
                            </a>
                        <?php } ?>
                    </td>
                    <td><?= $model['ChassisSN'] ?>&nbsp;<small class="text-muted">[ID:<?= $model['Router_ID'] ?>]</small></td>
                    <td><?= $model['Provider'] ?></td>
                    <td><?= $model['RouterStatus'] ?></td>
                    <td><?= $model['EnteredBy'] ?></td>
                    <td><?= $model['CreateDate'] ?></td>
                    <td class="text-center"><?= $model['Site_ID'] ?></td>
                    <td align="center">
                        <?php if ($model['bIsLinkedToCurrentSite']) { ?>
                            <a href="<?= APP_URL . $XFA['toggleLink'] . '&router=' . $model['Router_ID'] . '&v=' . $model['vHash'] . '&state=1'?> " class="js-toggle" data-text="">
                                <i class="ace-icon fa fa-chain-broken red"></i>
                            </a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
<?php } ?>