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
        $orderBy = " PkID ASC ";
        break;
}

$listSwitch = (empty($_GET['show_delete'])) ? 0 : 1;

$sql = "SELECT
            i.PkID,
            i.Name,
            i.City,
            i.Country,
            i.PrimaryContact,
            i.Phone,
            i.PhoneExt,
            DATE_FORMAT(i.CreateDate, '%Y-%m-%d') AS CreateDate,
            CONCAT(t.FirstName, ' ', t.LastName) AS FullName,
            t.User_ID,
            t.Phone_1,
            t.PhoneExt_1,
            t.Email
            FROM erpcorp_admin.integrators AS i
            LEFT JOIN erpcorp_admin.technicians AS t ON i.PkID = t.IT_ID AND t.DeleteFlag = 0
            WHERE i.DeleteFlag = :listSwitch
            ORDER BY  " . $orderBy . ", t.User_ID ASC";
$PDOdb->prepare($sql);
$PDOdb->bind('listSwitch', $listSwitch);
$PDOdb->execute();

if ($PDOdb->rowCount() > 0) {
    $curPkID = -1;
    while ($row = $PDOdb->getRow()) {
        if ($curPkID != $row['PkID']) {
            $curPkID                            = $row['PkID'];
            $models[$curPkID]['PkID']           = $row['PkID'];
            $models[$curPkID]['vHash']          = $Utils->createVerificationHash($row['PkID']);
            $models[$curPkID]['Name']           = $row['Name'];
            $models[$curPkID]['City']           = $row['City'];
            $models[$curPkID]['Country']        = $row['Country'];
            $models[$curPkID]['PrimaryContact'] = $row['PrimaryContact'];
            $models[$curPkID]['Phone']          = $row['Phone'];
            $models[$curPkID]['PhoneExt']       = $row['PhoneExt'];
            $models[$curPkID]['CreateDate']     = $row['CreateDate'];
            $models[$curPkID]['Technicians']    = [];
        }
        if ($row['User_ID'] != null) {
            $UserDetails                       = [];
            $UserDetails['User_ID']            = $row['User_ID'];
            $UserDetails['FullName']           = $row['FullName'];
            $UserDetails['Email']              = $row['Email'];
            $UserDetails['Phone_1']            = $row['Phone_1'];
            $UserDetails['PhoneExt_1']         = $row['PhoneExt_1'];
            $models[$curPkID]['Technicians'][] = $UserDetails;
        }
    }
}
?>

