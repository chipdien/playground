<?php if (!defined('ABSPATH')) exit('No direct script access allowed'); 

$user = new User();
$fbaccount = new fbaccount();
$fbaccountDetails = $fbaccount->get($fbaccount->UserDefaultFbAccount());

?>
<html dir="<?php echo lang("DIR"); ?>">
<head>
	<title>{{title}} | <?php echo Options::get("sitename"); ?></title>
	<meta charset="UTF-8" />
	<meta name="description" content="">
	<meta name="author" content="Abdellah Gounane - Gounane.com">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
 	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="{{templateFolder}}/images/favicon.png" >

	<!-- CSS Files -->
	<link href="{{templateFolder}}/css/custom.css" rel="stylesheet" />
	
	<link href="{{templateFolder}}/css/fb.emoji.css" rel="stylesheet" />
	<link href="{{templateFolder}}/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="{{templateFolder}}/css/datatables.bootstrap.min.css" rel="stylesheet">
	<link href="{{templateFolder}}/css/font-awesome.css" rel="stylesheet">
	<link href="{{templateFolder}}/css/bootstrap-datetimepicker.min.css" rel="stylesheet">

	<?php if(lang("DIR") == "rtl"){ ?>
		<link href="{{templateFolder}}/css/rtl.css" rel="stylesheet" />
	<?php } ?>

	<!-- JS Files -->
	<script src="{{templateFolder}}/js/jquery.js"></script>
	<script src="core/js/lang.js"></script>
	<script src="core/js/javascript.js"></script>
	<script src="{{templateFolder}}/js/jsui.js"></script>
	<script src="{{templateFolder}}/js/postpreview.js"></script>
	<script src="{{templateFolder}}/bootstrap/js/bootstrap.min.js"></script>
	<script src="{{templateFolder}}/js/jquery.dataTables.min.js"></script>
	<script src="{{templateFolder}}/js/dataTables.bootstrap.min.js"></script>
	<script src="{{templateFolder}}/js/moment.min.js"></script>
	<script src="{{templateFolder}}/js/bootstrap-datetimepicker.min.js" rel="stylesheet"></script>


	<script>
	$(document).ready(function(){
			$('[data-toggle="tooltip"]').tooltip();
			$('#scheduledPostTime').datetimepicker();
			var translations = {
		            "lengthMenu": "<?php echo sprintf(lang('Display %s records per page'),'_MENU_'); ?>",
		            "zeroRecords": "<?php l('No records available'); ?>",
					"info": "<?php echo sprintf(lang('Showing %s to %s of %s'),"_START_","_END_","_TOTAL_"); ?>",
		            "infoEmpty": "<?php l('No records available'); ?>",
		            "infoFiltered": "<?php echo sprintf(lang('(filtered from %s total records)'),'_MAX_'); ?>",
		            "search":  "<?php l('Search'); ?>:",
		            "paginate": {
				        "first": "<?php l('First'); ?>",
				        "last": "<?php l('Last'); ?>",
				        "next":  "<?php l('Next'); ?>",
				        "previous":   "<?php l('Previous'); ?>",
				    }
			};

			var oDataTable = $('#datatable').DataTable({
				"aaSorting": [],
				"responsive": true,
		        "aoColumnDefs": [{
		            'bSortable': false,
		            'aTargets': [0]
		        }],
		        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				"iDisplayLength": 50,
				"language": translations,
		    });

		    var groupsDataTable = $('#groupsDatabale').DataTable({
				"aaSorting": [],
				"bSort": true,
				"responsive": true,
		        "aoColumnDefs": [{
		            'bSortable': false,
		            'aTargets': [0]
		        }],
		        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				"iDisplayLength": 100,
				"language": translations,
		    });
	});
	</script>
</head>
<body>
<noscript>
	<div class="alerts alert alert-danger">
		<span class="glyphicon glyphicon-warning-sign"></span>
		<p class='alerttext'>JavaScript MUST be enabled in order for you to use kingposter. However, it seems JavaScript is either disabled or not supported by your browser. If your browser supports JavaScript, Please enable JavaScript by changing your browser options, then try again.</p>
	</div>
