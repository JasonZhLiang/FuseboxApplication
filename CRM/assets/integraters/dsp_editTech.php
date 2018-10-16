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
            <strong>Manage technician</strong>
            <div class="muted"><?php if($addFlag){echo("Add");}else{echo("View/Modify");} ?> a technician</div>
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
            <h4 class="text-center"><?php if($addFlag){echo("Add");}else{echo("View/Modify");} ?> a technician</h4>
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
        <form onsubmit="//event.preventDefault();" action="<?= APP_URL . $XFA['processTech'] ?>"
              class="form-horizontal" method="post" name="frmInput">
            <input type="hidden" name="inputFormSubmitted" value="1"/>
            <input type="hidden" name="ID" value="<?= $model['User_ID'] ?>"/>
            <input type="hidden" name="v" value="<?= $model['vHash'] ?>"/>
            <input type="hidden" name="itid" value="<?= $model['IT_ID'] ?>"/>
            <input type="hidden" name="vit" value="<?= $model['vHashT'] ?>"/>
            <div class="form-group" <?php if($addFlag)echo("style ='display:none'"); ?> >
                <label for="User_ID" class="col-sm-4 control-label no-padding-right">Technician ID:
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="User_ID"
                               id="User_ID" value="<?= $model['User_ID'] ?>" readonly>
                        <span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i>
							</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="FirstName" class="col-sm-4 control-label no-padding-right required">First Name:
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="FirstName"
                               id="FirstName" value="<?= $model['FirstName'] ?>">
                        <span class="input-group-addon">
								<i class="glyphicon glyphicon-pencil"></i>
							</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="LastName" class="col-sm-4 control-label no-padding-right required">Last Name:
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="LastName"
                               id="LasttName" value="<?= $model['LastName'] ?>">
                        <span class="input-group-addon">
								<i class="glyphicon glyphicon-pencil"></i>
							</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="Email" class="col-sm-4 control-label no-padding-right required">Email:
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="Email"
                               id="Email" value="<?= $model['Email'] ?>">
                        <span class="input-group-addon">
								<i class="glyphicon glyphicon-pencil"></i>
							</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="Phone_1" class="col-sm-4 control-label no-padding-right required">Phone:</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="Phone_1" id="Phone_1" value="<?= $model['Phone_1'] ?>">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-pencil"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="PhoneType_1" class="col-sm-4 control-label no-padding-right required">Phone Type:
                </label>
                <div class="col-sm-8">
                    <select class="selectpicker" data-live-search="true" name="PhoneType_1" id="PhoneType_1">
                        <option value="">Choose one</option>
                        <?= $optPhoneType ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="PhoneExt_1" class="col-sm-4 control-label no-padding-right">Phone Extension:</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="PhoneExt_1" id="PhoneExt_1" value="<?= $model['PhoneExt_1'] ?>">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-pencil"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="Comments" class="col-sm-4 control-label no-padding-right">Comments:
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <textarea rows="5" class="form-control date-picker" name="Comments"
                                  id="Comments"><?=$model['Comments'];?></textarea>
                        <span class="input-group-addon">
								<i class="glyphicon glyphicon-pencil"></i>
							</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="Notes" class="col-sm-4 control-label no-padding-right">
                </label>
                <div class="col-sm-8 alert alert-info">
                    * fields are required.
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
</div>
<br>
<?php $Utils->displayIconHelp(); ?>

<script>
    $(function () {
        $(".js_cancel").on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();
            var href = $(this).data("href");
            window.location.href = href;
        });
    });
</script>