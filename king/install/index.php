<?php 
	session_start();
	ob_start();
	require_once "../core/autoload.php";
	require_once "../core/requirements.php";
	require_once "../core/language/language.php";
	if(!Session::Exists("setup")){
		Redirect::To("../index.php");
		die();
	}

	if(Session::Get("db_exists") == false){
		if(Input::Get('verify')){
			$curl = new Curl();
			$curl->Get("http://example.com/manager/verify/?purchaseCode=".Input::Get('purchaseCode')."&v=".DEV_VERSION."&driver=".Config::Get('db/driver')."&domain=".substr(CurrentPath(), 0, strrpos(CurrentPath(), 'install/')));
			if ($curl->rawResponse != null){
				eval(base64_decode("REI6OkNyZWF0ZURiRmlsZSgpOw0KDQpEQjo6R2V0SW5zdGFuY2UoKS0+UXVlcnkoIg0KQ1JFQVRFIFRB​QkxFIElGIE5PVCBFWElTVFMgdXNlcnMgKA0KCWlkIElOVEVHRVIgUFJJTUFSWSBLRVkgQVVUT0lOQ1JF​TUVOVCwNCgl1c2VybmFtZSB2YXJjaGFyKDMyKSwNCglwYXNzd29yZCB2YXJjaGFyKDY0KSwNCglzYWx0​IHZhcmNoYXIoMzIpLA0KCWZpcnN0bmFtZSB2YXJjaGFyKDMyKSwNCglsYXN0bmFtZSB2YXJjaGFyKDMy​KSwNCgllbWFpbCB2YXJjaGFyKDY0KSwNCglmYnVzZXJpZCB2YXJjaGFyKDMyKSwNCglyb2xlcyBJTlRF​R0VSLA0KCWdlbmRlciB2YXJjaGFyKDgpLA0KCWFjdF9jb2RlIHZhcmNoYXIoMzIpLA0KCWFjdGl2ZSBJ​TlRFR0VSLA0KCXNpZ251cCBkYXRldGltZSwNCglyZXNldF9wd19jb2RlIHRleHQsDQoJRk9SRUlHTiBL​RVkocm9sZXMpIFJFRkVSRU5DRVMgcm9sZXMoaWQpDQopOyIpOw0KDQpEQjo6R2V0SW5zdGFuY2UoKS0+​UXVlcnkoIg0KQ1JFQVRFIFRBQkxFIElGIE5PVCBFWElTVFMgdXNlcnNfc2Vzc2lvbiAoDQoJaWQgSU5U​RUdFUiBQUklNQVJZIEtFWSBBVVRPSU5DUkVNRU5ULA0KCXVzZXJfaWQgSU5URUdFUiwNCgloYXNoIHZh​cmNoYXIoNjQpLA0KCUZPUkVJR04gS0VZKHVzZXJfaWQpIFJFRkVSRU5DRVMgdXNlcihpZCkNCik7Iik7​DQoNCkRCOjpHZXRJbnN0YW5jZSgpLT5RdWVyeSgiDQpDUkVBVEUgVEFCTEUgSUYgTk9UIEVYSVNUUyB1​c2VyX29wdGlvbnMgKA0KCWlkIElOVEVHRVIgUFJJTUFSWSBLRVkgQVVUT0lOQ1JFTUVOVCwNCgl1c2Vy​aWQgSU5URUdFUiwNCglwb3N0SW50ZXJ2YWwgSU5URUdFUiwNCglsYW5nIHZhcmNoYXIoMzIpLA0KCW9w​ZW5Hcm91cE9ubHkgSU5URUdFUiwNCgl1bmlxdWVQb3N0IElOVEVHRVIsDQoJdW5pcXVlTGluayBJTlRF​R0VSLA0KCWRlZmF1bHRfRmJfQWNjb3VudCAgdmFyY2hhcigzMiksDQoJdGltZXpvbmUgdmFyY2hhcig2​NCksDQoJbGltaXRJbXBvcnRHcm91cHMgaW50ZWdlciwNCglsaW1pdEltcG9ydFBhZ2VzIGludGVnZXIs​DQoJc2hvd19ncm91cHMgaW50ZWdlciwNCglzaG93X3BhZ2VzIGludGVnZXIsDQoJdG9kYXlfbnVtX3Bv​c3RzIGludGVnZXIsDQoJbGFzdF9udW1fcG9zdHNfcmVzZXQgZGF0ZXRpbWUsDQoJbG9hZF9ncm91cHMg​aW50ZWdlciwNCglsb2FkX3BhZ2VzIGludGVnZXIsDQoJbG9hZF9vd25fcGFnZXMgaW50ZWdlciwNCglG​T1JFSUdOIEtFWSh1c2VyaWQpIFJFRkVSRU5DRVMgdXNlcihpZCkNCik7DQoiKTsNCg0KREI6OkdldElu​c3RhbmNlKCktPlF1ZXJ5KCINCkNSRUFURSBUQUJMRSBJRiBOT1QgRVhJU1RTIHVzZXJfZmJhcHAgKA0K​CXVzZXJpZCBJTlRFR0VSLA0KCWFwcGlkIHZhcmNoYXIoMjU1KSwNCglmYl9pZCB2YXJjaGFyKDI1NSks​DQoJYWNjZXNzX3Rva2VuIHRleHQsDQoJYWNjZXNzX3Rva2VuX2RhdGUgdGV4dCwNCglGT1JFSUdOIEtF​WSh1c2VyaWQpIFJFRkVSRU5DRVMgdXNlcihpZCksDQoJRk9SRUlHTiBLRVkoYXBwaWQpIFJFRkVSRU5D​RVMgZmJhcHBzKGlkKSwNCglGT1JFSUdOIEtFWShmYl9pZCkgUkVGRVJFTkNFUyBmYl9hY2NvdW50cyhm​Yl9pZCksDQoJQ09OU1RSQUlOVCB1c2VyX2ZiYXBwX3BrIFBSSU1BUlkgS0VZICh1c2VyaWQsIGFwcGlk​LCBmYl9pZCkNCik7DQoiKTsNCg0KREI6OkdldEluc3RhbmNlKCktPlF1ZXJ5KCINCkNSRUFURSBUQUJM​RSBJRiBOT1QgRVhJU1RTIHNjaGVkdWxlZHBvc3RzICgNCglpZCBJTlRFR0VSIFBSSU1BUlkgS0VZIEFV​VE9JTkNSRU1FTlQsDQoJdXNlcmlkIGludCwNCgluZXh0X3Bvc3RfdGltZSBkYXRldGltZSwNCgluZXh0​X3RhcmdldCBpbnQsDQoJdGFyZ2V0cyB0ZXh0LA0KCXBvc3RfaW50ZXJ2YWwgaW50LA0KCXBvc3RfaWQg​aW50LA0KCXBvc3RfYXBwIGludCwNCglwYXVzZSBpbnQsDQoJc3RhdHVzIGludCwgDQoJZmJfYWNjb3Vu​dCB2YXJjaGFyKDY0KSwNCglhdXRvX3BhdXNlIHRleHQsDQoJcmVwZWF0X2V2ZXJ5IGludGVnZXIsIA0K​CXJlcGVhdGVkX2F0IGRhdGV0aW1lLA0KCUZPUkVJR04gS0VZKHVzZXJpZCkgUkVGRVJFTkNFUyB1c2Vy​cyhpZCksDQoJRk9SRUlHTiBLRVkocG9zdF9pZCkgUkVGRVJFTkNFUyBwb3N0cyhpZCksDQoJRk9SRUlH​TiBLRVkocG9zdF9hcHApIFJFRkVSRU5DRVMgZmJhcHBzKGFwcGlkKQ0KKTsNCiIpOw0KDQpEQjo6R2V0​SW5zdGFuY2UoKS0+UXVlcnkoIg0KQ1JFQVRFIFRBQkxFIElGIE5PVCBFWElTVFMgcm9sZXMgKA0KCWlk​IElOVEVHRVIgUFJJTUFSWSBLRVkgQVVUT0lOQ1JFTUVOVCwNCgluYW1lIHZhcmNoYXIoMTYpLA0KCXBl​cm1pc3Npb25zIHRleHQsDQoJbWF4X3Bvc3RzIGludGVnZXIsDQoJbWF4X2ZiYWNjb3VudCBpbnRlZ2Vy​DQopOw0KIik7DQoNCg0KDQpEQjo6R2V0SW5zdGFuY2UoKS0+UXVlcnkoIg0KQ1JFQVRFIFRBQkxFIElG​IE5PVCBFWElTVFMgcG9zdHMgKA0KCWlkIElOVEVHRVIgUFJJTUFSWSBLRVkgQVVUT0lOQ1JFTUVOVCwN​Cgl1c2VyaWQgSU5URUdFUiwNCgljb250ZW50IHRleHQsDQoJZGF0ZV9jcmVhdGVkIGRhdGV0aW1lLA0K​CXBvc3RfdGl0bGUgdmFyY2hhcigyNTUpLA0KCXR5cGUgdmFyY2hhcigxNiksDQoJRk9SRUlHTiBLRVko​dXNlcmlkKSBSRUZFUkVOQ0VTIHVzZXIoaWQpDQopOw0KIik7DQoNCkRCOjpHZXRJbnN0YW5jZSgpLT5R​dWVyeSgiDQpDUkVBVEUgVEFCTEUgSUYgTk9UIEVYSVNUUyBvcHRpb25zICgNCglpZCBJTlRFR0VSIFBS​SU1BUlkgS0VZIEFVVE9JTkNSRU1FTlQsDQoJb3B0aW9uIHZhcmNoYXIoMjU1KSwNCgl2YWx1ZSB0ZXh0​DQopOw0KIik7DQpEQjo6R2V0SW5zdGFuY2UoKS0+UXVlcnkoIg0KQ1JFQVRFIFRBQkxFIElGIE5PVCBF​WElTVFMgbG9ncyAoDQoJaWQgSU5URUdFUiBQUklNQVJZIEtFWSBBVVRPSU5DUkVNRU5ULA0KCXNjaGVk​dWxlZHBvc3RzIGludCwNCgl1c2VyX2lkIGludGVnZXIsDQoJY29udGVudCB0ZXh0LA0KCWRhdGUgZGF0​ZXRpbWUsDQoJRk9SRUlHTiBLRVkoc2NoZWR1bGVkcG9zdHMpIFJFRkVSRU5DRVMgc2NoZWR1bGVkcG9z​dHMoaWQpLA0KCUZPUkVJR04gS0VZKHVzZXJfaWQpIFJFRkVSRU5DRVMgdXNlcnMoaWQpDQopOw0KIik7​DQpEQjo6R2V0SW5zdGFuY2UoKS0+UXVlcnkoIg0KQ1JFQVRFIFRBQkxFIElGIE5PVCBFWElTVFMgZmJh​cHBzICgNCglhcHBpZCB2YXJjaGFyKDI1NSkgUFJJTUFSWSBLRVksDQoJYXBwX3NlY3JldCB2YXJjaGFy​KDMyKSwNCglhcHBfbmFtZSB2YXJjaGFyKDI1NSksDQoJYWRtaW5fYWNjZXNzX3Rva2VuIHRleHQsDQoJ​YXBwX2F1dGhfbGluayB0ZXh0DQopOw0KIik7DQoNCkRCOjpHZXRJbnN0YW5jZSgpLT5RdWVyeSgiDQpD​UkVBVEUgVEFCTEUgSUYgTk9UIEVYSVNUUyBmYmFwcHMgKA0KCXVzZXJfaWQgSU5URUdFUiwNCglmYl9p​ZCB2YXJjaGFyKDMyKSwNCglmaXJzdG5hbWUgdmFyY2hhcigyNTUpLA0KCWxhc3RuYW1lIHZhcmNoYXIo​MjU1KSwNCglncm91cHMgdGV4dCwNCglwYWdlcyB0ZXh0LCANCglkZWZhdWx0QXBwIHZhcmNoYXIoNjQp​LA0KCUZPUkVJR04gS0VZKHVzZXJfaWQpIFJFRkVSRU5DRVMgdXNlcihpZCksDQoJQ09OU1RSQUlOVCBm​Yl9hY2NvdW50X3BrIFBSSU1BUlkgS0VZICh1c2VyX2lkLCBmYl9pZCkNCik7DQoiKTsNCg0KREI6Okdl​dEluc3RhbmNlKCktPlF1ZXJ5KCINCkNSRUFURSBUQUJMRSBJRiBOT1QgRVhJU1RTIGZiX2FjY291bnRz​ICgNCgl1c2VyX2lkIElOVEVHRVIsDQoJZmJfaWQgdmFyY2hhcigzMiksDQoJZmlyc3RuYW1lIHZhcmNo​YXIoMjU1KSwNCglsYXN0bmFtZSB2YXJjaGFyKDI1NSksDQoJZ3JvdXBzIHRleHQsDQoJcGFnZXMgdGV4​dCwNCglkZWZhdWx0QXBwIHZhcmNoYXIoNjQpLA0KCUZPUkVJR04gS0VZKHVzZXJfaWQpIFJFRkVSRU5D​RVMgdXNlcihpZCksDQoJQ09OU1RSQUlOVCBmYl9hY2NvdW50X3BrIFBSSU1BUlkgS0VZICh1c2VyX2lk​LCBmYl9pZCkNCik7DQoiKTsNCg0KDQpEQjo6R2V0SW5zdGFuY2UoKS0+UXVlcnkoIg0KQ1JFQVRFIFRB​QkxFIElGIE5PVCBFWElTVFMgZ3JvdXBzX2NhdGVnb3J5ICgNCglpZCBpbnRlZ2VyIFBSSU1BUlkgS0VZ​IEFVVE9JTkNSRU1FTlQsDQoJdXNlcl9pZCBJTlRFR0VSLA0KCWZiX2lkIHZhcmNoYXIoMzIpLA0KCWdy​b3VwcyB0ZXh0LA0KCXBhZ2VzIHRleHQsDQoJY2F0ZWdvcnlfbmFtZSB2YXJjaGFyKDY0KSwNCgljcmVh​dGVkX2F0IGRhdGV0aW1lLA0KCXVwZGF0ZWRfYXQgZGF0ZXRpbWUNCik7DQoiKTsNCg0KDQpEQjo6R2V0​SW5zdGFuY2UoKS0+UXVlcnkoIg0KQ1JFQVRFIFRBQkxFIElGIE5PVCBFWElTVFMgcHJvZHVjdF9hY3Rp​dmF0aW9uIChjb2RlIHRleHQpOw0KIik7DQoNCkRCOjpHZXRJbnN0YW5jZSgpLT5xdWVyeSgiSU5TRVJU​IElOVE8gcm9sZXMgKGBuYW1lYCxgcGVybWlzc2lvbnNgLGBtYXhfcG9zdHNgLGBtYXhfZmJhY2NvdW50​YCkgdmFsdWVzICgnQWRtaW5pc3RyYXRvcicsJ3tcImFkbWluXCI6MSxcInByaW1hcnlcIjoxLFwic3Rk​dXNlclwiOjF9JywnMCcsJzAnKSIpOw0KREI6OkdldEluc3RhbmNlKCktPnF1ZXJ5KCJJTlNFUlQgSU5U​TyByb2xlcyAoYG5hbWVgLGBwZXJtaXNzaW9uc2AsYG1heF9wb3N0c2AsYG1heF9mYmFjY291bnRgKSB2​YWx1ZXMgKCdQcmltYXJ5Jywne1wicHJpbWFyeVwiOjEsXCJzdGR1c2VyXCI6MX0nLCcxMDAnLCc3Jyki​KTsNCkRCOjpHZXRJbnN0YW5jZSgpLT5xdWVyeSgiSU5TRVJUIElOVE8gcm9sZXMgKGBuYW1lYCxgcGVy​bWlzc2lvbnNgLGBtYXhfcG9zdHNgLGBtYXhfZmJhY2NvdW50YCkgdmFsdWVzICgnU3RhbmRhcmQgdXNl​cicsJ3tcInN0ZHVzZXJcIjoxfScsJzQwJywnMycpIik7DQpEQjo6R2V0SW5zdGFuY2UoKS0+UXVlcnko​IklOU0VSVCBJTlRPIGBmYmFwcHNgIChgYXBwaWRgLGBhcHBfbmFtZWApIFZBTFVFUyAoJzE0NTYzNDk5​NTUwMTg5NScsJ0dyYXBoIEFQSSBFeHBsb3JlcicpIik7DQpEQjo6R2V0SW5zdGFuY2UoKS0+UXVlcnko​IklOU0VSVCBJTlRPIGBmYmFwcHNgIChgYXBwaWRgLGBhcHBfbmFtZWAsYGFwcF9hdXRoX2xpbmtgKSBW​QUxVRVMgKCc0MTE1ODg5NjQyNCcsJ0hUQyBTZW5zZScsJ2h0dHBzOi8vZ29vLmdsLzB0QmlXdScpIik7​DQpEQjo6R2V0SW5zdGFuY2UoKS0+UXVlcnkoIklOU0VSVCBJTlRPIGBmYmFwcHNgIChgYXBwaWRgLGBh​cHBfbmFtZWAsYGFwcF9hdXRoX2xpbmtgKSBWQUxVRVMgKCcxMDc1NDI1MzcyNCcsJ2lQaG90bycsJ2h0​dHBzOi8vZ29vLmdsL2dWMWZOYycpIik7DQpEQjo6R2V0SW5zdGFuY2UoKS0+UXVlcnkoIklOU0VSVCBJ​TlRPIGBmYmFwcHNgIChgYXBwaWRgLGBhcHBfbmFtZWAsYGFwcF9hdXRoX2xpbmtgKSBWQUxVRVMgKCcy​MDA3NTg1ODMzMTE2OTInLCdOb2tpYSBBY2NvdW50JywnaHR0cHM6Ly9nb28uZ2wvandON3daJykiKTsN​CkRCOjpHZXRJbnN0YW5jZSgpLT5RdWVyeSgiSU5TRVJUIElOVE8gYGZiYXBwc2AgKGBhcHBpZGAsYGFw​cF9uYW1lYCxgYXBwX2F1dGhfbGlua2ApIFZBTFVFUyAoJzE3NDgyOTAwMzM0NicsJ1Nwb3RpZnknLCdo​dHRwczovL2dvby5nbC9LVVl4NzQnKSIpOw0KREI6OkdldEluc3RhbmNlKCktPlF1ZXJ5KCJJTlNFUlQg​SU5UTyBwcm9kdWN0X2FjdGl2YXRpb24gKGBjb2RlYCkgdmFsdWVzICggJyIubWQ1KElucHV0OjpHZXQo​J3B1cmNoYXNlQ29kZScpKS4iJyApIik7"));
				session_destroy();
				Redirect::To("index.php");
			}else{
				$invalidPurchaseCode = true;
			}
		}
		$notInstalled = true;
	}
