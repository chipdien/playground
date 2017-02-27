<?php  
include('core/init.php');


$ScheduledPost = new ScheduledPosts();
$posts = new Posts();
$fb = new Facebook();
$fb_account = new FbAccount();
$template = new Template();

if(Input::get("action","GET") == "delete" && Input::Get("id","GET")){
	try{
		$ScheduledPost->delete(Input::Get("id","GET"));
		Session::Flash("scheduledPosts","success",lang("The scheduled post(s)s has been deleted successfully"),true);
	}catch(Exception $ex){
		Session::Flash("scheduledPosts","<script>alertBox('".$ex->GetMessage()."','danger','.messageBox');</script>");
	}
	
	Redirect::To("scheduledposts.php");
}

if(Input::get("action","GET") == "repeat" && Input::Get("id","GET")){
	try{
		DB::GetInstance()->Update("scheduledposts","id",Input::Get("id","GET"),array(
			"next_target" => '0',
			"status" => '0',
		));
	}catch(Exception $ex){
		Session::Flash("scheduledPosts","danger",$ex->GetMessage(),true);
	}
	Redirect::To("scheduledposts.php");
}


if(Input::get("action","GET") == "pause" && Input::Get("id","GET")){
	$stat = Input::Get("stat","GET") == "" ? "0" : Input::Get("stat","GET");
	try{
		DB::GetInstance()->Update("scheduledposts","id",Input::Get("id","GET"),array("pause" => $stat));
	}catch(Exception $ex){
		Session::Flash("scheduledPosts","danger",$ex->GetMessage(),true);
	}
	Redirect::To("scheduledposts.php");
}

$template->header("Scheduled posts");

if(Session::exists('scheduledPosts')){
	foreach(Session::Flash('scheduledPosts') as $error){
		echo "<div class='alert alert-".$error['type']."' role='alert'>";
		echo "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>";
		echo "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>";
		echo "&nbsp;".$error['message'];
		echo "</div>";
	}
}
					
?>
<div class="messageBox"></div>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><span class="glyphicon glyphicon-time"></span> <?php echo lang("SCHEDULED_POSTS"); ?> </h3>
	</div>
	<div class="panel-body">
		<table class="table table-bordered table-striped dataTable" id="datatable" width="100%">
			<thead>
				<tr>
					<td>
						<input type='checkbox' id="checkbox-all" class="check-all checkbox-style" name='a' />
						<label for="checkbox-all"></label>
					</td>
					<td><?php echo lang("NEXT_POSTING_TIME"); ?></td>
					<td><?php echo lang("POST_INTERVAL"); ?></td>
					<td><?php echo lang("POST"); ?></td>
					<td><?php echo lang("FB_APP"); ?></td>
					<td><?php echo lang("FB_ACCOUNT"); ?></td>
					<td><?php echo lang("PAUSE_RESUME"); ?></td>
					<td><?php echo lang('STATUS'); ?></td>
					<td></td>
				</tr>
			</thead>
			<?php 
				try{
					$ScheduledPosts = $ScheduledPost->userPosts();
				}catch(Exception $e){
					echo "Error : ".$e->GetMessage();
				}
				
				if($ScheduledPosts){
					foreach($ScheduledPosts as $ScheduledPost){
						
						$fba = $fb_account->get($ScheduledPost->fb_account);

						$nextRepeat = "Stoped";
						if($ScheduledPost->repeat_every != 0){
							$nextRepeat = new DateTime($ScheduledPost->repeated_at);
							$nextRepeat->modify('+'.$ScheduledPost->repeat_every.' day');
							$nextRepeat = $nextRepeat->format("Y-m-d H:i");
						}
						
						$postTitle = "<span style='color:red'>".lang('Not found!')."</span>";
						$app_name = "<span style='color:red'>".lang('Not found!')."</span>";
						$fb_display = "<span style='color:red'>".lang('Not found!')."</span>";
						
						if($posts->get($ScheduledPost->post_id)){
							$postTitle = $posts->get($ScheduledPost->post_id)->post_title;
						}
						
						if(isset(Facebook::App($ScheduledPost->post_app)->app_name)){
							$app_name = Facebook::App($ScheduledPost->post_app)->app_name;
						}

						if($fba->getFbId()){
							$fb_display = "<img src='https://graph.facebook.com/".$fba->getFbId()."/picture?redirect=1&height=40&width=40&type=normal' width='32' height='32' style='vertical-align:middle;' />
								".$fba->getFirstname()." ".$fba->getLastname();
						}
						
						$totalGroups = count(json_decode($ScheduledPost->targets,true));
						$ap = json_decode($ScheduledPost->auto_pause);
						$autoPause = "";
						if(isset($ap->pause) && $ap->pause != null && $ap->pause != 0){
							$autoPause = lang("Auto pause after") . " : " . $ap->pause . " posts " . lang("Resume after") . " : ".$ap->resume . " " . lang("Hours");
						}
						
						$status = $ScheduledPost->status == "1" ? "<span class='btn btn-success'>".lang('COMPLETED')." (".$totalGroups."/".$totalGroups.")</span>" : "<span class='btn btn-default'>Progress ".$ScheduledPost->next_target ."/".$totalGroups."</span>";
						$pause = $ScheduledPost->pause == "0" ? lang('PAUSE') : lang('RESUME');
						$stat = $ScheduledPost->pause == "0" ? "1" : "0";
						$pauseBtn = $ScheduledPost->pause == "0" ? "primary" : "warning";
						$pauseBtnIcon = $ScheduledPost->pause == "0" ? "pause" : "play";
						$fba = $fb_account->get($ScheduledPost->fb_account);
						echo "<tr>
						<td>
							<input type='checkbox' class='checkbox checkbox-style' name='' id='' value='' />
							<label for=''></label>
						</td>
						<td>".$ScheduledPost->next_post_time."</td>
						<td>".$ScheduledPost->post_interval." Min</td>
						<td>".$postTitle."</td>
						<td>".$app_name."</td>
						<td>".$fb_display."</td>
						<td>
							<a href='scheduledposts.php?action=pause&stat=".$stat."&id=".$ScheduledPost->id."' class='btn btn-".$pauseBtn."'><span class='glyphicon glyphicon-".$pauseBtnIcon."'></span> ".$pause."
							</a>";
						if($autoPause){
							echo " <a href='#'  onclick='return false;'' data-toggle='tooltip' data-placement='top' title='".$autoPause."'><span class='glyphicon glyphicon-question-sign'></span></a>";
						}
						echo "
						</td>
						<td>".$status."</td>
						<td>
							<a href='scheduledposts.php?action=delete&id=".$ScheduledPost->id."' title='".lang('DELETE')."' class='btn btn-danger delete' id='".$ScheduledPost->id."' onclick='return confirm(\"".lang('SCHEDULE_DELETE_CONFIRM')."\");'><span class='glyphicon glyphicon-trash'></span></a>
							<a href='logs.php?scheduleid=".$ScheduledPost->id."' title='".lang('VIEW_LOG')."' class='btn btn-primary'><span class='glyphicon glyphicon-folder-open'></span></a>

							<button title='".lang('REPOST')."     (".lang('Next repeat on')." : ".$nextRepeat.")' data-toggle='tooltip' data-placement='top' class='btn btn-primary repeatSchedule' data-repeat_every='".$ScheduledPost->repeat_every."' value='".$ScheduledPost->id."' ><span class='glyphicon glyphicon-repeat'></span></button>

						</td>
						</tr>"
						;
					}
				}
				
			?>
		</table>
	</div>
