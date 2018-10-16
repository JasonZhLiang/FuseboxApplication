<?php
////////////////////////////////////////////////////////////
// File: dsp_list
//		display/view page
//
// Information:
//		Date		- 2014-07-16
//		Author		- Rick	O'S
//		Version		- 2.0
//
// History:
//		- v2.0 2014-07-16: initial development 
//
////////////////////////////////////////////////////////////

?>
<strong>Contacts</strong>
<?php if (empty($_SESSION['ADMIN_USER']['curSite']['Site_ID'])){ ?>
    <div class="muted">Search For Contacts</div>
<?php }else{?>
    <div class="muted">Contacts Management for Site #<?php echo($_SESSION['ADMIN_USER']['curSite']['Site_ID']) ?></div>
<?php } ?>
<hr>
<?php
if ( ! empty($_GET['err'])) $error .= "<br>".urldecode( $_GET['err']);
if ( ! empty($error) ) { ?>
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
<?php if (empty($_SESSION['ADMIN_USER']['curSite']['Site_ID'])){ ?>
<form action="<?php echo(APP_URL . $XFA["process"]); ?>" method="POST" class="form-horizontal">
	<input type="hidden" name="inputFormSubmitted" value="1">
	<div class="row">
		<div class="col-sm-10 col-sm-offset-1">
			<div class="row">
				<div class="">
					Search By:
				</div>
			</div>
			<div class="row">
				<div class="form-group">
					<label for="searchLastName" class="col-sm-2 col-xs-1 control-label no-padding-right"> Last Name </label>
					<div class="col-sm-6 col-xs-8">
						<input type="text" class="col-xs-12 js_field" placeholder="Last Name" id="searchLastName" name="searchLastName" value="<?php echo($searchLastName); ?>">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group">
					<label for="searchEmail" class="col-sm-2 col-xs-1 control-label no-padding-right"> Email </label>
					<div class="col-sm-6 col-xs-8">
						<input type="text" class="col-xs-12 js_field" placeholder="Email" id="searchEmail" name="searchEmail" value="<?php echo($searchEmail); ?>">
					</div>
					<div class="col-sm-4 col-xs-3">
						<button class="btn btn-xs btn-info" type="submit"><i class="fa ace-icon fa-search"></i> Go!</button>
						<button class="btn btn-xs js_clearFields"><i class="fa ace-icon fa-undo"></i> Clear</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="text-center">
			<label>
				<input type="radio" class="ace" name="form-qry-cond" <?php echo($qryStartsWith); ?> value="S">
				<span class="lbl"> Starts With</span>
			</label>
			<label>
				<input type="radio" class="ace" name="form-qry-cond" <?php echo($qryContains); ?> value="C">
				<span class="lbl"> Contains</span>
			</label>
		</div>
	</div>
</form>
<hr>
<?php  } ?>
<div class="text-center">
	<a href="<?php echo(APP_URL . $XFA['add']); ?>" title="Add a new record" class="btn btn-xs btn-success">
		<i class="fa-plus fa ace-icon"></i> Create Contact
	</a>
    <?php if ($listSwitch == 0) { ?>
        <a href="<?= APP_URL . $XFA['process'] ?>&show_delete=1" class="btn btn-xs btn-success">
            <i class="fa-eraser fa ace-icon"></i>Show Deleted Contacts
        </a>
    <?php } else { ?>
        <a href="<?= APP_URL . $XFA['process'] ?>&show_delete=0" class="btn btn-xs btn-success">
            <i class="fa-reply fa ace-icon"></i>Back to available Contacts
        </a>
    <?php } ?>

</div>

<div class="row">
    <table class="margin-6" width="100%" cellpadding="0" cellspacing="0" border="0">

    </table>
</div>

<?php if ( ! empty($users) && is_array($users) ) { ?>
	<div class="space-12"></div>
    <form action="<?php if ($listSwitch == 0)  echo(APP_URL . $XFA["delete"]); else echo(APP_URL . $XFA["undelete"]); ?>" method="post" name="frm_delete" id="frm_delete">
        <input type="hidden" name="DeleteID" id="DeleteID" value="">
        <input type="hidden" name="vDelete" id="vDelete" value="">
    	<input type="hidden" name="DeleteBy" id="DeleteBy" value="<?php echo($admPkID); ?>">
    </form>
    <table class="table table-hover">
        <?php 
			foreach($users as $user) { 
				$emailDisabled = empty($user['Email']) ? 'disabled': '';
                if($user['isActive']){
                    $user['data-active'] = 1;
                    $user['btn-state'] = 'btn-success';
                    $user['btn-text'] = 'YES';
                    //btn-success
                }else{
                    $user['data-active'] = 0;
                    $user['btn-state'] = 'btn-danger';
                    $user['btn-text'] = 'Suspended';
                }
		?>
            <tr>
                <td width="25">
                    <a href="<?php echo(APP_URL . $XFA["edit"] ."&u=". $user["User_ID"]); ?>" class="btn btn-xs btn-info">
                        <i class="ace-icon fa fa-edit"></i>
                    </a>
                </td>
                <td width="300">
                   <!-- <a class="btn btn-yellow btn-xs js_btnLink" href="<?php echo(APP_URL . $XFA['3rdP_proxyLogin'] . $user["my3pSpecs"] ); ?>" target="pubFmi" title="Click to 3rdParty login as this user" name="btn_3pFmi" id="btn_3pFmi" ><i class="ace-icon fa fa-lock"></i></a>-->
                   <!-- <a class="btn btn-success btn-xs" href="<?php echo(APP_URL . $XFA['invoices']); ?>&u=<?php echo($user["User_ID"]); ?>"><i class="ace-icon fa fa-dollar"></i></a>-->
                    <a href="mailto:<?php echo($user["Email"]); ?>" title="<?php echo($user["Email"]); ?>" class="btn btn-white btn-info btn-xs <?php echo($emailDisabled);?>"><i class="ace-icon fa fa-envelope"></i> Email</a>
					<?php echo($user["Fullname"]); ?>&nbsp;<span class="text-muted">[<?php echo($user["User_ID"]); ?>]</span>
                </td>
                <td><?php echo($user["isFR"]); ?></td>
                <td><?php echo($user["Phone"]); ?></td>
                <td><?php echo($user["Region"]); ?></td>
                <td><?php echo($user["Lang"]); ?></td>
                <td>
                    <?php if($user['LoginAttempts']>=10) {?>
                        <button class="btn btn-white btn-danger btn-xs js-locked-out" title="click to unlock" data-u="<?php echo($user['User_ID']); ?>" data-v="<?php echo(md5(SEED . $user['User_ID'])); ?>">
                            <?php $trans->et('Locked') ?>
                        </button>
                    <?php };?>
                </td>
                <td>
                    <?php echo('<button class="btn btn-white '.$user['btn-state'].' btn-xs js-toggle-active" data-active="'.$user['data-active'].'" data-user="' . $user['User_ID'] . '">'.$user['btn-text'].'</button>'); ?>
                </td>
                <td width="25">
                    <a href="<?php echo(APP_URL . 'usersChangePw.home&u='.$user["User_ID"].'&v='.md5(SEED . $user["User_ID"]));?>" class="btn btn-xs btn-info"><i class="ace-icon fa fa-pencil"></i></a>
                </td>
                <td width="25">
                	<?php if ($admPkID == $user['User_ID']) {// cannot delete yourself?>
                    	&nbsp;
                	<?php }else{ // offer a delete button ?>
                        <button class="btn <?php if ($listSwitch == 1) {echo("btn-primary js_undeleteBtn");} else {echo("btn-danger js_deleteBtn");}; ?> btn-xs" id="del_<?php echo($user['User_ID']); ?>" data-key="data-iw-token_<?php echo($user['verifyToken']); ?>">
                            <i class="ace-icon fa <?php if ($listSwitch == 1) {echo("fa-recycle");} else {echo("fa-times");}; ?>"></i>
                        </button>
                	<?php }?>
                 </td>
            </tr>
        <?php } ?>
    </table>
    <?php if (! empty($_SESSION['ADMIN_USER']['curSite']['Site_ID'])){ ?>
        <div class="row">
            <div class="col-xs-12 text-center">
                <a class="btn btn-xs" href="<?php echo(APP_URL . $XFA["siteLink"]."&id=".$_SESSION['ADMIN_USER']['curSite']['Site_ID']."&v=".md5(SEED.$_SESSION['ADMIN_USER']['curSite']['Site_ID'])); ?>"><i class="ace-icon fa fa-rotate-right"></i> <?php $trans->et('Return') ?></a>
            </div>
        </div>
        <div class="space-12"></div>
    <?php } ?>
<?php $Utils->displayIconHelp(); ?>
<?php } ?>
<div id="dialog-confirm" class="hide">
	<p id="dialog-confirm-msg">This will delete this Function. Are you sure?</p>
</div>

<div id="dialog-unlock-confirm" class="hide">
    <p><?php $trans->et("This user has exceeded the maximum number of failed login attempts and is currently locked out. Do you wish to unlock this account?") ?></p>
</div>

<script type="text/javascript" language="JavaScript">
<!--
$(function(){
	//$("#searchForm").focus();

	$("#searchLastName").focus();

	$(".js_navigate").on("click", function(e){
		// prevent form submission
		e.preventDefault();
		e.stopPropagation();

		window.location.href = $(this).attr("href");
	});

	$(".js_clearFields").on("click", function(e){
		// prevent form submission
		e.preventDefault();
		e.stopPropagation();

		$(".js_field").val("");
	});

});


	var deleteForm = document.getElementById('frm_delete');
    $(function(){
	
        $(".js_deleteBtn").click(function(e){
			//  handle the click of the delete buttons to ppopulate and submit the delete form 
			e.stopPropagation();
			e.preventDefault();
            var deleteID = e.currentTarget.id.replace('del_', '');
            var deltoken = $(this).attr('data-key').replace('data-iw-token_', '');
			var msg = "\n<br /> " +
                    "\nYou are about to remove this User from the Database.  <br /> \n\n" +
                    "\nAre you certain that you wish to Continue? <br /> \n\n" +
                    "";
			$("#dialog-confirm-msg").html(msg);
			
			var dialog = $("#dialog-confirm").removeClass('hide').dialog({
				resizable: false,
				width: 420,
				modal: true,
				title_html: true,
				title: "<div class=\"widget-header widget-header-small\"><h4 class='smaller'><i class='icon-warning-sign red'></i> Delete This User?</h4></div>",
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
                			$("#DeleteID").attr("value", deleteID);
                			$("#vDelete").attr("value", deltoken);
							//			deleteForm.submit();	//	when using these dialog-boxes,  I have to explicitly do this;  `return true;` does nothing 
							$("#frm_delete").submit();
						}
					}
				]
			});	
        });

        $(".js_undeleteBtn").click(function(e){
            //  handle the click of the delete buttons to ppopulate and submit the delete form
            e.stopPropagation();
            e.preventDefault();
            var deleteID = e.currentTarget.id.replace('del_', '');
            var deltoken = $(this).attr('data-key').replace('data-iw-token_', '');
            var msg = "\n<br /> " +
                "\nYou are about to recoverd this User from the Database.  <br /> \n\n" +
                "\nAre you certain that you wish to Continue? <br /> \n\n" +
                "";
            $("#dialog-confirm-msg").html(msg);

            var dialog = $("#dialog-confirm").removeClass('hide').dialog({
                resizable: false,
                width: 420,
                modal: true,
                title_html: true,
                title: "<div class=\"widget-header widget-header-small\"><h4 class='smaller'><i class='icon-warning-sign blue'></i> Undelete This User?</h4></div>",
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
                        html: "<i class='fa fa-recycle ace-icon bigger-110'></i>&nbsp; Undelete",
                        "class" : "btn btn-primary btn-xs pull-right",
                        click: function() {
                            $( this ).dialog( "close" );
                            $("#DeleteID").attr("value", deleteID);
                            $("#vDelete").attr("value", deltoken);
                            //			deleteForm.submit();	//	when using these dialog-boxes,  I have to explicitly do this;  `return true;` does nothing
                            $("#frm_delete").submit();
                        }
                    }
                ]
            });
        });

        $('.js-locked-out').on('click', function(){
            var $this   = $(this);
            var id    = $this.data('u');
            var v    = $this.data('v');
            $('#dialog-unlock-confirm')
                .removeClass('hide')
                .dialog(
                    {
                        resizable: false,
                        modal: true,
                        title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle blue'></i> <?php $trans->etjs('Unlock Account') ?></h4></div>",
                        title_html: true,
                        buttons: [
                            {
                                html: "<i class='ace-icon fa fa-times bigger-110'></i>&nbsp; <?php $trans->etjs('Cancel') ?>",
                                "class" : "btn btn-xs pull-right",
                                click: function() {
                                    $( this ).dialog( "close" );
                                }
                            },
                            {
                                html: "<i class='ace-icon fa fa-recycle bigger-110'></i>&nbsp; <?php $trans->etjs('Unlock User') ?>",
                                "class" : "btn btn-xs pull-right btn-primary",
                                click: function() {
                                    $.ajax({
                                        url: '<?php echo(APP_URL . $XFA['unlock']); ?>',
                                        data: {id: id, v: v},
                                        dataType: 'json',
                                        method: 'post',
                                        success: function(result){
                                            if (result.success == 1) {
                                                $this.remove();
                                            }
                                        },
                                        error: function(){
                                            console.log('An error has occurred');
                                        }
                                    });
                                    $( this ).dialog( "close" );
                                }
                            }
                        ]
                    }
                );
        });

        var refActive = [
            {btn: 'btn-danger', text: 'Suspended'},
            {btn: 'btn-success', text: 'YES'}
        ];

        $('.js-toggle-active').on('click', function() {
            var $this = $(this);
            var user = $this.data('user');
            var state = $this.data('active');

            $.ajax({
                url: '<?php echo(APP_URL . $XFA['toggleActive']); ?>',
                data: {user: user, state: state},
                dataType: 'json',
                success: function(result){
                    if (result.success == 1) {
                        $this
                            .removeClass('btn-danger btn-success')
                            .addClass(refActive[result.state].btn)
                            .text(refActive[result.state].text)
                            .data('active', result.state);
                    }
                },
                error: function(){
                    console.log('An error has occurred');
                }
            });
        });
    });
//-->
</script>