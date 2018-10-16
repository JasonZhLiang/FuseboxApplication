<?php
$curUser_ID = $_SESSION['ADMIN_USER']['User_ID'];


$showDeleted = (isset($_GET['showDeleted'])) && ($_GET['showDeleted'] ==1) ? "1" : "0" ;

// Formulate Query
//$accessLvlFilter = ' AND ( System_Administrator >= '.$_SESSION['ADMIN_USER']['Sys_AdminLvl'] .')';
$accessLvlFilter = '';

$sql = "SELECT u.User_ID, u.FirstName, u.LastName, u.isActive 
		FROM users AS u
		INNER JOIN erpcorp_admin.AdminUsers AS au ON u.User_ID = au.User_ID
		WHERE (u.DeleteFlag = :DeleteFlag)
		  ". $accessLvlFilter ."
		ORDER BY u.isActive DESC, u.FirstName, u.LastName
		";
$PDOdb->prepare($sql);
$PDOdb->bind('DeleteFlag', $showDeleted);
$PDOdb->execute();
$iRowsReturned = $PDOdb->rowCount();
if ( $iRowsReturned == 0 )  {
	$error .= '<br />No Records returned ...';
} else {
	while ($row = $PDOdb->getRow()) {
		$rec = []; // re-init
		// Get record data
		$rec['User_ID']			    = $row['User_ID'];
		$rec['Name']			    = $row['FirstName'] .' ' . $row['LastName'];
		//		$rec['UserLogin']		= $row['UserLogin'];
		$rec['isActive']		    = $row['isActive'];
		$rec['AccessLevel']		    = ''; //= $row['System_Administrator'];
        $rec['AccessLevel_dsp']     = getAccessLevelKey($rec['AccessLevel'], $aAccessLevels);

        // prep Btns
        $rec['userToken']           = md5(SEED.$rec['User_ID']);
		$rec['editQuery']		    = '&id='. $rec['User_ID'] . '&v=' .  $rec['userToken'];
        $rec['editBtn']             = $ACL->canEditBtn($XFA['editUser'], '', $rec['editQuery']);
        $rec['changePwdBtn']	    = $ACL->canCustomBtn($XFA['changePwd'], 'mod', 'btn-info', 'glyphicon glyphicon-pencil', '', '&u='.$rec['User_ID']);
		if($rec['isActive']){
			$rec['activeIcon']    = 'glyphicon glyphicon-ok ';
			$rec['activeBtnType'] = " btn-success";
		}else{
			$rec['activeIcon']    = 'glyphicon glyphicon-warning-sign';
			$rec['activeBtnType'] = " btn-danger";
		}
        $rec['activeBtn']	        = $ACL->canCustomBtn($XFA['editUser'], 'mod', $rec['activeBtnType'], $rec['activeIcon'], '', '&id='. $rec['User_ID']);
        // Store this record
		$recList[] = $rec;
	}
}
// Clean up
unset($rec);

// BEGIN the User-table
?>
<!--  BRANDING  -->
    <strong>Administrative Users</strong>
    <div class="muted">Administrative User Management</div>
    <hr noshade size="1">
    <div class="text-center">
        <?php echo($ACL->canAddBtn($XFA['addUser'], 'Add a User', '&t=add')); ?>
    </div>
    <div class="space-12"></div>
<!--  // BRANDING  -->
<?php
if ( ! empty($_GET['err'])) {
	$error = urldecode( $_GET['err']);
	?>
<div class="row alert alert-danger alert-dismissable">
	<div class="col-xs-3 col-sm-3  text-right"><b>WARNING:</b></div>
	<div class="col-xs-6 col-sm-6  text-center">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<?php echo($error); ?>&nbsp;
	</div><!-- //col -->
</div> <!-- //row -->
<?php } ?>

