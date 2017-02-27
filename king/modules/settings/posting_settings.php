<h4 class="tab-title"><i class="fa fa-clipboard"></i> <?php echo lang('POSTING_SETTINGS'); ?></h4>
<div class="input-group">
	<?php 
		if(Input::Get("openGroupOnly")){
			$openGroupOnlyChecked = Input::Get("openGroupOnly") == "on" ? "checked" : "";
		} else if(isset($user->Options()->openGroupOnly)){
			$openGroupOnlyChecked =  $user->Options()->openGroupOnly ? "checked" : "";
		}else{
			$openGroupOnlyChecked = "";
		}
		?>
	<input type="checkbox" class="checkbox-style" id="openGroupOnly" name="openGroupOnly" aria-label="Post to open group only" <?php echo $openGroupOnlyChecked;?> />
	<label for="openGroupOnly"></label>
	<span class="input-text"><?php echo lang('SHOW_OPEN_GROUPS_ONLY'); ?></span>
</div>
<div class="input-group">
	<?php 
		if(Input::Get("uniquePost")){
			$uniquePost = Input::Get("uniquePost") == "on" ? "checked" : "";
		} else if(isset($user->Options()->uniquePost)){
			$uniquePost =  $user->Options()->uniquePost ? "checked" : "";
		}else{
			$uniquePost = "";
		}
		?>
	<input type="checkbox" class="checkbox-style" id="uniquePost" name="uniquePost" aria-label="Unique post" <?php echo $uniquePost;?> />
	<label for="uniquePost"></label>

	<span class="input-text"><?php echo lang('UNIQUE_POST'); ?> <a href="#"  onclick="return false;" data-toggle="tooltip" data-placement="top" title="<?php echo lang('UNIQUE_POST_TEXT'); ?>"><span class="glyphicon glyphicon-question-sign"></span></a> </span>
</div>
<div class="input-group">
	<?php 
		if(Input::Get("uniqueLink")){
			$uniqueLink = Input::Get("uniqueLink") == "on" ? "checked" : "";
		} else if(isset($user->Options()->uniqueLink)){
			$uniqueLink =  $user->Options()->uniqueLink ? "checked" : "";
		}else{
			$uniqueLink = "";
		}
		?>
	<input type="checkbox" class="checkbox-style" id="uniqueLink" name="uniqueLink" aria-label="Unique post" <?php echo $uniqueLink;?> />
	<label for="uniqueLink"></label>
	<span class="input-text"><?php echo lang('UNIQUE_LINK'); ?> <a href="#"  onclick="return false;" data-toggle="tooltip" data-placement="top" title="<?php echo lang('UNIQUE_LINK_TEXT'); ?>"><span class="glyphicon glyphicon-question-sign"></span></a></span>
</div>
<label for="postInterval"><?php echo lang('POST_INTERVAL'); ?> (<small><?php echo lang('IN_SECONDS'); ?></small>)</label>
<select name='postInterval' id="postInterval"  class="form-control">
	<?php 
	$minInterval = Options::get('min_interval') < 10 ? 10 : Options::get('min_interval');
	for($i = $minInterval;$i<=1500;$i += 30){
		$selected = Input::Get('postInterval');
		
		if(isset($user->Options()->postInterval)){
			$selected = Input::Get('postInterval') ? Input::Get('postInterval') : $user->Options()->postInterval;
		}

		if ($i==$selected) {
			echo "<option value='$i' selected>$i Sec</option>";
		} else {
			echo "<option value='$i'>$i Sec</option>";
		}
	}
	?>
</select>
<label for="postApp"><?php echo lang('FB_APP'); ?></label>
<select name='postApp' id="postApp" class="form-control">
	<option value=""></option>
	<?php
		if($fb->AppsList()){
				$selected = Input::Get('postApp') ? Input::Get('postApp') : $fbaccount->UserFbAccountDefaultApp();
				foreach($fb->AppsList() as $app){
					$select = $selected == $app->appid ? "selected" : "";
					if($fb->getAccessToken($app->appid)){
						echo "<option value='".$app->appid."' ".$select.">".$app->app_name."</option>";
					}
				}
		}
	?>
</select>