<div class="headline">
    <table class="margin-6" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td>
                <strong>Integrators Management</strong>
                <div class="muted">Manage <?= SEC_TYPE ?></div>
                <hr noshade size="1">
            </td>
        </tr>
        <tr>
            <td align="center">
                <a href="<?= APP_URL . $XFA['add'] ?>&id=000"
                   title="Click here to add a <?= SEC_TYPE ?> entry" class=" btn btn-xs btn-success">
                    <i class="fa-plus fa ace-icon"></i>Create <?= SEC_TYPE ?>
                </a>
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
            <th width="20"></th>
            <th width="40">Edit</th>
            <th width="120">
                <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=Name<?= QS_DEBUG ?>">Name</a>
            </th>
            <th width="20">
                <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=City<?= QS_DEBUG ?>">City</a>
            </th>
            <th width="10">Country</th>
            <th width="60">
                <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=PrimaryContact<?= QS_DEBUG ?>">Contact</a>
            </th>
            <th width="100">Phone</th>
            <th width="20">
                <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=CreateDate<?= QS_DEBUG ?>">CreateDate</a>
            </th>
            <th width="20">
                <?php if ($listSwitch == 0) {
                    echo("Delete");
                } else {
                    echo("Undelete");
                }; ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($models as $index => $model) { ?>
            <tr valign="top" class="header">
                <td align="center"><span class="ace-icon fa fa-chevron-right"></span></td>
                <td>
                    <a href="<?= APP_URL . $XFA['edit'] . '&id=' . $model['PkID'] . '&v=' . $model['vHash'] ?>"
                       class="btn btn-info btn-xs ">
                        <i class="ace-icon fa fa-edit"></i>
                    </a>
                </td>
                <td><?= $model['Name'] ?>
                    <small class="text-muted"> [ID:<?= $model['PkID'] ?>]</small>
                </td>
                <td><?= $model['City'] ?></td>
                <td><?= $model['Country'] ?></td>
                <td><?= $model['PrimaryContact'] ?></td>
                <td>
                    <?php $phone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "+1 ($1) $2-$3", $model['Phone']); echo($phone); ?>
                    <?php if (!empty($model['PhoneExt'])) { ?>
                        <small class="text-muted"> [ext: <?php echo($model['PhoneExt']) ?>]</small>
                    <?php } ?>
                </td>
                <td><?= $model['CreateDate'] ?></td>
                <td>
                    <button class="btn <?php if ($listSwitch == 1) {echo("btn-primary js_undeleteBtn");} else {echo("btn-danger js_deleteBtn");}; ?> btn-xs" data-id="<?= $model['PkID'] ?>" data-v="<?= $model['vHash'] ?>">
                        <i class="ace-icon fa <?php if ($listSwitch == 1) {echo("fa-recycle");} else {echo("fa-times");}; ?>"></i>
                    </button>
                </td>
            </tr>
            <tr class="data">
                <td colspan="8">
                    <?php
                    $technicians = $model['Technicians'];
                    if (empty($technicians)) { ?>
                        <div class="row">
                            <div class="col-xs-1">&nbsp;</div>
                            <div class="col-xs-10">
                                <i class="text-muted">No technician records associated with this integrator</i>
                            </div>
                        </div>
                    <?php } else { ?>
                        <?php foreach ($technicians as $key => $technician) { ?>
                            <div class="row">
                                <div class="col-md-2 align-right">
                                    <a class="btn btn-white btn-xs" style="border: none !important" title="Edit"
                                       href="<?php echo(APP_URL . $XFA["editTech"] . "&tid=" . $technician["User_ID"] . "&tv=" . md5(SEED . $technician["User_ID"])); ?>">
                                        <i class="ace-icon fa fa-pencil bigger-120 blue"></i>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <i class="ace-icon fa fa-user blue"></i>&nbsp;<?php echo($technician['FullName']); ?>
                                </div>
                                <div class="col-md-3">
                                    <i class="ace-icon fa fa-phone blue"></i>&nbsp;
                                    <?php $phone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "+1 ($1) $2-$3", $technician['Phone_1']); echo($phone); ?>
                                    <?php if (!empty($technician['PhoneExt_1'])) { ?>
                                        <small class="text-muted"> [ext: <?php echo($technician['PhoneExt_1']) ?>]</small>
                                    <?php } ?>
                                </div>
                                <div class="col-md-3">
                                    <i class="ace-icon fa fa-envelope blue"></i>&nbsp;<a
                                            href="mailto:"><?php echo($technician['Email']); ?></a>
                                </div>
                                <div class="col-md-2">
                                    <ul class="list-inline">
                                        <li>
                                            <form action="<?php echo(APP_URL . $XFA["delTech"]); ?>" method="POST"
                                                  class="deleteTechForm">
                                                <input type="hidden" name="User_ID"
                                                       value="<?php echo($technician["User_ID"]); ?>">
                                                <input type="hidden" name="tv"
                                                       value="<?php echo(md5(SEED . $technician["User_ID"])); ?>">
                                                <input type="hidden" name="delete" value="1">
                                                <button class="btn btn-white btn-xs" style="border: none !important"
                                                        type="submit" title="Remove this Technician">
                                                    <i class="ace-icon fa fa-trash-o red bigger-140"></i>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-2">&nbsp;</div>
                        <div class="col-md-10">
                            <a title="Add"
                               href="<?php echo(APP_URL . $XFA["addTech"] . "&tid=" . $model['PkID'] . "&tv=" . md5(SEED . $model['PkID'])); ?>">
                                <span class="green"><i class="ace-icon fa fa-plus"></i></span>
                            </a>Add a Technician
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

<?php } ?>

<div class="row">
    <table class="margin-6" width="100%" cellpadding="0" cellspacing="0" border="0">
        <?php if ($listSwitch == 0) { ?>
            <tr>
                <td align="center">
                    <a href="<?= APP_URL . $XFA['list'] ?>&show_delete=1" class="btn btn-xs btn-success">
                        <i class="fa-eraser fa ace-icon"></i>Show Deleted Integrators</a>
                </td>
            </tr>
        <?php } else { ?>
            <tr>
                <td align="center">
                    <a href="<?= APP_URL . $XFA['list'] ?>&show_delete=0" class="btn btn-xs btn-success">
                        <i class="fa-reply fa ace-icon"></i>Back to available Integrators</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<div class="space-12"></div>

<?= $Utils->displayIconHelp() ?>

<form action="<?php if ($listSwitch == 0) {
    echo(APP_URL . $XFA["delete"]);
} else {
    echo(APP_URL . $XFA["undelete"]);
}; ?>" method="POST" id="deleteForm">
    <input type="hidden" name="Integrator_ID" id="Integrator_ID" value="">
    <input type="hidden" name="v" id="v" value="">
