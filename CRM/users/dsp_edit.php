<?php
////////////////////////////////////////////////////////////
// File: dsp_edit.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2014-07-18
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in JetBrains PhpStorm
//		
//
////////////////////////////////////////////////////////////

	//		echo('<pre> in the data-model:<br />'. print_r($model, 1). '</pre>');
?>
<strong>Contact Management</strong>
<div class="muted"><?php echo(ucfirst($Fusebox["action"])); ?> a Contact</div>
<hr>
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
<div class="row">
	<div class="col-xs-12">
		<form action="<?php echo(APP_URL . $XFA["process"]); ?>"  name="frmInput" id="frmInput" method="post" class="form-horizontal">
			<input type="hidden" name="inputFormSubmitted" id="inputFormSubmitted" value="1" />
			<input type="hidden" name="id" id="id" value="<?php echo($model['User_ID']); ?>" />
			<div class="form-group">
				<label for="user-ID" class="col-sm-3 control-label no-padding-right"> ID: </label>
				<div class="col-sm-9">
					<input type="text" value="<?php echo($model['User_ID']); ?>" id="user-ID" class="col-xs-10 col-sm-5" disabled="disabled">
				</div>
			</div>
			<div class="form-group">
				<label for="FirstName" class="col-sm-3 control-label no-padding-right">
					<i class="fa ace-icon fa-asterisk text-danger"></i>
					First Name:
				</label>
				<div class="col-sm-9">
					<input type="text" class="col-xs-10 col-sm-5" name="FirstName" placeholder="First Name" id="FirstName" <?php echo($HTML5req);?> value="<?php echo($model['FirstName']); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="LastName" class="col-sm-3 control-label no-padding-right">
					<i class="fa ace-icon fa-asterisk text-danger"></i>
					<b>Last Name</b>:
				</label>
				<div class="col-sm-9">
					<input type="text" class="col-xs-10 col-sm-5" name="LastName" placeholder="Last Name" id="LastName" <?php echo($HTML5req);?> value="<?php echo($model['LastName']); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="LastName" class="col-sm-3 control-label no-padding-right">
					<b>Position / Title</b>:
				</label>
				<div class="col-sm-9">
					<input type="text" class="col-xs-10 col-sm-5" name="PositionTitle" placeholder="Position / Title" id="PositionTitle" value="<?php echo($model['PositionTitle']); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="Email" class="col-sm-3 control-label no-padding-right">
					<i class="fa ace-icon fa-asterisk text-danger"></i>
					Email/Login ID:
				</label>
				<div class="col-sm-9">
                    <?php if($Fusebox["action"] == 'add'){ ?>
                        <input type="email" class="col-xs-10 col-sm-5" name="Email" placeholder="Email" id="Email" <?php echo($HTML5req);?> value="<?php echo($model['Email']);  ?>" autocomplete="off">
                    <?php } else { ?>
                        <p class="form-control-static bigger-110"><?php echo($model['Email']); ?></p>
                    <?php } ?>
				</div>
			</div>


			<?php if($Fusebox["action"] == 'add'){ ?>
			<div class="form-group">
				<label for="Password" class="col-sm-3 control-label no-padding-right">
					<i class="fa ace-icon fa-asterisk text-danger"></i>
					Password:
				</label>
				<div class="col-sm-9">
					<input type="password" class="col-xs-10 col-sm-5" name="Password" placeholder="Password" id="newPassword">
				</div>
			</div>
			<div class="form-group">
				<label for="verifyPW" class="col-sm-3 control-label no-padding-right">
					<i class="fa ace-icon fa-asterisk text-danger"></i>
					Confirm Password: </label>
				<div class="col-sm-9">
					<input type="password" class="col-xs-10 col-sm-5" name="verifyPW" placeholder="Confirm Password" id="verifyPW">
					<span class="help-inline col-xs-12 col-sm-7">
						<span class="middle"><i>(<?php $trans->et('password, minimum 8 characters'); ?>)</i></span>
					</span>

				</div>
			</div>
			<?php } ?>

            
            
            <!-- additional User-info fields //-->   
			<div class="form-group">
                <label for="Language"  class="col-sm-3 control-label no-padding-right">Language Preference</label>
                
				<div class="col-sm-9">
	                <div class="radio">
    	                <label>
        	                <input name="Lang" class="ace" type="radio" value="_EN" <?php echo($model['langEnglishSelected_rb']); ?> >
            	            <span class="lbl"> English</span>
                	    </label>
	                </div>
	
    	            <div class="radio">
        	            <label>
            	            <input name="Lang" class="ace" type="radio" value="_FR" <?php echo($model['langFrenchSelected_rb']); ?>>
                	        <span class="lbl"> French </span>
                    	</label>
	                </div>
				</div>
            </div>
									
             
			<div class="form-group">
				<label for="City" class="col-sm-3 control-label no-padding-right">
					City:
				</label>
				<div class="col-sm-9">
					<input type="text" class="col-xs-10 col-sm-5" name="City" placeholder="City" id="City"  value="<?php echo($model['City']); ?>" maxlength="200">
				</div>
			</div>
            
			<div class="form-group" >
				<label for="Province" class="col-sm-3 control-label no-padding-right"> Province: </label>
                <div class="col-sm-9">
                    <input type="text" class="col-xs-10 col-sm-5" name="Province" placeholder="Province" id="Province"  value="<?php echo($model['Province']); ?>" maxlength="200">
                </div>
			</div> 
               
			<div class="form-group">
				<label for="Phone_1" class="col-sm-3 control-label no-padding-right">Phone: </label>
				<div class="col-sm-2 col-xs-6">
					<input type="text" class="col-xs-12" name="Phone_1" placeholder="Phone" id="Phone_1" value="<?php echo($model['Phone_1']); ?>" maxlength="22">
				</div>
                <div class="col-sm-1 col-xs-3 ">
                    <select name="PhoneType_1" id="PhoneType_1" class="form-control">
                        <option class="disabled text-muted" value="">Phone Type</option>
                        <option <?php if ($model['PhoneType_1'] == 'Cell') echo('selected'); ?>><?php $trans->et('Cell'); ?></option>
                        <option <?php if ($model['PhoneType_1'] == 'Work') echo('selected'); ?>><?php $trans->et('Work'); ?></option>
                    </select>
                </div>
                <div class="col-sm-2 col-xs-3">
                    <label for="Ext" class="col-sm-2 control-label no-padding-left blue">Ext: </label>
                    <input type="text" class="col-sm-3 col-xs-10" name="PhoneExt_1" placeholder="Ext" id="PhoneExt_1"  value="<?php echo($model['PhoneExt_1']); ?>" maxlength="8">
                </div>
			</div>
			<div class="clearfix form-actions">
				<div class="row">
					<div class="text-center">
						<button class="btn btn-xs btn-info" type="submit"><i class="fa ace-icon fa-plus"></i> Save</button>
						<a href="<?php echo(APP_URL . $XFA["return"]); ?>" class="btn btn-xs js_navigate"><i class="fa ace-icon fa-undo"></i> Cancel</a>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<?php if ( ! empty($sitesInfo) ) { ?>
    <div class="space-12"></div>
    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <div class="alert alert-info">
                <div class="row">
                    <div class="text-center">
                        <?php echo($sitesInfo); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if ( ! empty($sites) && is_array($sites) ) { ?>
    <div class="space-12"></div>
    <div class="widget-box widget-color-dark light-border">
        <div class="widget-header">
            <h6 class="widget-title bigger-110">Associated Site(s) with this user</h6>
<!--            <a href="--><?php //echo(APP_URL . $XFA['editSitesList']); ?><!--">-->
<!--                <div class="widget-toolbar">-->
<!--                    <i class="ace-icon fa fa-cog light-blue"></i>-->
<!--                </div>-->
<!--            </a>-->
        </div>
        <div class="widget-body">
            <div class="widget-main">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="TblHeader">Site Name&nbsp;<span class="text-muted">[ID]</span></th>
                        <th class="TblHeader">Description</th>
                        <th class="TblHeader">City</th>
                        <th class="TblHeader">Address</th>
                        <th class="TblHeader">Privileges</th>
                        <th class="text-center" width="10%">Can Invite Others</th>
                        <th class="text-center" width="10%">Emergency Services Pierce Notifications</th>
                        <th class="text-center" width="10%">Emergency Services Broadcast</th>
                    </tr>
                    </thead>
                    <?php foreach($sites as $site) {
                        $isVoice    = $site["isBroadCastVoice"] ? 'green': 'light';
                        $isSMS      = $site["isBroadCastSMS"] ? 'green': 'light';
                        $isEmail    = $site["isBroadCast"] ? 'green': 'light';
                        $isNotify   = $site["isNotified"] ? 'green': 'light';
                        $emailDisabled = empty($user['Email']) ? 'disabled' : '';
                        ?>
                        <tr>
                            <td><?php echo($site["SiteName"]); ?>&nbsp;<span class="text-muted">[<?php echo($site["Site_ID"]); ?>]</span></td>
                            <td><?php echo($site["SiteDescription"]); ?></td>
                            <td><?php echo($site["City"]); ?></td>
                            <td><?php echo($site["Address_1"]); ?></td>
                            <td><?php echo(array_search($site['AccessLevel'], $aAccessLevels)); ?>&nbsp;</td>
                            <td class="text-center"><?php if(empty($site['canInvite'])) echo('No'); else echo('Yes'); ?></td>
                            <td class="text-center"><?php if(empty($site['isNotified'])) echo('No'); else echo('Yes'); ?></td>
                            <td class="text-center">
                                <span class="ace-icon fa fa-comment-o <?php echo($isVoice); ?> bigger-140"></i></span>&nbsp;
                                <span class="ace-icon fa fa-mobile <?php echo($isSMS); ?> bigger-140"></i></span>&nbsp;
                                <span class="ace-icon fa fa-envelope <?php echo($isEmail); ?> bigger-140"></i></span>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="text-center col-xs-12 margin-bottom-10">
                <b><?php $trans->et('Notification Legend') ?></b>: &nbsp;
                <?php $trans->et('Voice') ?>: <span data-rel="tooltip" data-original-title="Contact by voicemail"><i class="ace-icon fa fa-comment-o green bigger-140"></i></span>&nbsp;
                <?php $trans->et('SMS') ?>: <span data-rel="tooltip" data-original-title="Contact by SMS"><i class="ace-icon fa fa-mobile green bigger-140"></i></span>&nbsp;
                <?php $trans->et('Email') ?>: <span data-rel="tooltip" data-original-title="Contact by email"><i class="ace-icon fa fa-envelope green bigger-140"></i></span>
            </div>
        </div>
    </div>
<?php } ?>

<script>
	function clickClear(e){
		console.log(this);
		$(document).trigger("clickClear");
	}

	function clickNew(){
		$(document).trigger("clickNew");
	}

	function clickClearTitle(e){
		console.log(this);
		$(document).trigger("clickClearTitle");
	}

	function clickNewTitle(){
		$(document).trigger("clickNewTitle");
	}
//
//	$(function(){
//
//		var orgValue			= "";
//		var titleValue			= "";
//
//		var orgList				= $("#orgList");
//		var newOrgName			= $("#newOrgName");
//		var orgListContainer	= $("#orgListContainer");
//		var newOrgContainer		= $("#newOrgContainer");
//
//		var title				= $("#PositionTitle");
//		var newTitle			= $("#newTitle");
//		var titleContainer		= $("#TitleContainer");
//		var newTitleContainer	= $("#newTitleContainer");
//
//		$("#FirstName").focus();
//		newOrgContainer.hide();
//		newTitleContainer.hide();
//
//		orgList.select2({
//			formatNoMatches: function(val){
//				orgValue = val;
//				return  '<div class="text-danger text-center">Sorry, no matches found</div>'+
//						'<div class="text-center">You can...</div> '+
//						'<div class="text-center"><button onclick="clickClear();">Retry</button>'+
//						'<button onclick="clickNew();">Add to List</button></div>';
//			},
//			matcher: function(term, text) {
//				var match = text.toUpperCase().indexOf(term.toUpperCase()) >= 0;
//				//console.log(match);
//				return match;
//			},
//			width: "100%",
//			placeholder: "Please choose an organization from this list"
//		});
//
//		$(document)
//				.on("clickClear", function(){
//					$(".select2-input").val("");
//					orgList
//							.val("")
//							.select2("close")
//							.select2("open");
//				})
//				.on("clickNew", function(){
//					orgList.select2("close");
//					orgListContainer.slideUp();
//					newOrgContainer.slideDown();
//					newOrgName
//							.focus()
//							.val(orgValue)
//					;
//				})
//				.on("clickClearTitle", function(){
//					$(".select2-input").val("");
//					title
//							.val("")
//							.select2("close")
//							.select2("open");
//				})
//				.on("clickNewTitle", function(){
//					title.select2("close");
//					titleContainer.slideUp();
//					newTitleContainer.slideDown();
//					newTitle
//							.focus()
//							.val(titleValue)
//					;
//				})
//		;
//
//		title.select2({
//			formatNoMatches: function(val){
//				titleValue = val;
//				return  '<div class="text-danger text-center">Sorry, no matches found</div>'+
//						'<div class="text-center">You can...</div> '+
//						'<div class="text-center"><button onclick="clickClearTitle();">Retry</button>'+
//						'<button onclick="clickNewTitle();">Add to List</button></div>';
//			},
//			matcher: function(term, text) {
//				var match = text.toUpperCase().indexOf(term.toUpperCase()) >= 0;
//				//console.log(match);
//				return match;
//			},
//			width: "100%",
//			placeholder: "Please choose a Title"
//		});
//
//		$(".js_navigate, #cancelBtn").on("click", function(e){
//			e.preventDefault();
//			e.stopPropagation();
//
//			window.location.href = $(this).attr("href");
//		});
//
//	});
</script>