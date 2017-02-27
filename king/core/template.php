<?php
class Template
{
	private $_db,
			$_template,
			$_templateFolder,
			$_templateFolderPath,
			$_twig;
	
	public function __construct(){
		$this->_db = DB::getInstance();
		$this->_template = new TemplateRender();
		$this->_templateFolder = Options::Get("siteurl").'/theme/default';
		$this->_templateFolderPath = ABSPATH.'/theme/default';

		$loader = new \Twig_Loader_Filesystem("application/views");
		$this->twig = new \Twig_Environment($loader);

		$this->twig->addFunction(new Twig_SimpleFunction('l', function ($string) {
			l($string);
		}));

		$this->twig->addFunction(new Twig_SimpleFunction('lang', function ($string) {
			return lang($string);
		}));

		$this->twig->addFunction(new Twig_SimpleFunction('settings', function ($string) {
			return Options::Get($string);
		}));

		$this->twig->addFunction(new Twig_SimpleFunction('base_url', function ($string) {
			echo Options::Get("siteurl").$string;
		}));

		$this->twig->addFunction(new Twig_SimpleFunction('input_post', function ($string) {
			return Input::Get($string,'POST');
		}));

		$this->twig->addGlobal('version', VERSION);

	} 
	
	// header Template
	public function header($title,$extraContent = null){
		$this->_template->assign('templateFolder',$this->_templateFolder);
		$this->_template->assign('title',$title);
		$this->_template->render($this->_templateFolderPath,'header');
	}

	public function signUp(){
		$this->_template->assign('templateFolder',$this->_templateFolder);
		$this->_template->render($this->_templateFolderPath,'signup');
	}
	
	public function signIn(){
		$this->_template->assign('templateFolder',$this->_templateFolder);
		$this->_template->render($this->_templateFolderPath,'signin');
	}


	//footer Template 
	public function footer($v = true){
		$version = $v ? '| '.lang('VERSION').' '.VERSION : "";

		$this->_template->assign('VERSION',$version);
		$this->_template->assign('COPYRIGHT', lang('COPYRIGHT') .' &copy '.date('Y'));
		$this->_template->render($this->_templateFolderPath,'footer');
	}

	public function render($template,$data = array()){
		try {
			$template = $this->twig->loadTemplate($template.".twig");
			echo $template->render($data);
		} catch (Exception $e) {
		 	throw new Exception('ERROR: ' . $e->getMessage());
		}
	}
	

}


?>