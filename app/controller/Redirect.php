<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;

class Redirect extends Controller {

	public function __construct() {
		$this->view('DefaultLayout');
		$this->models('Redirects,Tracking,QRGenerator');
		//if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
	}

	public function find($shortURL) {

		$url = $this->Redirects->url($shortURL);
		$this->view->redirect($url);

	}

	public function to_main_page() {
		$this->view->redirect('https://www.lr-online.de');
	}

}
