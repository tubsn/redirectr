<?php

namespace app\controller;
use flundr\mvc\Controller;

class Redirect extends Controller {

	public function __construct() {
		$this->view('DefaultLayout');
		$this->models('Redirects');
	}

	public function find($shortURL) {

		$url = $this->Redirects->url($shortURL);
		dd($url);


	}

	public function info($id) {

		dd($this->Redirects->stats($id));



	}

	public function cms() {

		$this->view->redirects = $this->Redirects->list();
		$this->view->render('redirects/index');

	}

}
