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
                <strong>Modems Management<?php if(!empty($_GET['fromSite'])) echo(' for Site # '. $_SESSION['ADMIN_USER']['curSite']['Site_ID']) ?></strong>
                <div class="muted">Manage Modems</div>
                <hr noshade size="1">
            </td>
        </tr>
    </table>
</div>
<div class="space-12"></div>
<div class="row">
    <div class="text-center">
        <label><span class="lbl infobox-blue">List of Modems Assigned to Current Site</span></label>
    </div>
</div>
<?php renderModemList ($orderBy,$siteID,true)?>
<div class="space-12"></div>
<div class="row">
    <div class="text-center">
        <label><span class="lbl infobox-blue">List of Unassigned Modems</span></label>
    </div>
</div>
<?php renderModemList ($orderBy,$siteID,false)?>
<div class="row">
    <div class="col-xs-12 text-center">
        <a class="btn btn-xs" href="<?php echo(APP_URL . $XFA["return"]."&id=".$_SESSION['ADMIN_USER']['curSite']['Site_ID']."&v=".md5(SEED.$_SESSION['ADMIN_USER']['curSite']['Site_ID'])."#assets"); ?>"><i class="ace-icon fa fa-rotate-right"></i> <?php $trans->et('Return') ?></a>
    </div>
</div>

<div class="space-12"></div>

<div id="dialog-toggle-confirm" class="hide">
    <p>This action will associate this Modem with the current site. Do you wish to continue?</p>
</div>
<div id="dialog-toggle-delete" class="hide">
    <p>This action will disassociate this Modem with the current site. Do you wish to continue?</p>
</div>