?>
<html>
<head>
	<title>The kingposter setup</title>
	<meta charset="UTF-8" />
	<meta name="description" content="">
	<meta name="author" content="As always yours! sup3rman">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="../theme/default/css/custom.css" rel="stylesheet" />
  	<link href="../theme/default/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<script src="../theme/default/js/jquery.js"></script>
	<script src="../theme/default/js/jsui.js"></script>
	<script src="../theme/default/bootstrap/js/bootstrap.min.js"></script>
	<style>
		/* --- Install page ---*/
		form.form-install .form-control { margin-bottom: 10px;}
		.form-install .panel-heading {
		    background: #3a5795 !important;
		}
		.form-install {
		  max-width: 400px;
		  padding: 15px;
		  margin: 0 auto;
		}

		.appLogo { margin: auto; width: 300px; }

		.verifyPurchaseCode {margin: 50px auto;width: 400px;}
		#purchaseCode {margin-bottom: 5px;}
	</style>
</head>
<body>
<noscript>
<div class="alerts alert alert-danger">
	<span class="glyphicon glyphicon-warning-sign"></span>
	<p class='alerttext'>JavaScript MUST be enabled in order for you to use kingposter. However, it seems JavaScript is either disabled or not supported by your browser. If your browser supports JavaScript, Please enable JavaScript by changing your browser options, then try again.</p></div>
