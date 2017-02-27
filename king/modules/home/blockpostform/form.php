<?php if (!defined('ABSPATH')) exit('No direct script access allowed');
// Elfinder connecter


// Load post if the post id has been passed
if(Input::Get("post_id","GET")){
	$posts = new Posts();
	$getPost = $posts->get(Input::Get("post_id"));
	if($getPost){
		
		$post = json_decode($getPost->content);

		$_POST['postTitle'] = escape($getPost->post_title);
		$_POST['postId'] = $getPost->id;
		
		$_POST['message'] = escape($post->message);
		$_POST['postType'] = "message";
		
		// Set Post type
		if(Posts::PostType($getPost->content) == "link"){
			$_POST['postType'] = "link";
			$_POST['link'] = escape($post->link);
			$_POST['picture'] = escape($post->picture);
			$_POST['name'] = escape($post->name);
			$_POST['caption'] = escape($post->caption);
			$_POST['description'] = escape($post->description);
		}

		// Set Post type
		if(Posts::PostType($getPost->content) == "image"){
			$_POST['postType'] = "image";
			$_POST['imageURL'] = escape($post->image);
		}

		// Set Post type
		if(Posts::PostType($getPost->content) == "video"){
			$_POST['postType'] = "video";
			$_POST['video'] = escape($post->video);
			$_POST['descriptionVideo'] = isset($post->descriptionVideo) ? escape($post->descriptionVideo) : "";
		}
	}
}else{
	$_POST['postType'] = "message";
}
?>
<!-- Save post dialog -->
<div id="postTitleModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?php echo lang("POST_TITLE"); ?></h4>
			</div>
			<div class="modal-body">
				<div class="messageBoxModal"></div>
				<div class="formField">
					<label for="postTitle"><?php echo lang("POST_TITLE"); ?></label>
					<input type="text" name='postTitle' id="postTitle" class="form-control" placeholder='<?php echo lang("POST_TITLE"); ?>.' value="<?php echo Input::Get("postTitle");?>" />
				</div>
			</div>
			<div class="modal-footer">
				<a class="btn btn-default" data-dismiss="modal"><?php echo lang("CLOSE"); ?></a>
				<a id="savePostModal" class="btn btn-primary"><?php echo lang("SAVE_POST"); ?></a>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<ul class="postType">

			<li>
			<a href="#" onclick="return false;" class="postTypeMessage <?php if(Input::Get("postType") == "message") echo "postTypeActive"; ?>"><span class="glyphicon glyphicon-align-left"></span> <?php echo lang("MESSAGE"); ?> </a>
			</li>

			<li>
			<a href="#" onclick="return false;"  class="postTypeLink <?php if(Input::Get("postType") == "link") echo "postTypeActive"; ?>">
			<span class="glyphicon glyphicon-link"></span> <?php echo lang("LINK");?> </a>
			</li>

			<li>
			<a href="#" onclick="return false;"  class="postTypeImage <?php if(Input::Get("postType") == "image") echo "postTypeActive"; ?>">
			<span class="glyphicon glyphicon-picture"></span> <?php echo lang("IMAGE");?> </a>
			</li>

			<li>
			<a href="#" onclick="return false;"  class="postTypeVideo <?php if(Input::Get("postType") == "video") echo "postTypeActive"; ?>">
			<span class="glyphicon glyphicon-facetime-video"></span> <?php echo lang("VIDEO");?> </a>
			</li>

		</ul>
	<div class="clear"></div>
