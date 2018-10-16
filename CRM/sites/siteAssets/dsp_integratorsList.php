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
$models = [];
$orderBy = (isset($_GET['order_by'])) ? $_GET['order_by'] : "";
switch ($orderBy) {
    case 'Name':
        $orderBy = "Name ASC ";
        break;
    case 'City':
        $orderBy = "City ASC ";
        break;
    case 'PrimaryContact':
        $orderBy = "PrimaryContact ASC ";
        break;
    case 'CreateDate':
        $orderBy = "CreateDate DESC ";
        break;
    default:
        $orderBy = "PkID ASC ";
        break;
}

// retrieve list of integrators
$sql = "SELECT
            i.PkID,
            i.Name,
            i.City,
            i.Country,
            i.PrimaryContact,
            i.Phone,
            i.PhoneExt,
            DATE_FORMAT(i.CreateDate, '%Y-%m-%d') AS CreateDate,
            x.Integrator_ID
            FROM erpcorp_admin.integrators AS i
            LEFT JOIN erpcorp_admin.site_asset_xref AS x ON i.PkID = x.Integrator_ID 
                AND x.DeleteFlag = 0 
                AND x.Site_ID = :Site_ID
            WHERE i.DeleteFlag = 0
            ORDER BY  " . $orderBy .";";

$PDOdb->prepare($sql);
$PDOdb->bind('Site_ID', $_SESSION['ADMIN_USER']['curSite']['Site_ID']);
$PDOdb->execute();

if ($PDOdb->rowCount() > 0) {
    $curPkID = -1;
    while ($row = $PDOdb->getRow()) {
            $curPkID                            = $row['PkID'];
            $models[$curPkID]['PkID']           = $row['PkID'];
            $models[$curPkID]['vHash']          = $Utils->createVerificationHash($row['PkID']);
            $models[$curPkID]['Name']           = $row['Name'];
            $models[$curPkID]['City']           = $row['City'];
            $models[$curPkID]['Country']        = $row['Country'];
            $models[$curPkID]['PrimaryContact'] = $row['PrimaryContact'];
            $models[$curPkID]['PhoneExt']       = $row['PhoneExt'];
            $models[$curPkID]['CreateDate']     = $row['CreateDate'];
            $models[$curPkID]['bIsLinked']      = empty($row['Integrator_ID'])?false:true;
            //format phone number
            $models[$curPkID]['Phone']= preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "+1 ($1) $2-$3", $row['Phone']);
    }
}
?>
<div class="headline">
    <table class="margin-6" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td>
                <strong>Integrators Management<?php if(!empty($_GET['fromSite'])) echo(' for Site # '. $_SESSION['ADMIN_USER']['curSite']['Site_ID']) ?></strong>
                <div class="muted">Manage Integrators</div>
                <hr noshade size="1">
            </td>
        </tr>
    </table>
</div>
<div class="space-12"></div>

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
            <th width="10%" class="text-center">Link</th>
            <th width="20%">
                <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=Name<?= QS_DEBUG ?>">Name</a>
            </th>
            <th width="10%">
                <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=City<?= QS_DEBUG ?>">City</a>
            </th>
            <th width="10%">Country</th>
            <th width="10%">
                <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=PrimaryContact<?= QS_DEBUG ?>">Contact</a>
            </th>
            <th width="15%">Phone</th>
            <th width="10%">
                <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=CreateDate<?= QS_DEBUG ?>">Entered Date</a>
            </th>
            <th width="10%" class="text-center">Unlink</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($models as $index => $model) { ?>
            <tr valign="top">
                <td class="text-center">
                    <?php if (!$model['bIsLinked']) { ?>
                        <a href="<?= APP_URL . $XFA['toggleLink'] . '&integrator=' . $model['PkID'] . '&v=' . $model['vHash'] . '&state=0'?> " class="js-toggle">
                            <i class="ace-icon fa fa-chain green text-center"></i>
                        </a>
                    <?php } ?>
                </td>
                <td><?= $model['Name'] ?>
                    <small class="text-muted"> [ID:<?= $model['PkID'] ?>]</small>
                </td>
                <td><?= $model['City'] ?></td>
                <td><?= $model['Country'] ?></td>
                <td><?= $model['PrimaryContact'] ?></td>
                <td>
                    <?php echo($model['Phone']); ?>
                    <?php if (!empty($model['PhoneExt'])) { ?>
                        <small class="text-muted"> [ext: <?php echo($model['PhoneExt']) ?>]</small>
                    <?php } ?>
                </td>
                <td><?= $model['CreateDate'] ?></td>
                <td class="text-center">
                    <?php if ($model['bIsLinked']) { ?>
                        <a href="<?= APP_URL . $XFA['toggleLink'] . '&integrator=' . $model['PkID'] . '&v=' . $model['vHash'] . '&state=1'?> " class="js-toggle">
                            <i class="ace-icon fa fa-chain-broken red"></i>
                        </a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

<?php } ?>

<div class="row">
    <div class="col-xs-12 text-center">
        <a class="btn btn-xs" href="<?php echo(APP_URL . $XFA["return"]."&id=".$_SESSION['ADMIN_USER']['curSite']['Site_ID']."&v=".md5(SEED.$_SESSION['ADMIN_USER']['curSite']['Site_ID'])."#assets"); ?>"><i class="ace-icon fa fa-rotate-right"></i> <?php $trans->et('Return') ?></a>
    </div>
</div>

<div class="space-12"></div>

<div id="dialog-toggle-confirm" class="hide">
    <p>This action will alter the link status between this integrator with current site. Do you wish to continue?</p>
</div>

<script>
    $(function () {
        $('.js-toggle').on('click', function(e){
            e.stopPropagation();
            e.preventDefault();
            var href = $(this).attr('href');
            $('#dialog-toggle-confirm')
                .removeClass('hide')
                .dialog({
                    modal: true,
                    width: 400,
                    title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='fa fa-warning ace-icon'></i> Change Link status</h4></div>",
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
        });
    });
</script>