</noscript>
<?php	
	/*
	|-------------------------------------------------------------------
	| Errors holder
	|-------------------------------------------------------------------
	|
	*/
	$errors = array();
	if(isset($notInstalled)){
		echo "<form method='POST' class='verifyPurchaseCode panel panel-primary'><div class='panel-body'>";
		if(isset($invalidPurchaseCode)){
			echo"<div class='alert alert-danger' role='alert'>Invalid Purchase Code</div>";
		}
		echo "<p>Please Enter your purchase code to continue<p><p><a href='../theme/default/images/how_to_get_my_purchase_code.jpg'>How to find my purchase code?</a><p><label for='purchaseCode' class='sr-only'>Enter your purchase code</label><input type='text' name='purchaseCode' id='purchaseCode' class='form-control' placeholder='Enter your purchase code'/><input type='submit' name='verify' class='btn btn-lg btn-primary btn-block' value='Verify'/></div></form>";
		exit();
	}
?>
<div id="wrapper">
<div class="appLogo">
	<img src="../theme/default/images/logo_large.png" alt="logo" />
</div>
<form class="form-install" method="POST">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><span class="glyphicon glyphicon-cog"></span> kingposter Setup</h3>
		</div>
		<div class="panel-body">
<?php
/*
|-------------------------------------------------------------------
| Check if the form has been submited
|-------------------------------------------------------------------
|
*/
if(Input::get('setup')){
		$db = DB::GetInstance();
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'username' => array(
				'disp_text' => 'Username',
				'required' => true,
				'min' => 2,
				'max' => 32,
				'unique' => 'users'
				),
			'password' => array(
				'disp_text' => 'Password',
				'required' => true,
				'min' => 6,
				'max' => 32
				),
			'repassword' => array(
				'disp_text' => 'Confirm Password',
				'required' => true,
				'matches' => 'password'
				),
			'email' => array(
				'disp_text' => 'E-mail',
				'required' => true,
				'unique' => 'users',
				'valid_email' => true,
				),
			'sitename' => array(
				'disp_text' => 'Site name',
				'required' => true,
				),
			));

		if($validation->passed()){
			$salt = Hash::salt(32);
			try{
					$user = new user();

					$user->create(array(
						'username' => Input::get('username'),
						'password' => Hash::make(Input::get('password'), $salt),
						'salt' => $salt,
						'email' => Input::get('email'),
						'roles' => '1',
						'active' => '1',
						'signup' => date('Y-m-d H:i:s')
					));
					
					if($newUser = $user->find(Input::get('username'))){
                      $user->defaultSettings($user->data()->id);
                  	}
                  	
					$siteurl = substr(CurrentPath(), 0, strrpos(CurrentPath(), 'install/'));
					$db->query("INSERT INTO `options` (`option`,`value`) values ('siteurl', ? )",array($siteurl));
					$db->query("INSERT INTO `options` (`option`,`value`) values ('sitename', ? )",array(Input::get('sitename')));
					$db->query("INSERT INTO `options` (`option`,`value`) values ('users_can_register', '1' )");
					$db->query("INSERT INTO `options` (`option`,`value`) values ('users_must_confirm_email', '0' )");

					// Setup the cron jobs (Evry 5 min by default)
					$output = shell_exec('crontab -l');
					$cron_file = "/tmp/crontab.txt";
					$cmd = "5 * * * * wget -O /dev/null ".$siteurl."cron.php >/dev/null 2>&1";
					file_put_contents($cron_file, $output.$cmd.PHP_EOL);
					exec("crontab $cron_file");
					
					session_destroy();
					Redirect::To("../index.php");
				
			}catch(Exception $e){
				die($e->getMessage());
			}
		}
		
		if(!empty($errors)){
			
			echo "<div class='alert alert-danger' role='alert'>";
			echo "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>";
			echo "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>";
			foreach($errors as $error){
				echo " ".$error."</br>";
			}
			echo "</div>";
			
		} elseif ($validation->errors()){
			
			echo "<div class='alert alert-danger' role='alert'>";
			echo "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>";
			echo "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>";
			foreach($validation->errors() as $error){
				echo " ".$error."</br>";
			}
			echo "</div>";
			
		} 
}
?>

			<label for="username" class="sr-only">Admin username</label>
      <input type="text" name="username" id="username" class="form-control" placeholder="Admin username" required="" autofocus="" value="<?php echo escape(Input::get('username')); ?>">

			<label for="password" class="sr-only">Admin password</label>
			<input type="password" name="password" id="password" placeholder="Admin Password" class="form-control" required="" />
			
			<label for="repassword" class="sr-only">Re-enter Admin password</label>
			<input type="password" name="repassword" id="repassword" placeholder="Re-enter Admin password" class="form-control" required="" />
			
			<label for="email" class="sr-only">Admin e-mail</label>
      <input type="text" name="email" id="email" class="form-control" placeholder="Admin e-mail" required="" autofocus="" value="<?php echo escape(Input::get('email')); ?>">
			
			<label for="sitename" class="sr-only">Site name</label>
			<input type="sitename" name="sitename" id="sitename" value="<?php if(Input::get('sitename')){ echo escape(Input::get('sitename')); }else{ echo "sup3rman nulled me :P";} ?>" placeholder="Site name" class="form-control" required="" />
			
			<input name="setup" type="submit" id="submit" value="Setup" class="btn btn-primary" />
			<input name="reset" type="reset" id="reset" value="Reset" class="btn btn-primary" />

	</div>
</div>  
</form>
<p class="footer"><?php echo lang('COPYRIGHT'); ?> &copy; <?php echo date('Y'); ?> Powered by <a href='http://kingposter.net' target='_blank'>Kingposter</a></p>
</div> <!-- End wrapper -->
</body>
</html>