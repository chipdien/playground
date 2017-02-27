<div class="panel panel-default">
  <div class="panel-body">
    <div class="postingDetails">
		<?php echo lang('Groups'); ?> : <strong><?php echo $fbaccount->GroupsCount(); ?></strong> | 
		<?php echo lang('Pages'); ?> : <strong><?php echo $fbaccount->PagesCount(); ?></strong> | 
		<?php echo lang('ELAPSED'); ?> : <strong><span class="totalPostTime">-</span></strong> | 
		<?php echo lang('TIME_LEFT'); ?> : <strong><span class="leftTime">-</span></strong>
	</div>
	<div class="controls">
	
		<button id="pauseButton" class="btn btn-primary" onclick="postPause()" disabled>
			<span class="glyphicon glyphicon-pause"></span><?php echo lang('PAUSE'); ?> 
		</button>
		
		<button id="resumeButton" class="btn btn-primary"  onclick="postResume()" disabled>
			<span class="glyphicon glyphicon-play"></span><?php echo lang('RESUME'); ?>  
		</button>
		
	</div>
  </div>
</div>