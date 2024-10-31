<?php

class SearchlivePlugin{

	public function __construct()
	{	
		$this->initFiles();
	}

	private function initFiles()
	{
		include_once ( SEARCHLIVE_PATH . 'inc/admin.php');
		include_once ( SEARCHLIVE_PATH . 'inc/query.php');
		include_once ( SEARCHLIVE_PATH . 'inc/helper.php');
		include_once ( SEARCHLIVE_PATH . 'inc/builder.php');
	}

}
new SearchlivePlugin();