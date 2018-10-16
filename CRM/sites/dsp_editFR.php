<?php
////////////////////////////////////////////////////////////
// File: dsp_editFR.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2014-02-18
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in JetBrains PhpStorm
//		
//
////////////////////////////////////////////////////////////
if ( ! empty($error) ) { ?>
	<div class="row">
		<div class="col-xs-12 text-center text-danger"><b><?php echo($error); ?></b></div>
	</div>
<?php } else { ?>
	<div class="space-12"></div>
<?php }  ?>

<h2 class="blue"><?php $trans->et('Available First Responders') ?></h2>
<div class="row">
	<div class="col-xs-12">
		<h3><?php echo($catList[$category]); ?></h3>
		<?php if ( ! empty($firstRespondersList ) ) {
			foreach($firstRespondersList as $firstResponder) {?>
			<form action="<?php echo (APP_URL . $XFA['process']);?>" method="post" class="form-horizontal addForm" id="siteData">
				<input type="hidden" name="inputFormSubmitted" value="TRUE" >
				<input type="hidden" name="category" value="<?php echo ($category);?>" >
				<input type="hidden" name="FR_ID" value="<?php echo ($firstResponder["PkID"]);?>">
				<div class="row showRow">
					<div class="col-xs-4">
						<button class="btn btn-success btn-xs"><i class="ace-icon fa fa-plus"></i></button>
						<span class="lbl"> <?php echo($firstResponder["Description"]); ?></span>
					</div>
					<div class="col-xs-4">
						<?php echo($firstResponder["City"]); ?>
					</div>
				</div>
			</form>
			<?php }
		}else { ?>
			<div class="row showRow">
				<div class="col-xs-12 text-center text-muted"><i><?php $trans->et('No services available in this category') ?></i></div>
			</div>
		<?php } ?>
	</div>
</div>
<div class="space-12"></div>
<div class="row">
	<div class="col-xs-12 text-center">
		<a class="btn btn-xs" href="<?php echo(APP_URL . $XFA["return"]); ?>"><i class="ace-icon fa fa-rotate-right"></i> <?php $trans->et('Return') ?></a>
	</div>
</div>
<div id="dialog-add" class="hide">
    <p>
        <?php $trans->et('This will add this First Responder for the Property. Click Confirm to continue.') ?>
    </p>
</div>
<script>
    $(function(){
        $( ".addForm" ).on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var that 	= this;
            $('#dialog-add')
                .removeClass('hide')
                .dialog(
                    {
                        resizable: false,
                        modal: true,
                        width: 400,
                        title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='ace-icon fa fa-plus blue'></i> <?php $trans->etjs('Add a FR Service') ?></h4></div>",
                        title_html: true,
                        buttons: [
                            {
                                html: "<i class='ace-icon fa fa-times bigger-110'></i>&nbsp; <?php $trans->etjs('Close') ?>",
                                "class" : "btn btn-xs pull-right",
                                click: function() {
                                    $( this ).dialog( "close" );
                                }
                            },
                            {
                                html: "<i class='ace-icon fa fa-certificate bigger-110'></i>&nbsp; <?php $trans->etjs('Confirm') ?>",
                                "class" : "btn btn-xs pull-right btn-primary",
                                click: function() {
                                    $( this ).dialog( "close" );
                                    that.submit();
                                }
                            }
                        ]
                    }
                );
        });
    });

</script>