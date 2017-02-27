<div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title"><?php echo lang("POST_PREVIEW"); ?></h3>
    </div>
    <div class="panel-body">
		<div class="postPreview">
		<div class="post">
			<div class="PreviewPoster">
				<img src='https://graph.facebook.com/<?php echo $fbaccountDetails->getFbid(); ?>/picture?redirect=1&height=40&width=40&type=normal' style='vertical-align:top;'  onerror="this.src = 'theme/default/images/facebookUser.jpg'" />
				<span class="userFullName">
					<?php
					if($fbaccountDetails->getLastname() || $fbaccountDetails->getFirstname()) 
						echo $fbaccountDetails->getLastname() . " " . $fbaccountDetails->getFirstname();
					else
						echo "Facebook User";
					?>
				</span>
				<span class="postPreviewDetails">
					<?php echo lang("NOW"); ?>· 
					<?php
						if($fbaccount->UserFbAccountDefaultApp() && $defaultApp = Facebook::App($fbaccount->UserFbAccountDefaultApp())){
							echo $defaultApp->app_name;
						}else{
							echo lang("APP_NAME"); 
						}						
					?>
				</span>
				<div class="clear"></div>
			</div>
			<p class="message"><span class="defaultMessage"></span></p>
			
			<a href="#" class="previewPostLink">
				<div class="previewLink"></div>
				<div class="postDetails">
					<p class="name">
						<span class="defaultName"></span>
					</p>
					<p class="description">
						<span class="defaultDescription"></span>
						<span class="defaultDescription"></span>
						<span class="defaultDescription"></span>
						<span class="defaultDescription"></span>
						<span class="defaultDescription"></span>
					</p>
					<p class="caption"><span class="defaultCaption"></span></p>
				</div>
			</a>
		</div>
	</div>
	</div>
  </div>