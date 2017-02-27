<?php
/*
|--------------------------------------------------------------------------
| Check the existance of the db file
|--------------------------------------------------------------------------
|
| If the database is not exists create one and create required tables
| Create the database file and insert the default sittengs
|
|--------------------------------------------------------------------------
*/

Session::Delete("setup");
Session::Delete("db_exists");
Session::Put("db_exists",true);

if(Config::Get('db/driver') == "mysql"){
	if (checkTablesExists() != 1) {
		$db = checkTablesExists() == 2 ? true : false;
		Session::Put("setup",true);
		Session::Put("db_exists",$db);
		Redirect::To("install");
		die();
	}
}else{
	$dbFile = ABSPATH.'/database/'.Config::Get('db/dbname').'.db';
	if (!file_exists($dbFile)) {
		Session::Put("setup",true);
		Session::Put("db_exists",false);
		Redirect::To("install");
		die();
	}else if (checkTablesExists() != 1) {
		$db = checkTablesExists() == 2 ? true : false;
		Session::Put("setup",true);
		Session::Put("db_exists",$db);
		Redirect::To("install");
		die();
	}
}

function checkTablesExists(){
	try{
		if(DB::GetInstance()->queryGet("SELECT id FROM users where roles = 1 ")->count() == 0) {
			if(DB::GetInstance()->queryGet("SELECT id FROM users")->count() == 0) {
				return 2;
			}
		}else{
			return 1;
		}
	}catch(Exception $ex){
		return 3;
	}
}

// Everything is okay
Session::Delete("setup");
Options::CheckSiteUrl();

?>