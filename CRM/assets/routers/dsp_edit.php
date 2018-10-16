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
            <strong>Assets Inventory</strong>
            <div class="muted"><?php if($addFlag){echo("Add");}else{echo("View/Modify");} ?> an Device</div>
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
            <h4 class="text-center"><?php if($addFlag){echo("Add");}else{echo("View/Modify");} ?> a Device</h4>
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
    <div class="col-sm-offset-1 col-sm-4 text-right">
        <img src="/_admin/images/Router.jpg" class="img-rounded img-responsive" style="margin: 0 auto"alt="Router sample" width="300" height="140">
        <img src="/_admin/images/router2.jpg" class="img-rounded" alt="Router sample" width="230" height="340">
        <img src="/_admin/images/router1.jpg" class="img-rounded" alt="Router sample" width="220" height="140">
    </div>
    <div class="col-sm-6">
        <form onsubmit="//event.preventDefault();" action="<?= APP_URL . $XFA['process'] ?>"
              class="form-horizontal" method="post" name="frmInput">

            <input type="hidden" name="inputFormSubmitted" value="1"/>
            <input type="hidden" name="ID" value="<?= $model['Router_ID'] ?>"/>
            <input type="hidden" name="v" value="<?= $model['vHash'] ?>"/>

            <div class="form-group" <?php if($addFlag)echo("style ='display:none'"); ?> >
                <label for="Router_ID" class="col-sm-4 control-label no-padding-right">Router ID:
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="Router_ID"
                               id="Router_ID" value="<?= $model['Router_ID'] ?>" readonly>
                        <span class="input-group-addon">
								<i class="glyphicon glyphicon-lock"></i>
							</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="PID" class="col-sm-4 control-label no-padding-right <?php if($addFlag){echo("required");}else{echo("");} ?>">PID:
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="PID"
                               id="PID" value="<?= $model['PID'] ?>" <?php if($addFlag){echo("autofocus");}else{echo("readonly");} ?>>
                        <span class="input-group-addon">
								<i class="<?php if($addFlag){echo("glyphicon glyphicon-pencil");}else{echo("glyphicon glyphicon-lock");} ?>"></i>
							</span>
                    </div>
                </div>
            </div>

<!--            <div class="form-group">
                <label for="TAN" class="col-sm-4 control-label no-padding-right <?php /*if($addFlag){echo("required");}else{echo("");} */?>">TAN Number:
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="TAN"
                               id="TAN" value="<?/*= $model['TAN'] */?>" <?php /*if($addFlag){echo("");}else{echo("readonly");} */?>>
                        <span class="input-group-addon">
								<i class="<?php /*if($addFlag){echo("glyphicon glyphicon-pencil");}else{echo("glyphicon glyphicon-lock");} */?>"></i>
							</span>
                    </div>
                </div>
            </div>-->

            <div class="form-group">
                <label for="ChassisSN" class="col-sm-4 control-label no-padding-right <?php if($addFlag){echo("required");}else{echo("");} ?>">Chassis SN:
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="ChassisSN"
                               id="ChassisSN" value="<?= $model['ChassisSN'] ?>" <?php if($addFlag){echo("");}else{echo("readonly");} ?>>
                        <span class="input-group-addon">
								<i class="<?php if($addFlag){echo("glyphicon glyphicon-pencil");}else{echo("glyphicon glyphicon-lock");} ?>"></i>
							</span>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label for="MAC" class="col-sm-4 control-label no-padding-right <?php if($addFlag){echo("required");}else{echo("");} ?>">MAC:
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="MAC"
                               id="MAC" value="<?= $model['MAC'] ?>" <?php if($addFlag){echo("");}else{echo("readonly");} ?>>
                        <span class="input-group-addon">
								<i class="<?php if($addFlag){echo("glyphicon glyphicon-pencil");}else{echo("glyphicon glyphicon-lock");} ?>"></i>
							</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="Provider" class="col-sm-4 control-label no-padding-right required">Provider:
                </label>
                <div class="col-sm-8">
                    <select class="selectpicker" data-live-search="true" name="Provider" id="Provider">
                        <option value="">Choose one</option>
                        <?= $optProviders ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="RouterStatus" class="col-sm-4 control-label no-padding-right required">RouterStatus:
                </label>
                <div class="col-sm-8">
                    <select name="RouterStatus" id="RouterStatus">
                        <option value="">Choose one</option>
                        <?= $optRouterStatus ?>
                    </select>
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

            <button name="btnSubmit" value="Submit" class="btn btn-sm btn-success" title="Click to save"><i
                        class="fa ace-icon fa-floppy-o"></i> Click to save
            </button>

            <button name="btnCancel" value="Cancel" class="btn btn-sm js_cancel"
                    data-href="<?= APP_URL . $XFA['list'] ?>" title="Cancel, Abandon Changes"><i
                        class="fa ace-icon fa-rotate-left"></i> Cancel
            </button>
        </form>
    </div>
</div>
<br>
<?php $Utils->displayIconHelp(); ?>

<script>
    $(":input").keypress(function(e){
        if (e.which == '10' || e.which == '13') {
            e.preventDefault();
            var focus = $(":focusable");
            var current = focus.index(this),
                next = focus.eq(current+1).length ? focus.eq(current+1) : focus.eq(0);
            next.focus();
        }
    });

    $(function () {
        $(".js_cancel").on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();

            var href = $(this).data("href");
            window.location.href = href;
        });
    });
</script>