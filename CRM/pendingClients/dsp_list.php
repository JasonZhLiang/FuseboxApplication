<?php
////////////////////////////////////////////////////////////
// File: dsp_list.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2015-08-05
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in PhpStorm
//		
//
////////////////////////////////////////////////////////////
// Get data from pending_client table
$clients	    = [];
$disableEdit    = '';
$editLink       = '';
$qryStatus      = '';
$qryEULAnc      = 'AND EULADate IS NOT NULL ';

switch ($PARAM['filter']) {
    case FILTER_LIST_EULA:
        $qryStatus = "pending";
        $title     = 'Pending Clients' . '<span class="required"> (No EULA)</span>';
        $qryEULAnc = 'AND EULADate IS NULL ';
        break;
    case FILTER_LIST_PENDING:
        $qryStatus = "pending";
        $title     = 'Pending Clients';
        break;
    case FILTER_LIST_ADDRESS_VALIDATION:
        $qryStatus = "validation";
        $title     = 'Address Validation';
        break;
    case FILTER_LIST_SITEMATCH:
        $qryStatus = "verification";
        $title     = 'Check and Verify Address Against Existing Sites';
        break;
    case FILTER_LIST_REVIEW:
        $qryStatus = "review"; //collect final details
        $title     = 'In Review';
        break;
    case FILTER_LIST_PUBLISH:
        $qryStatus = "published";
        $title     = 'Published';
        $disableEdit = 'disabled';
        break;
}

$sql = "
	SELECT
		PkID,
		PropertyType,
		Parent_ID,
		SiteMatch_ID,
		FirstName,
		LastName,
		SiteDescription,
		SiteCity,
		Phone,
		PhoneType,
		Email,
		SiteName,
		SiteAddress_1,
		PhoneExt,
		PriceLevel,
		Lang,
		Status,
		EULADate
	FROM pending_client
	WHERE DeleteFlag = 0
	  AND Status = :Status $qryEULAnc
	ORDER BY CreateDate DESC
";
$PDOdbPending->prepare($sql);
$PDOdbPending->bind('Status', $qryStatus);
$PDOdbPending->execute();
if ($PDOdbPending->rowCount() > 0) {
	while ($row = $PDOdbPending->getRow()) {
		$row['fullName']    = $row['FirstName'].' '.$row['LastName'];
		$row['PhoneFull']   = $row['Phone'] . (empty($row['PhoneExt']) ? '': ' ext '. $row['PhoneExt']) .' ('. $row['PhoneType'] .')' ;
		$row['Lang']        = $row['Lang'] == 'FR' ? 'F': 'E';
        $row['warning']     = empty($row['EULADate']) ? true: false;
        $row['isTenant']    = $row['PropertyType'] == 1 ? false: true;
        $row['Match']     = $row['isTenant'] ? ! empty($row['Parent_ID']): ! empty($row['SiteMatch_ID']);

        $clients[] = $row;
	}
}
$objClientForm = new PendingClientForm();
?>
<strong>Pending Clients</strong>
<div class="muted">Manage Pending Clients</div>
<hr noshade size="1">