</div>

<!-- Repeat dialog -->
<div id="repeatModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?php l("Repeat schedule"); ?></h4>
			</div>
			<div class="modal-body">
				<div class="messageBoxRepeatScheduleModal"></div>
				<form method="post">
					<input name="schedule_id" type="hidden" id="schedule_id" value="0">
					<div class="formField">
						<label for="repeatEvery">Repeat Interval</label>
						<select name="repeatEvery" id="repeatEvery" class="form-control">
							<option value='-1'><?php l('Once'); ?></option>";
							<option value='1'><?php l("Every day"); ?></option>";
							<?php for ($i=2; $i <= 30 ; $i++) { 
								echo "<option value='".$i."'>".sprintf(lang("Every %s days"),$i)."</option>";
							} ?>
						</select>
					</div>
					<div class="formField row">
						<div class="col-sm-6">
							<label for="repeatAt">Repeat at:</label>
							<div class='input-group date'>
								<input type='text' class="form-control" id='repeatAt' />
								<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
						<div class="col-sm-6">
							<label for="repeatAt">Start at:</label>
							<div class='input-group date'>
								<input type='text' class="form-control" id='startAt' />
								<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
						<div class="col-xs-12">
							<small style="color:red;margin:5px">
								<?php 
									$currentServerTime = new DateTime();
									echo "Current server time : ".$currentServerTime->format('Y-m-d H:i'); 
								?>
							</small>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<a class="btn btn-default" data-dismiss="modal"><?php l("CLOSE"); ?></a>
				<a id="repeatScheduleSaveBtn" class="btn btn-primary"><?php l("SAVE"); ?></a>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready(function() {
		jQuery('#repeatAt').datetimepicker({format: 'MM/DD/YYYY'});

		jQuery('#startAt').datetimepicker({
		  format:'HH:mm',
		});

	});

	$(".repeatSchedule").click(function(){
		$('#schedule_id').val( $( this ).val() );
		$("#repeatEvery").val($( this ).data("repeat_every"));
		$('#repeatModal').modal('show');
	});

	$("#repeatScheduleSaveBtn").click(function(){
  		$(".messageBoxRepeatScheduleModal").html("<br/><img src='theme/default/images/loading.gif' alt='loading' class='preloader' />");
       $.ajax({
        url: 'ajax/edit_schedule.php',
        dataType: 'json',
        type: 'post',
        contentType: 'application/x-www-form-urlencoded',
        data: { 
        	schedule_id: $('#schedule_id').val(),
        	repeat_every: $('#repeatEvery').val(),
        	repeat_at: $('#repeatAt').val(),
        	start_at: $('#startAt').val(),
        	action: "post",
        },
        success: function( data, textStatus, jQxhr ){
            if(data.status == "success"){
            	
            	if(data.message !== null && typeof data.message === 'object'){
					htmlData = "<ul>";
					Object.keys(data.message).forEach(function(key) {
					    htmlData += "<li>" + data.message[key] + "</li>";
					});
					htmlData += "</ul>";
            	}else{
            		htmlData = data.message;
            	}
            	alertBox(htmlData,"success",".messageBoxRepeatScheduleModal",true,true);
            	$(document).on('hide.bs.modal','#repeatModal', function () {location.reload();});
            }else{
            	
            	if(data.message !== null && typeof data.message === 'object'){
            		htmlData = "<ul>";
					Object.keys(data.message).forEach(function(key) {
					    htmlData += "<li>" + data.message[key] + "</li>";
					});
					htmlData += "</ul>";
            	}else{
            		htmlData = data.message;
            	}
            	alertBox(htmlData,"danger",".messageBoxRepeatScheduleModal",true,true);
            }
        },
        error: function( jqXhr, textStatus, errorThrown ){ console.log(errorThrown); }
      });

	});
</script>
<?php $template->footer(); ?>
