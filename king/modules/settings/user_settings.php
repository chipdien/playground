<h4 class="tab-title"><i class="fa fa-user"></i>  <?php echo lang('USER_SETTINGS'); ?></h4>
<label for="username"><?php echo lang('USERNAME')?> (<small><?php echo lang('USERNAME_CAN_NOT_CHANGED'); ?></small>)
</label>
<?php $username = Input::Get("username") == false ?  $user->data()->username : Input::Get("username"); ?>
<input type="text" name="username" class="form-control" id="username" placeholder="<?php echo lang('USERNAME'); ?>" value="<?php echo $username; ?>" disabled="disabled"/>

<?php $email = Input::Get("email") == false ?  $user->data()->email : Input::Get("email"); ?>
<label for="email"><?php echo lang('EMAIL'); ?></label>
<input type="text" name="email" class="form-control" id="email" placeholder="<?php echo lang('EMAIL'); ?>" value="<?php echo $email; ?>" />

<?php $firstname = Input::Get("firstname") == false ?  $user->data()->firstname : Input::Get("firstname"); ?>
<label for="firstname"><?php echo lang('first name'); ?></label>
<input type="text" name="firstname" class="form-control" id="firstname" placeholder="<?php echo lang('first name'); ?>" value="<?php echo $firstname; ?>" />

<?php $lastname = Input::Get("lastname") == false ?  $user->data()->lastname : Input::Get("lastname"); ?>
<label for="lastname"><?php echo lang('last name'); ?></label>
<input type="text" name="lastname" class="form-control" id="lastname" placeholder="<?php l('last name'); ?>" value="<?php echo $lastname; ?>" />

<label for="fbuserid"><?php echo lang('FB_USER_ID');?></label>
<?php $fbuserid = Input::Get("fbuserid") == false ?  $user->data()->fbuserid : Input::Get("fbuserid"); ?>
<input type="text" name="fbuserid" class="form-control" id="fbuserid" placeholder="<?php l('FB_USER_ID');?>" value="<?php echo $fbuserid; ?>"/>

<label for="password"><?php echo lang('PASSWORD'); ?></label>
<input type="password" name="password" class="form-control" id="password" value="" placeholder="<?php echo lang('NEW_PASSWORD');?>" readonly onfocus="this.removeAttribute('readonly');"/>

<label for="repassword"><?php echo lang('RE_ENTER_PASSWORD'); ?></label>
<input type="password" name="repassword" class="form-control" id="repassword" value="" placeholder="<?php echo lang('RE_ENTER_NEW_PASSWORD'); ?>" readonly onfocus="this.removeAttribute('readonly');"/>


<div class="input-group">
	<label for="timezone">
		<?php echo lang('TIMEZONE'); ?> | <?php echo lang('CURRENT_TIME'); ?> : <?php echo date("Y-m-d H:i"); ?>
	</label>
	<select name='timezone' id="timezone" class="form-control">
		<?php

		if(isset($user->Options()->timezone))
			$selected = Input::Get('timezone') ? Input::Get('timezone') : $user->Options()->timezone;
		else
			$selected = Input::Get('timezone');

		foreach(DateTimeZone::listIdentifiers(DateTimeZone::ALL) as $timezone){
			$select = $selected == $timezone ? "selected" : "";
			echo "<option value='".$timezone."' ".$select.">".$timezone."</option>";
		}
		?>
	</select>
</div>


<div class="input-group">
	<label for="language"><?php echo lang('LANGUAGE'); ?></label>
	<select name='language' id="language" class="form-control">
		<?php
		$currentUserLang = isset($user->Options()->lang) ? $user->Options()->lang : DEFAULT_LANG;
		foreach(Language::GetAvailableLangs() as $language){
			$select = strtolower($currentUserLang) == strtolower($language) ? "selected" : "";
			echo "<option value='".$language."' ".$select.">".ucfirst($language)."</option>";
		}
		?>
	</select>
</div>