<?php if ( ! empty($error) ) { ?>
	<div class="row">
		<div class="col-md-offset-2 col-md-8">
			<div class="alert alert-danger">
				<div class="row">
					<div class="text-center">
						<?php echo($error); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<?php $objClientForm->renderNavButtons($PARAM['filter']); ?>

<?php if ( ! empty($clients) && is_array($clients) ) { ?>
<h4><?php echo($title); ?></h4>
<table class="table table-hover table-bordered">
	<thead>
	<tr>
		<th width="40"></th>
        <th width="200">Name</th>
        <th width="50">Match</th>
        <th width="50">Tenant</th>
		<th width="250">Phone / Email Address</th>
		<th width="300">Site / Site Description</th>
		<th width="250">Address / City</th>
		<th>Type</th>
		<th class="text-center" width="60">Lang</th>
		<th width="40"></th>
	</tr>
	</thead>
	<?php foreach ($clients as $client) {  // TODO: create edit and delete circuits / buttons
        if (isset($pricingLevels[$client['PriceLevel']])) {
            $priceLevel = $pricingLevels[$client['PriceLevel']];
        } else {
            $priceLevel = 'Invalid value';
        }
        ?>
		<tr>
			<td>
				<a href="<?php echo(APP_URL . $XFA['edit'] .'&c='. $client['PkID']) ?>" class="btn btn-info btn-xs <?php echo($disableEdit); ?>">
					<i class="ace-icon fa fa-edit"></i>
				</a>
			</td>
			<td>
                <?php echo('<b>' . $client['fullName'] . '</b>'); ?>
            </td>
            <td class="text-center">
                <?php if ($client['Match']) { ?>
                    <span class="ace-icon fa fa-check green"></span>
                <?php } ?>
            </td>
            <td class="text-center">
                <?php if ($client['isTenant']) { ?>
                    <span class="ace-icon fa fa-check green"></span>
                <?php } ?>
            </td>
            <td><b><?php echo($client['PhoneFull']) ?></b><br>
                <a href="mailto:<?php echo($client['Email'])?>?subject=ERP%20Signup:%20"><?php echo($client['Email']) ?></a></td>
			<td><b><?php echo($client['SiteName']) ?></b></br>
                <?php echo( $client['SiteDescription']) ?></td>
			<td><b><?php echo($client['SiteAddress_1']) ?></b><br>
			    <?php echo($client['SiteCity']) ?></td>
			<td><?php echo($priceLevel) ?></td>
			<td class="text-center"><?php echo($client['Lang']) ?></td>
			<td>
				<button class="btn btn-danger btn-xs js_deleteBtn <?php echo($disableEdit); ?>" data-client="<?php echo($client['PkID']); ?>" data-v="<?php echo(md5(SEED . $client['PkID'])); ?>">
					<i class="ace-icon fa fa-times"></i>
				</button>
			</td>
		</tr>
	<?php }  ?>
</table>

<?php } else { ?>
	<div class="row">
		<div class="col-xs-offset-3 col-xs-6">
			<div class="panel panel-default">
				<div class="panel-body">
					<h3 class="text-center">No Records Found</h3>
				</div>
			</div>
		</div>
	</div>
<?php } ?>


<div id="dialog-confirm" class="hide">
    <p id="dialog-confirm-msg">This will delete this Client. Are you sure?</p>
</div>
<form action="<?php echo(APP_URL. $XFA["delete"]); ?>" method="post" name="frm_delete" id="frm_delete">
    <input type="hidden" name="PkID" id="PkID" value="">
    <input type="hidden" name="deleteFormSubmitted" id="deleteFormSubmitted" value="1">
    <input type="hidden" name="v" id="v" value="">
</form>


<script>
    $(function(){
        $( ".js_deleteBtn" ).on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var client_ID = $this.data('client');
            var v_ID = $this.data('v');
            var msg = "\n<br /> " +
                    "\nYou are about to remove this Client from the Database.  <br /> \n\n" +
                    "\nAre you certain that you wish to Continue? <br /> \n\n" +
                    "";
            $("#dialog-confirm-msg").html(msg);

            $("#dialog-confirm")
                    .removeClass('hide')
                    .dialog({
                        resizable: false,
                        width: 420,
                        modal: true,
                        title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='icon-warning-sign red'></i> Delete This Client?</h4></div>",
                        title_html: true,
                        buttons: [
                            {
                                html: "<i class='fa fa-undo ace-icon bigger-110'></i>&nbsp; Cancel",
                                "class" : "btn btn-xs pull-right",
                                click: function() {
                                    $( this ).dialog( "close" );
                                    //	when using these dialog-boxes,  returning  true OR false does nothing
                                }
                            },
                            {
                                html: "<i class='fa fa-trash-o ace-icon bigger-110'></i>&nbsp; Delete",
                                "class" : "btn btn-danger btn-xs pull-right",
                                click: function() {
                                    $( this ).dialog( "close" );
                                    $("#PkID").attr("value", client_ID);
                                    $("#v").attr("value", v_ID);
                                    $('#frm_delete').submit();	//	when using these dialog-boxes,  I have to explicitly do this;  `return true;` does nothing
                                }
                            }
                        ]
                    });
        });
    });
</script>