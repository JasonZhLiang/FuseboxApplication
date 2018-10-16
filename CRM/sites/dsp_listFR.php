<?php
////////////////////////////////////////////////////////////
// File: dsp_listFR.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2017-04-04
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in PhpStorm
//		
//
////////////////////////////////////////////////////////////
$formInputs = [
        'City'       => '',
        'Province'   => '',
        'Country'    => '',
        'startsWith' => 'checked',
        'contains'   => ''
];
$qryConditions = '';
$errors = [];
$iconState = 1;

$orgsFR = [];
$drillManagerObj = new SysBroadcastDrillManager($PDOdb, $trans);

if ( ! empty($_POST['inputFormSubmitted']) ) {
    $formSearchCond = $_POST['form-qry-cond'];
    $City           = $_POST['City'];
    $Province       = $_POST['Province'];
    $Country        = $_POST['Country'];
    
    $formInputs['City']     = $City;
    $formInputs['Province'] = $Province;
    $formInputs['Country']  = $Country;
    
    // validate inputs
    $validator = new Validate_fields();
    
    $validator->add_text_field('City',      $City,     'text', 'y', 100);
    $validator->add_text_field('Province',  $Province, 'text', 'y', 100);
    $validator->add_text_field('Country',   $Country,  'text', 'y', 100);
    
    if ( ! $validator->validation() ) {
        $errors[] = $validator->create_msg();
    }
    
    if ( ! empty($formSearchCond) && ! in_array($formSearchCond, ['S', 'C']) ) {
        trigger_error('Form tampering detected', E_USER_ERROR);
    }
    if (empty($errors)) {
        // TODO:
        // build query based on search inputs
    }
    
}

// Retrieve array of all Fr Orgs
$isDemo = 1;  //only retrieve demo sites
$orgsFR = $drillManagerObj->getAllFROrgs($isDemo);


?>
<strong>Sites List</strong>
<div class="muted">List of First Responders</div>
<hr>

<div class="margin-bottom-20 text-center">
    <a class="btn btn-purple" href="<?php echo(APP_URL . $XFA['list']); ?>">Sites</a>
    <button class="btn btn-purple btn-white btn-md" disabled>First Responders</button>
</div>
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
<div class="row margin-bottom-20 hide"> <?php // TODO: remove hide when ready to develop ?>
    <div class="col-xs-8 col-xs-offset-2">
        <form action="<?php echo(APP_URL . $XFA["process"]); ?>" method="POST" class="form-horizontal">
            <input type="hidden" name="inputFormSubmitted" value="1">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <div class="form-group">
                        <label for="City" class="col-sm-2 col-xs-1 control-label no-padding-right">Search By: </label>
                        <div class="col-sm-6 col-xs-8">
                            <input type="text" class="col-xs-12 js_field" placeholder="City" id="City" name="City" value="<?php echo($formInputs['City']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-offset-2 col-xs-8 col-xs-offset-2">
                            <input type="text" class="col-xs-12 js_field" placeholder="Province" id="Province" name="Province" value="<?php echo($formInputs['Province']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-offset-2 col-xs-8 col-xs-offset-2">
                            <input type="text" class="col-xs-12 js_field" placeholder="Country" id="Country" name="Country" value="<?php echo($formInputs['Country']); ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="text-center">
                    <label>
                        <input type="radio" class="ace" name="form-qry-cond" <?php echo($formInputs['startsWith']); ?> value="S">
                        <span class="lbl"> Starts With</span>
                    </label>
                    <label>
                        <input type="radio" class="ace" name="form-qry-cond" <?php echo($formInputs['contains']); ?> value="C">
                        <span class="lbl"> Contains</span>
                    </label>
                </div>
            </div>
            <div class="text-center">
                <button class="btn btn-xs btn-info" type="submit"><i class="fa ace-icon fa-search"></i> Go!</button>
                <button class="btn btn-xs js_clearFields"><i class="fa ace-icon fa-undo"></i> Clear</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-xs-8 col-xs-offset-2">
        <table class="table table-condensed">
            <tr>
                <th></th>
                <th>Organization</th>
                <th class="text-center">Schedule System Broadcast Drill</th>
                <th width="25">Users</th>
                <th>City</th>
                <th>Province/State</th>
                <th>Country</th>
            </tr>
            <?php if ( ! empty($orgsFR) ) { ?>
                <?php foreach ($orgsFR as $org) {
                    $colour = $org['UserCount'] > 0 ? 'badge-info': '';
                    $iconColor = $drillManagerObj->evaluateIconColor($org['PkID']);
                    ?>
                    <tr>
                        <td>
                            <a href="<?php echo(APP_URL . $XFA['viewUsers'] .'&o='. $org['PkID'] .'&v='. md5(SEED . $org['PkID'])); ?>" class="btn btn-xs btn-info">
                                <span class="ace-icon fa fa-eye"></span>
                            </a>
                        </td>
                        <td><?php echo($org['Desc_EN']); ?></td>
                        <td class="text-center"><a href="<?php echo(APP_URL . $XFA['sysDrillInfo'] .'&o='. $org['PkID'] .'&v='. md5(SEED . $org['PkID'])); ?>">
                                <span class="ace-icon fa fa-bullhorn fa-2x" style="color:<?php echo $iconColor ?>;"></span>
                            </a></td>
                        <td><span class="badge <?php echo($colour); ?>"><?php echo($org['UserCount']); ?></span></td>
                        <td><?php echo($org['City']); ?></td>
                        <td><?php echo($org['Province']); ?></td>
                        <td><?php echo($org['Country']); ?></td>
                    </tr>
                <?php } ?>
            <?php } else if ( ! empty($isSearch) ) { ?>
                <tr>
                    <td colspan="5" class="text-center text-muted text-italic">No organizations found</td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td colspan="5" class="text-center text-muted text-italic">No organizations registered</td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