</div>
<div class="panel-body">
	<input type="hidden" name="postType" id="postType" value="<?php echo Input::Get("postType"); ?>" />
	<input type="hidden" name="postId" id="postId" value="<?php echo Input::Get("postId");?>" />
	<input type="hidden" name="URLFrom" id="URLFrom" value="" />
	<div class="formField">
		<label for="message"><?php echo lang("MESSAGE"); ?> <a href="#"  onclick="return false;" data-toggle="tooltip" data-placement="top" style="float:right" title="<?php l("Spinning example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {foo|bar|foobar}!!"); ?>"><span class="glyphicon glyphicon-question-sign"></span></a></label>
		<textarea name='message' id="message" rows='3' cols='50' class="form-control" placeholder='<?php l("Your status here..."); ?>'><?php echo Input::Get("message");?></textarea>

		<div id="emoticons">
			<?php 
				$emoticonsList = array(
					'👍' => '_2e7',
					'👎' => '_2e8',
					'😝' => '_2g7',
					'😜' => '_2g6',
					'😃' => '_2fu',
					'😉' => '_2fx',
					'😂' => '_2ft',
					'😨' => '_2gf',
					'😭' => '_2gj',
					'😊' => '_2fy',
					'😓' => '_2g1',
					'😡' => '_2ga',
					'😠' => '_2g9',
					'👽' => '_2es',
					'😘' => '_2g4',
					'🌹' => '_2c9',
					'💋' => '_2ez',
					'💰' => '_4_q-',
					'💲' => '_4_q_',
					'💵' => '_4_r1',
					'❤' => '_2hc',
					'💔' => '_2f2',
					'💓' => '_2f1',
					'😍' => '_2f-',
					'🌟' => '_2c3',
					'✈' => '_4_u2',
					'⚡' => '_2h2',
					'🎵' => '_2cy',
					'⛔' => '_4_tb',
					'➡' => '_4_tk',
					'⬅' => '_4_tm',
					'⚽' => '_4_t9',
					'👀' => '_2dx',
					'☎' => '_4_t4',
					'✉' => '_4_u3',
					'🎁' => '_2cn',
				);

				foreach ($emoticonsList as $key => $value) {
					echo "<a href='#' title='".$key."' class='emoji ".$value." _1a-'></a>";
				}
			?>
		</div>

	</div>

	<div id="postLinkDetails" <?php if(Input::Get("postType") != "link") echo "style='display:none'"; ?>>
		<div class="formField">
			<label for="link"><?php echo lang("LINK");?>
				<a href="#"  onclick="return false;" data-toggle="tooltip" data-placement="top" style="float:right" title="If you specifie any field below This field is required">
				<span class="glyphicon glyphicon-question-sign"></span></a>
			</label>
			<input type='text' name='link' class="form-control" id="link" value="<?php echo Input::Get("link");?>" placeholder='<?php l("Post link here."); ?>' />
		</div>
		<div class="formField">
			<label for="picture"><?php echo lang("PICTURE"); ?></label>
			<div class="input-group">
				<input type='text' name='picture' id="picture" class="form-control"  value="<?php echo Input::Get("picture");?>" placeholder='<?php l("Post picture here."); ?>' />
				<div class="input-group-btn">
					<button type="button" id="mediaLibraryImageLink" class="btn btn-default"><?php l("Upload"); ?></button>
				</div>
			</div>
		</div>
		<div class="formField">
			<label for="name"><?php echo lang("NAME"); ?></label>
			<input type='text' id="name" name='name' class="form-control" value="<?php echo Input::Get("name");?>" placeholder='<?php l("Post name here."); ?>' >
		</div>
		<div class="formField">
			<label for="caption"><?php echo lang("CAPTION"); ?></label>
			<input type='text' name='caption' id="caption" class="form-control" value="<?php echo Input::Get("caption");?>" placeholder='<?php l("Post Caption here."); ?>' />
		</div>
		<div class="formField">
			<label for="description"><?php echo lang("DESCRIPTION"); ?></label>
			<textarea name='description' id="description" rows='3' cols='50' class="form-control" placeholder='<?php l("Post description here."); ?>'><?php echo Input::Get("description");?></textarea>
		</div>
	</div>

	<div id="postImageDetails" <?php if(Input::Get("postType") != "image") echo "style='display:none'"; ?>>
		<div class="formField">
			<label for="imageURL"><?php echo lang("IMAGE");?>
				<a href="#"  onclick="return false;" data-toggle="tooltip" data-placement="top" style="float:right" title="Image URL">
				<span class="glyphicon glyphicon-question-sign"></span></a>
			</label>
			<div class="input-group">
				<input type='text' name='imageURL' class="form-control" id="imageURL" value="<?php echo Input::Get("imageURL");?>" placeholder='<?php l("Image Link."); ?>' />
				<div class="input-group-btn">
					<button type="button" id="mediaLibraryImage" class="btn btn-default"><?php l("Upload"); ?></button>
				</div>
			</div>
		</div>
	</div>

	<div id="postVideoDetails" <?php if(Input::Get("postType") != "video") echo "style='display:none'"; ?>>
		<div class="formField">
				<label for="video"><?php echo lang("VIDEO");?>
				<a href="#"  onclick="return false;" data-toggle="tooltip" data-placement="top" style="float:right" title="Supported formats for uploaded videos: 3g2, 3gp, 3gpp, asf, avi, dat, divx, dv, f4v, flv, m2ts, m4v, mkv, mod, mov, mp4, mpe, mpeg, mpeg4, mpg, mts, nsv, ogm, ogv, qt, tod, ts, vob, wmv.">
					<span class="glyphicon glyphicon-question-sign"></span></a>
				</label>
				<div class="input-group">
					<input type='text' name='video' class="form-control" id="video" value="<?php echo Input::Get("video");?>" placeholder='<?php l("Video link (3gp, avi, mov, mp4, mpeg, mpeg4, vob, wmv...etc)."); ?>' />
					<div class="input-group-btn">
						<button type="button" id="mediaLibraryVideo" class="btn btn-default"><?php l("Upload"); ?></button>
					</div>
				</div>
		</div>
		<div class="formField">
			<label for="description"><?php echo lang("DESCRIPTION"); ?></label>
			<textarea name='descriptionVideo' id="video-description" rows='3' cols='50' class="form-control" placeholder='<?php l("Video Description"); ?>'><?php echo Input::Get("descriptionVideo");?></textarea>
		</div>
	</div>

	<div class="formField">
		<label for="defTime"><?php echo lang('POST_INTERVAL_SEC'); ?><a href="#"  onclick="return false;" data-toggle="tooltip" data-placement="top" style="float:right" title="The random interval is activated by default. the interval will be (Interval - Interval+30 seconds) Example: if you choose 60 Sec real interval will be 60 sec - 90 Sec" ><span class="glyphicon glyphicon-question-sign"></span></a></label>
		<select name='defTime' id="defTime" class="form-control">
			<?php 
				$selected = Input::Get('defTime');					
				if(isset($user->Options()->postInterval)){
					$selected = Input::Get('defTime') ? Input::Get('defTime') : $user->Options()->postInterval;
				}
				$minInterval = Options::get('min_interval') < 10 ? 10 : Options::get('min_interval');
				for($i = $minInterval; $i<=1500;$i += 30){
					if($i==$selected){
						echo "<option value='$i' selected>$i Sec</option>";
					}else{
						echo "<option value='$i'>$i Sec</option>";
					}
				}
			?>
		</select>
	</div>
	<br/>
	<div class="formField">
		<button onclick="return false;" class='btn btn-primary' id="post" name='post'>
			<span class="glyphicon glyphicon-send"></span> <?php echo lang("SEND_NOW"); ?> 
		</button>
		<button onclick="return false;" class='btn btn-primary' id="savepost" name='savepost'>
			<span class="glyphicon glyphicon-floppy-disk"></span> <?php echo lang("SAVE_POST"); ?>
		</button>
		<button onclick="return false;" class='btn btn-primary' id="scheduledpost">
			<span class="glyphicon glyphicon-time"></span> <?php echo lang("SCHEDULED_POSTS"); ?> 
		</button>
	</div>
	<div class="row scheduledpost" style="display:none">
		<div class="col-lg-12">
			<strong><?php echo lang('POST_INTERVAL'); ?></strong>
			<a href="#" data-toggle="tooltip" data-placement="top" style="float:right" title="Minimum post interval is 5 Minutes" onclick="return false;"><span class="glyphicon glyphicon-question-sign"></span></a>
			<div class="row">
				<div class="col-lg-3">
					<div class="input-group">
						<span class="input-group-addon">
							<input type="radio" name="timeType" id="intervalMunite" value="minute" checked />
						</span>
						<span class="form-control"><?php echo lang('MINUTES'); ?></span>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="input-group">
						<span class="input-group-addon">
							<input type="radio" name="timeType" id="intervalHour" value="hour" />
						</span>
						<span class="form-control"><?php echo lang('HOURS'); ?></span>
					</div>
				</div>
				<div class="col-lg-6">
					<select name='scheduledPostInterval' id="scheduledPostInterval" class="form-control">
						<?php 
						for($i = 1;$i<=90;$i++){
							if($i == 5) echo "<option value='$i' selected>$i</option>";
							else echo "<option value='$i'>$i</option>";
						}
						?>
					</select>
					
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6">
					<label for="scheduledPostTime">
						<?php echo lang('SCHEDULE_POST_START'); ?>
					</label>
					<div class="form-group">
						<div class='input-group date'>
							<input type='text' class="form-control" id='scheduledPostTime' />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
						<small style="color:red;margin:5px">
							<?php 
								$currentServerTime = new DateTime();
								echo "Current server time : ".$currentServerTime->format('Y-m-d H:i'); 
							?>
						</small>
					</div>
				</div>
				<div class="col-lg-6">
					<label for="scheduledPostApp"><?php echo lang('FB_APP'); ?></label>
					<select name='scheduledPostApp' id="scheduledPostApp" class="form-control">
						<?php
							if($fb->AppsList()){
								$selected = Input::Get('scheduledPostApp') ? Input::Get('scheduledPostApp') : $fbaccount->UserFbAccountDefaultApp();
								foreach($fb->AppsList() as $app){
									$select = $selected == $app->appid ? "selected" : "";
									if($fb->GetAccessToken($app->appid)){
										echo "<option value='".$app->appid."' ".$select.">".$app->app_name."</option>";
									}
								}
							}
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6">
					<label for="schedulePauseAfter"><?php l('Auto pause after (Posts)'); ?></label>
					<select name='schedulePauseAfter' id="schedulePauseAfter" class="form-control">
						<?php 
						for($i = 0;$i<=100;$i += 5){
							echo "<option value='$i'>$i</option>";
						}
						?>
					</select>
				</div>
				<div class="col-lg-6">
					<label for="scheduleResumeAfter"><?php l('Auto resume after (hours)'); ?></label>
					<select name='scheduleResumeAfter' id="scheduleResumeAfter" class="form-control">
						<?php 
						for($i = 1;$i<=100;$i++){
							echo "<option value='$i'>$i</option>";
						}
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<button onclick="return false;" class='btn btn-primary' id="saveScheduledPost" name='scheduledpost'>
						<span class="glyphicon glyphicon-time"></span> <?php echo lang("SAVE_SCHEDULED_POSTS"); ?> 
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="messageBox"></div>
</div>
</div>
<?php include __DIR__. "/elfinder/view.php";?>