</noscript>
<div class='alerts'></div>
<nav class="navbar navbar-inverse" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
		<div class="logo"><a href="index.php" title="Home"><img src="{{templateFolder}}/images/logo.png" alt="logo"></a></div>
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		  <span class="sr-only">Toggle navigation</span>
		  <span class="icon-bar"></span>
		  <span class="icon-bar"></span>
		  <span class="icon-bar"></span>
		</button>
    </div>
    <div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">

				<li><a href='<?php base_url('settings.php'); ?>'><span class="glyphicon glyphicon-cog"></span> <?php echo lang("SETTINGS"); ?> </a></li>
				
				<li class="dropdown">
					<a href='#' class="dropdown-toggle" data-toggle="dropdown" >
						<span class="glyphicon glyphicon-folder-open"></span>&nbsp;
						<?php echo lang("POSTS"); ?> 
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li>
							<a href="<?php base_url('posts.php'); ?>">
							<span class="glyphicon glyphicon-duplicate"></span> 
							<?php echo lang("SAVED_POSTS"); ?>
						</a>
						</li>
						<li>
							<a href="<?php base_url('scheduledposts.php'); ?>">
							<span class="glyphicon glyphicon-time"></span> 
							<?php echo lang("SCHEDULED_POSTS"); ?> 
							</a>
						</li>
						<li role="separator" class="divider"></li>
						<li>
							<a href="<?php base_url('logs.php'); ?>">
							<span class="glyphicon glyphicon-alert"></span> 
							<?php echo lang("LOGS"); ?> 
							</a>
						</li>
					</ul>
				</li>
				
				<?php if($user->HasPermission("admin")){ ?>
						<li><a href='<?php base_url('users.php'); ?>'><span class="glyphicon glyphicon-user"></span> <?php echo lang("USERS"); ?> </a></li>
				<?php } ?>

				<li class="dropdown">
					<a href='#' class="dropdown-toggle" data-toggle="dropdown" >
						<i class="fa fa-facebook"></i>&nbsp;
						&nbsp;<?php l('Switch fb account'); ?>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<?php 
							if(count($fbaccount->getAll())){
								foreach($fbaccount->getAll() as $fba){
		  							echo "<li><a href='";
		  								base_url('settings.php?switchFbAccount='.$fba->getFbId());
		  							echo "'>
		  								<img src='https://graph.facebook.com/".$fba->getFbId()."/picture?redirect=1&height=40&width=40&type=normal' style='vertical-align:middle;' width='32px' height='32px' onerror=\"this.src = 'theme/default/images/facebookUser.jpg'\"/>
		  							".$fba->getFirstname()." ".$fba->getLastname()."</a></li>";
								}

						 	}else{
						 		echo "<li><a href='#'>No facebook account available</a></li>";
						 	}

						?>
					</ul>
				</li>

				<?php if(defined('UPDATE')) { ?>
					<li><a href='http://goo.gl/RrrjcV' target="_blank"><span class="glyphicon glyphicon-ok-circle"></span> New update available!</a></li>
				<?php } ?>

			</ul>
			
			<ul class="nav navbar-nav navbar-right">
		        <li class="dropdown">
		          <a href="#" class="dropdown-toggle UserProfil" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								<img src='https://graph.facebook.com/<?php echo $fbaccount->UserDefaultFbAccount(); ?>/picture?redirect=1&height=40&width=40&type=normal' width='32' height='32' style='vertical-align:middle;'  onerror="this.src = 'theme/default/images/facebookUser.jpg'"/>
								<span class="userFullName"><?php echo ucfirst($fbaccountDetails->getLastname())." ".ucfirst($fbaccountDetails->getFirstname()); ?></span>
							</a>
		          <ul class="dropdown-menu">
		            <li>
	            		<a href='<?php base_url('settings.php'); ?>'>
	            			<span class="glyphicon glyphicon-cog"></span>
	            			<?php echo lang("SETTINGS"); ?> 
	            		</a>
	            	</li>
					<li>
						<a href='#' onclick="window.open('<?php base_url('resetaccesstoken.php'); ?>','','height=570,width=600');">
							<span class='glyphicon glyphicon-repeat'></span> 
							<?php echo lang("RESET_ACCESS_TOKEN"); ?>
						</a>
					</li>
					<li role="separator" class="divider"></li>
					<li>
						<a href='<?php base_url('logout.php'); ?>'>
							<span class="glyphicon glyphicon-log-out"></span> 
							<?php echo lang("LOGOUT"); ?> 
						</a>
					</li>
		          </ul>
		        </li>
      </ul>
    </div>
  </div>
</nav>
<div id="wrapper">