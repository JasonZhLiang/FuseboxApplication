<?php
////////////////////////////////////////////////////////////
// File: dsp_edit
//
// Description:
//		Displays edit/add of items
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
?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            <strong>Manage integrators</strong>
            <div class="muted"><?php if($addFlag){echo("Add");}else{echo("View/Modify");} ?> an Integrator</div>
            <hr noshade size="1">
        </td>
    </tr>
    <tr>
        <td class="sectionNavBar">
            <a href="<?= APP_URL . $XFA['list'] ?>" title="Return to List; abandon changes."> << Return To List</a>
        </td>
    </tr>
</table>
<div class="row">
    <div class="col-xs-offset-3 col-xs-6">
        <div class="row">
            <h4 class="text-center"><?php if($addFlag){echo("Add");}else{echo("View/Modify");} ?> an Integrator</h4>
            <hr noshade size="1">
        </div>
    </div>
</div>
<?php if (!empty($error)) { ?>
    <div class="row">
        <div class="col-xs-offset-3 col-xs-6">
            <div class="alert alert-danger">
                <div class="row">
                    <div class="col-xs-8 col-xs-offset-2"><?= $error ?></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<div class="row">
    <div class="col-sm-6 col-sm-offset-2">
        <form onsubmit="//event.preventDefault();" action="<?= APP_URL . $XFA['process'] ?>"
              class="form-horizontal" method="post" name="frmInput">
            <input type="hidden" name="inputFormSubmitted" value="1"/>
            <input type="hidden" name="ID" value="<?= $model['PkID'] ?>"/>
            <input type="hidden" name="v" value="<?= $model['vHash'] ?>"/>
            <div class="form-group" <?php if($addFlag)echo("style ='display:none'"); ?> >
                <label for="PkID" class="col-sm-4 control-label no-padding-right">ID:</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="PkID" id="PkID" value="<?= $model['PkID'] ?>" readonly>
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-lock"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="Name" class="col-sm-4 control-label no-padding-right required">Name:</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="Name" id="Name" value="<?= $model['Name'] ?>">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-pencil"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="PrimaryContact" class="col-sm-4 control-label no-padding-right required">Primary Contact:</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="PrimaryContact" id="PrimaryContact" value="<?= $model['PrimaryContact'] ?>">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-pencil"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="Phone" class="col-sm-4 control-label no-padding-right required">Phone:</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="Phone" id="Phone" value="<?= $model['Phone'] ?>">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-pencil"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="PhoneType" class="col-sm-4 control-label no-padding-right required">Phone Type:
                </label>
                <div class="col-sm-8">
                    <select class="form-control selectpicker" data-live-search="true" name="PhoneType" id="PhoneType">
                        <option value="">Choose one</option>
                        <?= $optPhoneType ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="PhoneExt" class="col-sm-4 control-label no-padding-right">Phone Extension:</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="PhoneExt" id="PhoneExt" value="<?= $model['PhoneExt'] ?>">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-pencil"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="Address" class="col-sm-4 control-label no-padding-right required">Address:</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="Address" id="Address" value="<?= $model['Address'] ?>">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-pencil"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="City" class="col-sm-4 control-label no-padding-right required">City:</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="City" id="City" value="<?= $model['City'] ?>">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-pencil"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="PostalCode" class="col-sm-4 control-label no-padding-right required">Postal Code/Zip code:</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="PostalCode" id="PostalCode" value="<?= $model['PostalCode'] ?>">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-pencil"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="Province" class="col-sm-4 control-label no-padding-right required">Province:</label>
                <div class="col-sm-8">
                    <select class="form-control selectpicker" data-live-search="true" name="Province" id="Province">
                        <option value="">Choose one</option>
                        <?= $optProvinces ?>
                    </select>
                </div>
            </div>
<!--            <div class="form-group">
                <label for="Country" class="col-sm-4 control-label no-padding-right required">Country:</label>
                <div class="col-sm-8">
                    <select class="form-control selectpicker" id="Country" name="Country">
                        <option class="disabled text-muted" value=""></option>
                        <option <?php /*if ($model['Country'] == 'Canada') echo('selected'); */?>>Canada</option>
                        <option <?php /*if ($model['Country'] == 'US') echo('selected'); */?>>US</option>
                    </select>
                </div>
            </div>-->
            <div class="form-group">
                <label for="Country" class="col-sm-4 control-label no-padding-right required">Country:</label>
                <div class="col-sm-8">
                    <div class="radio padding-left-10">
                        <label>
                            <input type="radio" name="Country" id="Canada" value="Canada" <?php echo($chkIsCanada); ?>>Canada&nbsp;&nbsp;&nbsp;
                        </label>
                        <label>
                            <input type="radio" name="Country" id="US" value="US" <?php echo($chkIsUS); ?>>US
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="Notes" class="col-sm-4 control-label no-padding-right">Notes:</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <textarea rows="2" class="form-control date-picker" name="Notes"
                                  id="Notes"><?=$model['Notes'];?></textarea>
                        <span class="input-group-addon">
								<i class="glyphicon glyphicon-pencil"></i>
							</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-offset-6">
                <button name="btnSubmit" value="Submit" class="btn btn-sm btn-success" title="Click to save">
                    <i class="fa ace-icon fa-floppy-o"></i> Click to save
                </button>
                <button name="btnCancel" value="Cancel" class="btn btn-sm js_cancel" data-href="<?= APP_URL . $XFA['list'] ?>" title="Cancel, Abandon Changes">
                    <i class="fa ace-icon fa-rotate-left"></i> Cancel
                </button>
            </div>
        </form>
    </div>
    <?php if(!$addFlag){ ?>
    <div class="col-sm-8 col-sm-offset-2">
        <hr noshade size="1">
        <form action="<?php echo (APP_URL . $XFA['process']);?>" method="post" class="form-horizontal" id="comments">
            <div class="form-group">
                <label class="col-xs-3 control-label no-padding-right">Comments:</label>
                <div class="col-xs-8">
                    <div class="row lastRow" style="padding-left: 10px">
                        <input type="text" class="col-sm-8 js_commentAdd" data-it="<?php echo($model['PkID']); ?>" value="" placeholder="click add an comment" maxlength="255">
                        <div class="col-sm-1">
                            <a href="#" class="btn btn-xs btn-success js_saveComment">
                                <i class="ace-icon fa fa-plus"></i>
                            </a>
                        </div>
                    </div>
                    <?php if ( ! empty($comments) ) {
                        foreach ($comments as $PkID=>$comment) { ?>
                            <label class="col-xs-8 control-label" id="label_<?php echo($PkID); ?>">
                                <small class="text-muted" >
                                    Created by <?php echo($commentUser[$PkID]); ?> at <?php echo($commentModifyDate[$PkID]); ?>
                                </small>
                            </label>
                            <div class="row" style="padding-left: 10px">
                                <input readonly type="text" class="col-sm-8 js_comment" id="comment_<?php echo($PkID); ?>" data-comment="<?php echo($PkID); ?>" name="Comment[<?php echo($PkID); ?>]" value="<?php echo ($comment); ?>" placeholder="" maxlength="255">
                                <div class="col-sm-1 hidden">
                                    <a href="#" class="btn btn-xs btn-danger js_deleteComment" data-delete="<?php echo($PkID); ?>"><i class="ace-icon fa fa-times"></i> </a>
                                </div>
                            </div>
                        <?php }
                    } ?>
                </div>
            </div>
            <div class="col-sm-12 text-center">
                <a href="<?php echo (APP_URL . $XFA["list"]); ?>" class="btn btn-sm">Return</a>
            </div>
        </form>
    </div>
    <?php } ?>
</div>
<br>
<?php $Utils->displayIconHelp(); ?>
<div id="confirmDelete" class="hide">
    <div class="alert alert-info bigger-110">
        This Comment will be deleted.
    </div>
</div>
<script>
    $(function () {
        $(".js_cancel").on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();
            var href = $(this).data("href");
            window.location.href = href;
        });
        function linkDeleteBtns () {
            $(".js_deleteComment").on("click", function(e){
                e.preventDefault();
                e.stopPropagation();
                var btnOjb 		= $(this);
                var id 			= btnOjb.data("delete");
                var targetOjb	= $("#comment_"+id);
                var targetOjbL	= $("#label_"+id);
                var dialog = $( "#confirmDelete" ).removeClass('hide').dialog({
                    resizable: false,
                    modal: true,
                    title: '<div class="widget-header widget-header-small"><h4 class="smaller"><i class="ace-icon fa fa-exclamation-triangle red"></i> Delete this Comment?</h4></div>',
                    title_html: true,
                    buttons: [
                        {
                            html: '<i class="ace-icon fa fa-times bigger-110"></i>&nbsp; Cancel',
                            "class" : "btn btn-xs pull-right",
                            click: function() {
                                $( this ).dialog( "close" );
                            }
                        },
                        {
                            html: '<i class="ace-icon fa fa-trash-o bigger-110"></i>&nbsp; Delete',
                            "class" : "btn btn-danger btn-xs pull-right",
                            click: function() {
                                var that = this;
                                $.ajax({
                                    url: "<?php echo(APP_URL.$XFA["deleteComment"]); ?>",
                                    data: {id:id},
                                    dataType: "text",
                                    success : function(data){
                                        if (data == "1") {
                                            targetOjb.remove();
                                            targetOjbL.remove();
                                            btnOjb.remove();
                                            $( that ).dialog( "close" );
                                        }
                                    },
                                    error: function (jqHXR, error, text) {

                                    }
                                });
                            }
                        }
                    ]
                });
                return false;
            });
        }
        function linkSaveBtns () {
            $('.js_saveComment').on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                $('.js_commentAdd').trigger('add.comment');
                return false;
            });
        }
        function linkUpdateFields () {
            $('.js_comment').on('focusout', function(e){
                e.preventDefault();
                e.stopPropagation();
                var targetOjb	= $(this);
                var id 			= targetOjb.data('comment');
                var newValue	= targetOjb.val();
                $.ajax({
                    url: '<?php echo(APP_URL.$XFA['updateComment']); ?>',
                    data: {
                        id		: id,
                        comment	: newValue
                    },
                    dataType: 'text',
                    success : function(data){
                        if (data == '1') {
                            <?php // console.log('Success!'); ?>
                        }
                    },
                    error: function (jqHXR, error, text) {
                    }
                });
            });
        }
        function linkAddField () {
            $(".js_commentAdd").on("add.comment", function(e){
                e.preventDefault();
                e.stopPropagation();
                var targetOjb	= $(this);
                var id			= targetOjb.data("it");
                var newValue	= targetOjb.val();
                if ( ! newValue) return;
                $.ajax({
                    url: "<?php echo(APP_URL.$XFA["addComment"]); ?>",
                    data: {
                        id		: id,
                        comment	: newValue
                    },
                    dataType: "json",
                    success : function(data){
                        if (data.status == "1") {
                            targetOjb
                                .removeClass("js_commentAdd")
                                .addClass("js_comment")
                                .attr("id", "comment_"+data.id)
                                .attr("data-comment", data.id)
                                .attr("name", "Comment["+data.id+"]")
                                .attr("readonly", true)
                                .before(
                                    '<label class="col-xs-8 control-label" id="label_'+data.id+'"><small class="text-muted" >'+
                                    'Created by '+data.user+ ' at ' + data.date +
                                    '</small></label>'
                                )
                                .after(
                                    '<div class="col-sm-1 hidden">'+
                                    '<button class="btn btn-xs btn-danger js_deleteComment" data-delete="'+data.id+'"><i class="ace-icon fa fa-times"></i> </button>'+
                                    '</div>'
                                );
                            $(".js_saveComment").remove();
                            $(".lastRow:first").before(
                                '<div class="row lastRow" style="padding-left: 10px">'+
                                '<input type="text" class="col-sm-8 js_commentAdd" value="" data-it="'+id+'" placeholder="add another comment" maxlength="255">'+
                                '<div class="col-sm-1">'+
                                '<button class="btn btn-xs btn-success js_saveComment"><i class="ace-icon fa fa-plus"></i> </button>'+
                                '</div>'+
                                '</div>'
                            );

                            $(".js_deleteComment").off();
                            $(".js_comment").off();
                            $(".js_commentAdd").off();
                            $(".js_commentAdd:first").focus();
                            linkDeleteBtns();
                            linkSaveBtns();
                            linkUpdateFields();
                            linkAddField();
                        }
                    },
                    error: function (jqHXR, error, text) {
                    }
                });
            })
            .on("keydown", function(e){
                if ( e.which == 13 ) {
                    e.preventDefault();
                    $(this).trigger("focusout");
                }
            });
        }
        linkDeleteBtns();
        linkSaveBtns();
        linkUpdateFields();
        linkAddField();
        $('#Province').on('change', function () {
            var pcode = $(this).val();
            if ( ! pcode) return;
            $.ajax({
                url: '<?php echo(APP_URL.$XFA['updateRadio']); ?>',
                data: {
                    pcode	: pcode
                },
                dataType: 'json',
                success : function(data){
                    if (data == '1') {
                        $('#Canada').prop('checked',true);
                        $('#US').prop('checked',false);
                    }
                    if (data == '99') {
                        $('#Canada').prop('checked',false);
                        $('#US').prop('checked',true);
                    }
                },
                error: function (jqHXR, error, text) {
                }
            });
        });
    });
</script>