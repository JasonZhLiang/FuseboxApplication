<?php

$error = "";
$curSite	= $_SESSION["ADMIN_USER"]["curSite"];
$id			= $curSite["Site_ID"];
if ( ! empty($_POST["deleteFromList"]) ) {
	$FR_ID = $_POST["FR_ID"];
	$sql = "DELETE FROM site_fr_xref
			WHERE Site_ID = :Site_ID
			AND FR_ID	= :FR_ID
	;";
    $PDOdb->prepare($sql);
    $PDOdb->bind('Site_ID', $id);
    $PDOdb->bind('FR_ID', $FR_ID);
	if ( ! $PDOdb->execute()) {
		$error = $trans->t("Delete failed. Try again, or contact the system administrator.");
	}
}
$firstRespondersList = array();
$sql = "SELECT
					fr.PkID,
					fr.Desc". LANG ." AS Description,
					fr.City,
					fr.Category,
					0 AS onList
				FROM fr_orgs AS fr
				WHERE fr.PkID NOT IN (SELECT FR_ID FROM site_fr_xref WHERE Site_ID = :Site_ID)
				UNION
				SELECT
					fr.PkID,
					fr.Desc". LANG ." AS Description,
					fr.City,
					fr.Category,
					1 AS onList
				FROM fr_orgs AS fr
				INNER JOIN site_fr_xref AS x ON x.FR_ID = fr.PkID AND x.Site_ID = :Site_ID
		;";
$PDOdb->prepare($sql);
$PDOdb->bind('Site_ID', $id);
$PDOdb->execute();
if ($PDOdb->rowCount() > 0) {
    while ($row = $PDOdb->getRow()) {
        if ($row["onList"] == 1) {
            $firstRespondersList["onList"][$row["Category"]][]	= $row;
        }else {
            $firstRespondersList["offList"][$row["Category"]][]	= $row;
        }
    }
}

$catList = array(
	1=>$trans->t("Police"),
	2=>$trans->t("Fire Services"),
	3=>$trans->t("Paramedic / Other")
);
if ( ! empty($error) ) { ?>
	<div class="row">
		<div class="col-xs-12 text-center text-danger"><?php echo($error); ?></div>
	</div>
<?php } else { ?>
	<div class="space-12"></div>
<?php }  ?>
<h3 class="text-center"><?php $trans->et('Registered With Site') ?></h3>
<div class="row">
	<div class="col-xs-12">
	<?php
	foreach ($catList as $category=>$title) {
		$encrypted = md5(SEED . $category);?>
		<div class="row">

			<div class="col-xs-2">
				<h4 class="blue"><?php echo($title); ?></h4>
			</div>
			<div class="col-xs-4" style="margin-top: 10px;">
				<a class="btn btn-xs btn-success" href="<?php echo(APP_URL . $XFA["add"] ."&cid=". $category ."&vid=". $encrypted); ?>"><i class="ace-icon fa fa-plus"></i> <?php $trans->et('Add A Service') ?></a>
			</div>
		</div>
		<?php if ( ! empty($firstRespondersList["onList"][$category] ) ) {
			foreach($firstRespondersList["onList"][$category] as $firstResponder) {?>
			<form action="<?php echo (APP_URL . $XFA['process']);?>" method="post" class="form-horizontal deleteForm" id="deleteFR_<?php echo ($firstResponder["PkID"]);?>">
				<input type="hidden" name="deleteFromList" value="TRUE" />
				<input type="hidden" name="FR_ID" value="<?php echo ($firstResponder["PkID"]);?>">
				<div class="row showRow">
					<div class="col-xs-4">
						<span class="lbl"><b><?php echo($firstResponder["Description"]); ?></b></span>
					</div>
					<div class="col-xs-4">
						<?php echo($firstResponder["City"]); ?>
					</div>
					<div class="col-xs-4">
						<button class="btn btn-danger btn-xs pull-right"><i class="ace-icon fa fa-times"></i></button>
					</div>
				</div>
			</form>
			<?php }
		} else { ?>
			<div class="row showRow">
				<div class="col-xs-12 text-center text-muted"><i><?php $trans->et('No services registered for this Property') ?></i></div>
			</div>
		<?php }
	} ?>
	</div>
</div>
<div class="space-12"></div>
<div class="row">
	<div class="col-xs-12 text-center">
		<a class="btn btn-xs" href="<?php echo(APP_URL . $XFA["return"]."&id=".$id."&v=".md5(SEED.$id)); ?>"><i class="ace-icon fa fa-rotate-right"></i> <?php $trans->et('Return') ?></a>
	</div>
</div>

<div id="dialog-delete" class="hide">
	<p>
		<?php $trans->et('This will unregister this First Responder for this Property. Click unregister to continue.') ?>
	</p>
</div>

<div id="dialog-add" class="hide">
    <p>
        <?php $trans->et('This will add a First Responder for this Property. Click Confirm to continue.') ?>
    </p>
</div>
<script>
	$(function(){
		$.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
			_title: function(title) {
				var $title = this.options.title || '&nbsp;'
				if( ("title_html" in this.options) && this.options.title_html == true ){
					title.html($title);
				} else {
					title.text($title);
				}
			}
		}));

		$( ".deleteForm" ).on('submit', function(e) {
			e.preventDefault();
			e.stopPropagation();
			var that 	= this;

			var dialog = $( "#dialog-delete" ).removeClass('hide').dialog({
				resizable: false,
				modal: true,
				width: 400,
				title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='ace-icon fa fa-exclamation-triangle red'></i> <?php $trans->etjs('Remove First Responder From Property?') ?></h4></div>",
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
						html: "<i class='ace-icon fa fa-trash bigger-110'></i>&nbsp; <?php $trans->etjs('Unregister') ?>",
						"class" : "btn btn-danger btn-xs pull-right",
						click: function() {
							$( this ).dialog( "close" );
							that.submit();
						}
					}
				]
			});
		});
	});

</script>