<?php
function renderModemList ($orderBy,$siteID,$assignedToSites){
    global $Utils,$PDOdb,$XFA;
    $modelsA = [];
    switch ($orderBy) {
        case 'FSN':
            $orderBy = "FSN DESC ";
            break;
        case 'ModemStatus':
            $orderBy = "ModemStatus ASC ";
            break;
        case 'CreateDate':
            $orderBy = "CreateDate DESC ";
            break;
        case 'Provider':
            $orderBy = "Provider ASC ";
            break;
        default:
            $orderBy = "Modem_ID ASC ";
            break;
    }
    $sql = "SELECT
                mo.Modem_ID,
                mo.FSN,
                DATE_FORMAT(mo.CreateDate, '%Y-%m-%d') AS CreateDate,
                lp.Label_EN AS `Provider`,
                ls.Label_EN AS `ModemStatus`,
                u.FirstName,
                u.LastName,
                x.Modem_ID AS MID,
                x.Site_ID
                FROM
                erpcorp_admin.ModemList AS mo
                INNER JOIN erpcorp_admin._lookups AS lp ON lp.PkID = mo.Provider
                INNER JOIN erpcorp_admin._lookups AS ls ON ls.PkID = mo.ModemStatus
                INNER JOIN users AS u ON u.User_ID = mo.CreateBy
                LEFT JOIN erpcorp_admin.site_asset_xref AS x ON mo.Modem_ID = x.Modem_ID 
                    AND x.DeleteFlag = 0 
                WHERE mo.DeleteFlag = 0
                ORDER BY  " . $orderBy . "";
    $PDOdb->prepare($sql);
    $PDOdb->execute();
    if ($PDOdb->rowCount() > 0) {
        while ($row = $PDOdb->getRow()) {
            $row['EnteredBy'] = $row['FirstName'] .' '. $row['LastName'];
            $row['vHash']     = $Utils->createVerificationHash($row['Modem_ID']);
            $row['bIsLinked'] = empty($row['MID'])?false:true;
            $row['bIsLinkedToCurrentSite'] = ($row['Site_ID']==$siteID)?true:false;
            $modelsA[]         = $row;
        }
    }
    if (!empty($modelsA)){
        foreach ($modelsA as $idx=>$model){
            $sql = "SELECT x.SIMcard_ID,
                   s.ICCID,
                   s.SerialNumber,
                   lp.Label_EN AS `Provider`
            FROM erpcorp_admin.site_asset_xref AS x
            INNER JOIN erpcorp_admin.SIMList AS s ON x.SIMcard_ID = s.SIMcard_ID AND s.DeleteFlag = 0
            INNER JOIN erpcorp_admin._lookups AS lp ON lp.PkID = s.Provider
            WHERE x.DeleteFlag = 0
            AND x.SIMcard_ID != 0
            AND x.Modem_ID = :Modem_ID;";
            $PDOdb->prepare($sql);
            $PDOdb->bind('Modem_ID', $model['Modem_ID']);
            $PDOdb->execute();
            if ($PDOdb->rowCount() > 0) {
                while ($row = $PDOdb->getRow()) {
                    $model['sim'][] = $row;
                }
            }
            $modelsA[$idx] = $model;
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
                <th width="50"></th>
                <th width="150" class="text-center">Link</th>
                <th width="130">
                    <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=FSN<?= QS_DEBUG ?>">FSN</a>
                </th>
                <th width="130">
                    <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=Provider<?= QS_DEBUG ?>">Provider</a>
                </th>
                <th width="125">
                    <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=ModemStatus<?= QS_DEBUG ?>">Status</a>
                </th>
                <th width="160">Entered by</th>
                <th width="130">
                    <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=CreateDate<?= QS_DEBUG ?>">Entered Date</a>
                </th>
                <th width="150" class="text-center">Assigned Site_ID</th>
                <th width="150" class="text-center">Unlink</th>
            </tr>
            </thead>
            <?php foreach ($models as $model) { ?>
                <tr valign="top" class="header">
                    <td align="center">
                        <span class="ace-icon fa <?php if(!($model['bIsLinked']==false && empty($model['sim']))){echo('fa-chevron-down blue');}else{echo('fa-chevron-right');} ?>"></span>
                    </td>
                    <td align="center">
                        <?php if (!$model['bIsLinked'])  { ?>
                            <a href="<?= APP_URL . $XFA['toggleLink'] . '&modem=' . $model['Modem_ID'] . '&v=' . $model['vHash'] . '&state=0'?> " class="js-toggle" data-text="true">
                                <i class="ace-icon fa fa-chain green text-center"></i>
                            </a>
                        <?php } ?>
                    </td>
                    <td><?= $model['FSN'] ?>&nbsp;<small class="text-muted">[ID:<?= $model['Modem_ID'] ?>]</small></td>
                    <td><?= $model['Provider'] ?></td>
                    <td><?= $model['ModemStatus'] ?></td>
                    <td><?= $model['EnteredBy'] ?></td>
                    <td><?= $model['CreateDate'] ?></td>
                    <td class="text-center"><?= $model['Site_ID'] ?></td>
                    <td align="center">
                        <?php if ($model['bIsLinkedToCurrentSite']) { ?>
                            <a href="<?= APP_URL . $XFA['toggleLink'] . '&modem=' . $model['Modem_ID'] . '&v=' . $model['vHash'] . '&state=1'?> " class="js-toggle" data-text="">
                                <i class="ace-icon fa fa-chain-broken red"></i>
                            </a>
                        <?php } ?>
                    </td>
                </tr>
                <tr class="<?php if ($model['bIsLinked']==false && empty($model['sim'])) echo('data') ?>" style="<?php if ($model['bIsLinked']==false && empty($model['sim'])) echo('display:none'); else echo('display:table-row'); ?>">
                    <td colspan="7">
                        <div class="col-xs-offset-2 col-xs-1 text-right">
                            <a class="btn btn-white btn-xs" style="border: none !important" title="Edit"
                               href="<?php echo(APP_URL . $XFA["linkSIM"] . "&mid=" . $model['Modem_ID'] . "&mv=" . md5(SEED . $model['Modem_ID'])); ?>">
                                <i class="ace-icon fa fa-pencil bigger-120 blue"></i>
                            </a>
                        </div>
                    <?php if (!empty($model['sim'])){foreach($model['sim'] as $sim) {?>
                        <div class="col-xs-3 text-info">
                            SIMcard<span class="text-muted small"> [<?php echo($sim['SIMcard_ID']); ?>] </span> ICCID: <?php echo($sim['ICCID']); ?>
                        </div>
                        <div class="col-xs-3 text-info">Serial Number: <?php echo($sim['SerialNumber']); ?></div>
                    <?php }}else{?>
                        <div><i class="col-xs-3 smaller-85 text-muted">No SIM card associated with this modem.</i></div>
                    <?php }?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
<?php } ?>

<script>
    $(function () {
        $('.js-toggle').on('click', function(e){
            e.stopPropagation();
            e.preventDefault();
            var href = $(this).attr('href');
            var text = $(this).data('text');
            if (text != ''){
                $('#dialog-toggle-confirm')
                    .removeClass('hide')
                    .dialog({
                        modal: true,
                        width: 400,
                        title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='fa fa-warning ace-icon'></i> Add Modem to Site</h4></div>",
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
                        title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='fa fa-warning ace-icon red'></i> Remove Modem from Site</h4></div>",
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
        $('.data').hide();

        $('.header a').click(function (e) {
            e.stopPropagation();
        });

        $('.header').click(function () {
            if ($(this).find('span').hasClass('fa-chevron-down')) {
                $(this).find('span').removeClass('fa-chevron-down blue');
                $(this).find('span').addClass('fa-chevron-right');
            } else {
                $(this).find('span').removeClass('fa-chevron-right');
                $(this).find('span').addClass('fa-chevron-down blue');
            }
            $(this).nextUntil('tr.header').slideToggle(100);
        })
    });
</script>
