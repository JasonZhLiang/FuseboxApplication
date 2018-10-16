<strong>Sites</strong>

<?php if (empty($_SESSION['tagetUser']['User_ID'])){ ?>
    <div class="muted">Search For Sites</div>
<?php }else{?>
    <div class="muted">Sites Management for User #<?php echo($_SESSION['tagetUser']['User_ID']) ?></div>
<?php } ?>
<hr>
<?php if ( ! empty($_GET['err'])) $error .= "<br>".urldecode( $_GET['err']); if ( ! empty($error) ) { ?>
<div class="row">
	<div class="col-md-offset-2 col-md-8">
		<div class="alert alert-danger">
			<div class="row">
				<div class="text-center"><?php echo($error); ?></div>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<?php if (empty($_SESSION['tagetUser']['User_ID'])){ ?>
<form action="<?php echo(APP_URL . $XFA["process"]); ?>" method="POST" class="form-horizontal">
	<input type="hidden" name="inputFormSubmitted" value="1">
    <input type="hidden" name="s" id="s" value="<?php echo($searchSiteName); ?>">
	<div class="row">
		<div class="col-sm-10 col-sm-offset-1">
            <div class="form-group">
                <div class="col-sm-3 control-label">Search By: Site Name/Address</div>
                <div class="col-sm-6 col-xs-8">
                    <input type="text" class="col-xs-12 js_field" placeholder="Site Name" id="searchSiteName" name="searchSiteName" value="<?php echo($searchSiteName); ?>">
                </div>
                <div class="col-sm-3 col-xs-3">
                    <button class="btn btn-xs btn-info" type="submit"><i class="fa ace-icon fa-search"></i> Go!</button>
                    <button class="btn btn-xs js_clearFields"><i class="fa ace-icon fa-undo"></i> Clear</button>
                </div>
            </div>
		</div>
	</div>
	<div class="row">
		<div class="text-center">
            <label><span class="lbl infobox-blue">(Note: Search Site by typing Site ID or key words contained in Name or Address fields)</span></label>
		</div>
	</div>
</form>
<?php } ?>
<?php if ( ! empty($sites) && is_array($sites) ) { ?>
	<div class="space-12"></div>
    <form action="<?php echo(APP_URL. $XFA["delete"]); ?>" method="post" name="frm_delete" id="frm_delete">
        <input type="hidden" name="DeleteID" id="DeleteID" value="">
        <input type="hidden" name="vDelete" id="vDelete" value="">
    </form>
    <table class="table table-hover">
        <thead>
        <tr>
            <td width="25"></td>
            <td><b>Site Name&nbsp;<span class="text-muted">[ID]</span></b></td>
            <td><b>Description</b></td>
            <td><b>Prime Contact</b></td>
            <td><b>City</b></td>
            <td><b>Address</b></td>
            <td width="25"></td>
        </tr>
        </thead>
        <?php foreach($sites as $site) { ?>
            <tr>
                <td><a href="<?php echo(APP_URL . $XFA["edit"] ."&id=". $site["Site_ID"]."&v=".$site['verifyToken']."&s=".$searchSiteName); ?>" class="btn btn-xs btn-info"><i class="ace-icon fa fa-edit"></i></a></td>
                <td><a href="<?php echo(APP_URL . $XFA["edit"] ."&id=". $site["Site_ID"]."&v=".$site['verifyToken']."&s=".$searchSiteName); ?>"><?php echo($site["SiteName"]); ?></a>&nbsp;<span class="text-muted">[<?php echo($site["Site_ID"]); ?>]</span></td>
                <td><?php echo($site["SiteDescription"]); ?></td>
                <td><?php echo($site["FullName"]); ?></td>
                <td><?php echo($site["City"]); ?></td>
                <td><?php echo($site["Address_1"]); ?></td>
                <td width="25"><button class="js_DeleteBtn btn btn-danger btn-xs" id="del_<?php echo($site['Site_ID']); ?>" v="token_<?php echo($site['verifyToken']); ?>" title="Delete This User"><i class="fa fa-times ace-icon"></i></button></td>
            </tr>
        <?php } ?>
    </table>
    <?php if (! empty($_SESSION['tagetUser']['User_ID'])){ ?>
        <div class="row">
            <div class="col-xs-12 text-center">
                <a class="btn btn-xs" href="<?php echo(APP_URL . $XFA["userLink"]."&u=".$_SESSION['tagetUser']['User_ID']."&v=".md5(SEED.$_SESSION['tagetUser']['User_ID'])); ?>"><i class="ace-icon fa fa-rotate-right"></i> <?php $trans->et('Return') ?></a>
            </div>
        </div>
        <div class="space-12"></div>
    <?php } ?>
<?php $Utils->displayIconHelp(); ?>
<?php } ?>
<div id="dialog-confirm" class="hide">
	<p id="dialog-confirm-msg">This will delete this Function. Are you sure?</p>
</div>
<script type="text/javascript" language="JavaScript">
    $(function(){
        $("#searchSiteName").select();
        $(".js_clearFields").on("click", function(e){
            e.preventDefault();
            e.stopPropagation();
            $(".js_field").val("");
        });
        $(".js_DeleteBtn").click(function(e){
            e.stopPropagation();
            e.preventDefault();
            var deleteID = e.currentTarget.id.replace('del_', '');
            var deltoken = $(this).attr('v').replace('token_', '');
            var msg = "\n<br /> " +
                    "\nYou are about to remove this Site from the Database.  <br /> \n\n" +
                    "\nAre you certain that you wish to Continue? <br /> \n\n" +
                    "";
            $("#dialog-confirm-msg").html(msg);
            var dialog = $("#dialog-confirm").removeClass('hide').dialog({
                resizable: false,
                width: 420,
                modal: true,
                title_html: true,
                title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='icon-warning-sign red'></i> Delete This Site?</h4></div>",
                buttons: [
                    {
                        html: "<i class='fa fa-undo ace-icon bigger-110'></i>&nbsp; Cancel",
                        "class" : "btn btn-xs pull-right",
                        click: function() {
                            $( this ).dialog( "close" );
                        }
                    },
                    {
                        html: "<i class='fa fa-trash-o ace-icon bigger-110'></i>&nbsp; Delete",
                        "class" : "btn btn-danger btn-xs pull-right",
                        click: function() {
                            $( this ).dialog( "close" );
                            $("#DeleteID").attr("value", deleteID);
                            $("#vDelete").attr("value", deltoken);
                            $("#frm_delete").submit();
                        }
                    }
                ]
            });
        });
    });
</script>