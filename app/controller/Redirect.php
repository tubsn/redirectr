<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;

class Redirect extends Controller {

	public function __construct() {
		$this->view('DefaultLayout');
		$this->models('Redirects,Tracking,QRGenerator');
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
	}

	public function find($shortURL) {

		$url = $this->Redirects->url($shortURL);
		// echo 'TESTSYSTEM: Weiterleitung würde jetzt hierhin gehen: ';
		//dump($url);
		$this->view->redirect($url);

	}

	// Crud

	public function create() {
		$this->view->render('redirects/create');
	}

	public function save() {
		$newID = $this->Redirects->new($_POST);
		$this->view->redirect('/cms');
	}

	public function edit($id) {

		$redirect = $this->Redirects->details($id);
		if (empty($redirect)) {throw new \Exception("Entry not Found", 404);}

		$this->view->redirect = $redirect;
		$this->view->chart = $this->Tracking->hits_by_day($id);
		$this->view->qrcode = $this->QRGenerator->go('https://lr.de/' . $redirect['shorturl']);

		$this->view->render('redirects/edit');
	}

	public function update($id) {
		$data = [
			'shorturl' => $_POST['shorturl'],
			'url' => $_POST['url']
		];
		$this->Redirects->set($data, $id);
		$this->view->redirect('/cms');
	}

	public function delete($id) {
		$this->Redirects->delete($id);
		$this->Redirects->remove_tracking($id);
		$this->view->redirect('/cms');
	}

	// End Crud

	public function cms() {

		/*
		$qr = (new QRCode)->render('https://www.lr-online.de');

		echo '<img src="'.$qr.'">';
		die;
		*/

		$this->view->stats = $this->Redirects->global_stats();
		$this->view->redirects = $this->Redirects->list();
		$this->view->render('redirects/index');

	}

	public function stats() {
		$this->view->render('redirects/stats');
	}

	public function to_main_page() {
		$this->view->redirect('https://www.lr-online.de');
	}

	public function search() {

		$query = $_GET['q'] ?? null;
		if (!$query) {throw new \Exception("Kein Suchbegriff angegeben", 404);}

		$this->view->query = $query;
		$this->view->redirects = $this->Redirects->list($query);
		$this->view->render('redirects/index');

	}

}
