<?php 
	if(!$user->HasPermission("admin")){ 
		die(lang("You don't have enough permissions to perform this operation"));
	}

	if(Input::get('save')){
		Options::Update(array(
			"users_can_register" => Input::Get("usersCanRegister") == "on" ? 1 : 0,
			"users_must_confirm_email" => Input::Get("usersMustConfirmEmail") == "on" ? 1 : 0,
			"user_active_by_admin" => Input::Get("userActiveByAdmin") == "on" ? 1 : 0,
			"sitename" => Input::Get("sitename"),
			"min_interval" => Input::Get("minInterval") < 10 ? 10 : Input::Get("minInterval"),
		));
	}
?>
<h4 class="tab-title"><i class="fa fa-tasks"></i> <?php echo lang('GENERAL_SETTINGS'); ?></h4>
<div class="input-group">
	<?php 
	$sitename = Input::Get("sitename") ? Input::Get("sitename")  : Options::Get("sitename");
	?>
	<label for="sitename"><?php echo lang('SITE_NAME'); ?></label>
	<input type="text" name="sitename" class="form-control" id="sitename" placeholder="<?php echo lang('SITE_NAME'); ?>" value="<?php echo $sitename; ?>" />
</div>

<?php
if(Input::Get("usersCanRegister")){
	$usersCanRegister = Input::Get("usersCanRegister") == "on" ? "checked" : "";
} else {
	$usersCanRegister = Options::Get("users_can_register") ? "checked" : "";
}
?>
<div class="input-group">
	<input type="checkbox" class='checkbox-style' id="usersCanRegister" name="usersCanRegister" aria-label="Users can register" <?php echo $usersCanRegister;?> />
	<label for="usersCanRegister"></label>
	<span class="input-text"><?php echo lang('USERS_CAN_REGISTER'); ?></span>
</div>

<div class="input-group">
	<?php 
	if(Input::Get("usersMustConfirmEmail")){
		$usersMustConfirmEmail = Input::Get("usersMustConfirmEmail") == "on" ? "checked" : "";
	} else {
		$usersMustConfirmEmail = Options::Get("users_must_confirm_email") ? "checked" : "";
	}
	?>
	<input type="checkbox" id="usersMustConfirmEmail" name="usersMustConfirmEmail"  class='checkbox-style' aria-label="New users must confirm their email address" <?php echo $usersMustConfirmEmail;?> />
	<label for="usersMustConfirmEmail" ></label>
	<span class="input-text"><?php echo lang('USERS_MUST_CONFIRM_EMAIL'); ?></span>
</div>

<div class="input-group">
	<?php
	if(Input::Get("userActiveByAdmin")){
		$userActiveByAdmin = Input::Get("userActiveByAdmin") == "on" ? "checked" : "";
	} else {
		$userActiveByAdmin = Options::Get("user_active_by_admin") ? "checked" : "";
	}
	?>
	<input type="checkbox" class='checkbox-style' id="userActiveByAdmin" name="userActiveByAdmin" aria-label="New user account must be Activated by an admin" <?php echo $userActiveByAdmin;?> />
	<label for="userActiveByAdmin"></label>
	<span class="input-text"><?php echo lang('New user account must be Activated by an admin'); ?></span>
</div>

<div class="input-group">
	<?php 
	$minInterval = Input::Get("minInterval") ? Input::Get("minInterval")  : Options::Get("min_interval");
	?>
	<label for="minInterval"><?php echo lang('Minimum interval for immediate posting (in seconds)'); ?></label>
	<input type="number" name="minInterval" class="form-control" id="minInterval" placeholder="<?php echo lang('Minimum interval for immediate posting (in seconds)'); ?>" value="<?php echo $minInterval; ?>" />
</div>