<table class="table table-hover">
	<?php
	if(! empty($error)){
		?>
		<tr valign="top" bordercolor="#993300" style="border: 1px solid #993300">
			<td valign="middle" style="padding-left:42px;" class="error">
				<?php echo($error); ?>
			</td>
		</tr>
		<?php
	}
	?>

		<thead>
		<tr>
			<th class="TblHeader">&nbsp;</th>
			<th class="TblHeader">Name</th>
			<th class="TblHeader text-center" align="center" width="100">Change Pwd</th>
			<th class="TblHeader text-center" align="center">Active</th>
			<th class="TblHeader text-center" align="center">Access Level</th>
			<th class="TblHeader">&nbsp;</th>
		</tr>
		</thead>
		<?php
		if ( $iRowsReturned > 0 && is_array($recList))  {
			foreach ($recList as $record) {
				?>
				<tr>
					<td class="text-center"><?php echo($record['editBtn']); ?></td>
					<td><?php echo($record['Name']); ?><span class="text-muted"> [ID:<?php echo($record['User_ID']); ?>]</span></td>
					<td class="text-center"><?php echo($record['changePwdBtn']); ?></td>
                    <td class="text-center"><?php echo($record['activeBtn']); ?></td>
					<td class="text-center" width="100"><?php echo($record['AccessLevel_dsp']); ?>&nbsp;</td>
					<td class="text-right" width="25">
                        <?php if($ACL->checkAuth('admin', 'del')) : // Alternate syntax ?>
						<button class="js_DeleteBtn btn btn-danger btn-xs" data-id="<?php echo($record['User_ID']); ?>" data-key="<?php echo($record['userToken']); ?>" title="Delete This User">
							<i class="glyphicon glyphicon-remove"></i>
						</button>
                        <?php endif;  // Alternate syntax ?>
					</td>
				</tr>
				<?php
			}
		}
		?>
</table>
<br>&nbsp;
<?php // $Utils->displayIconHelp(); // TODO: UPDATE ICON-HELP or remove entirely ?>
<?php if($ACL->checkAuth('admin', 'del')) : ?>
<div id="dialog-confirm" class="hide">
	<div>You are about to remove this User from the Database.</div>
	<div>Are you certain that you wish to Continue?</div>
</div>
<form action="<?php echo(APP_URL. $XFA["removeUser"]); ?>" method="post" name="frm_delete" id="frm_delete">
	<input type="hidden" name="deleteFormSubmitted" id="deleteFormSubmitted" value="true">
	<input type="hidden" name="DeleteID" id="DeleteID" value="">
	<input type="hidden" name="vDelete" id="vDelete" value="">
	<input type="hidden" name="DeleteBy" id="DeleteBy" value="<?php echo($curUser_ID); ?>">
</form>

<script type="text/javascript" language="JavaScript">
	<!--
	var deleteForm = document.getElementById('frm_delete');
	$(function(){

		$(".js_DeleteBtn").click(function(e){
			//  handle the click of the delete buttons to ppopulate and submit the delete form
			e.stopPropagation();
			e.preventDefault();
			var btnObj = $(this);
			var deleteID = btnObj.data('id');
			var deltoken = btnObj.data('key');

			var dialog = $("#dialog-confirm").removeClass('hide').dialog({
				resizable: false,
				width: 420,
				modal: true,
				title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='fa fa-exclamation-triangle red'></i> Delete This User?</h4></div>",
				title_html: true,
				buttons: [
					{
						html: "<i class='glyphicon glyphicon-undo  bigger-110'></i>&nbsp; Cancel",
						"class" : "btn btn-xs pull-right",
						click: function() {
							$( this ).dialog( "close" );
							//	when using these dialog-boxes,  returning  true OR false does nothing
						}
					},
					{
						html: "<i class='glyphicon glyphicon-trash  bigger-110'></i>&nbsp; Delete",
						"class" : "btn btn-danger btn-xs pull-right",
						click: function() {
							$( this ).dialog( "close" );
							$("#DeleteID").val(deleteID);
							$("#vDelete").val(deltoken);
							//			deleteForm.submit();	//	when using these dialog-boxes,  I have to explicitly do this;  `return true;` does nothing
							$("#frm_delete").submit();
						}
					}
				]
			});
		});


	});
	//-->
</script>
<?php endif; ?>
<?php
function getAccessLevelKey($myValue, $aIn){
	$myKey = "N/A";
	foreach ($aIn as $key=>$val) {
		if ($val == $myValue ) {
			$myKey =  $key;
		}
	}

	return $myKey;
}