</form>

<div id="dialog-confirm" class="hide">
    <div class="alert alert-info bigger-110">
        This integrator will be <?php if ($listSwitch == 0) {
            echo("removed");
        } else {
            echo("recovered");
        }; ?>
    </div>
</div>

<div id="dialog-delete" class="hide">
    <div class="alert alert-info bigger-110">
        This technician will be removed.
    </div>
</div>

<script>
    $(function () {
        $(".js_deleteBtn").on("click", function (e) {
            e.stopPropagation(); //prevent open row
            var id = $(this).data("id");
            var v = $(this).data("v");
            $("#Integrator_ID").val(id);
            $("#v").val(v);
            console.log();
            $("#dialog-confirm")
                .removeClass('hide')
                .dialog({
                    modal: true,
                    title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle red'></i> Delete this Integrator?</h4></div>",
                    title_html: true,
                    buttons: [
                        {
                            html: "<i class='fa ace-icon fa-trash-o bigger-110'></i> Delete",
                            "class": "btn btn-danger btn-xs",
                            click: function () {
                                $(this).dialog("close");
                                $("#deleteForm").submit();
                            }
                        },
                        {
                            html: "<i class='fa fa-times ace-icon bigger-110'></i> Cancel",
                            "class": "btn btn-xs",
                            click: function () {
                                $(this).dialog("close");
                            }
                        }
                    ]
                })
            ;
        });

        $(".js_undeleteBtn").on("click", function (e) {
            e.stopPropagation(); //prevent open row
            var id = $(this).data("id");
            var v = $(this).data("v");
            $("#Integrator_ID").val(id);
            $("#v").val(v);
            console.log();
            $("#dialog-confirm")
                .removeClass('hide')
                .dialog({
                    modal: true,
                    title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle blue'></i> Undelete this Integrator?</h4></div>",
                    title_html: true,
                    buttons: [
                        {
                            html: "<i class='fa ace-icon fa-recycle bigger-110'></i> Undelete",
                            "class": "btn btn-primary btn-xs",
                            click: function () {
                                $(this).dialog("close");
                                $("#deleteForm").submit();
                            }
                        },
                        {
                            html: "<i class='fa fa-times ace-icon bigger-110'></i> Cancel",
                            "class": "btn btn-xs",
                            click: function () {
                                $(this).dialog("close");
                            }
                        }
                    ]
                })
            ;
        });

        $(".deleteTechForm").on('click', function (e) {
            e.preventDefault();
            var delform = this;
            $("#dialog-delete").removeClass('hide').dialog({
                resizable: false,
                modal: true,
                title: '<div class="widget-header widget-header-small"><h4 class="smaller"><i class="ace-icon fa fa-exclamation-triangle red"></i>Delete this Technician?</h4></div>',
                title_html: true,
                buttons: [
                    {
                        html: '<i class="ace-icon fa fa-times bigger-110"></i>Cancel',
                        "class": "btn btn-xs pull-right",
                        click: function () {
                            $(this).dialog("close");
                        }
                    },
                    {
                        html: '<i class="ace-icon fa fa-trash-o bigger-110"></i>Delete',
                        "class": "btn btn-danger btn-xs pull-right",
                        click: function () {
                            $(this).dialog("close");
                            delform.submit();
                        }
                    }
                ]
            });
        });

        $('.data').hide();

        $('.header a').click(function (e) {
            e.stopPropagation();//prevent open row
        });

        $('.header').click(function () {
//        $(this).find('span').text(function(_, value){return value=='-'?'+':'-'});
            if ($(this).find('span').hasClass('fa-chevron-down')) {
                $(this).find('span').removeClass('fa-chevron-down blue');
                $(this).find('span').addClass('fa-chevron-right');
            } else {
                $(this).find('span').removeClass('fa-chevron-right');
                $(this).find('span').addClass('fa-chevron-down blue');
            }
            console.log("here");
            $(this).nextUntil('tr.header').slideToggle(100); // or just use "toggle()"
        })

    });
</script>

