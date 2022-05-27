<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\auth\Auth;
use flundr\utility\Pager;

class CMS extends Controller {

	public function __construct() {
		$this->view('DefaultLayout');
		$this->models('Redirects,Tracking,QRGenerator');
		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}
	}

	public function cms($category = null) {

		$itemsPerPage = 30;
		$pager = new Pager($this->Redirects->count($category), $itemsPerPage);
		$this->view->pager = $pager->htmldata;
		$offset = $pager->offset;

		$this->view->stats = $this->Redirects->category_stats($category);
		$this->view->redirects = $this->Redirects->list($category, $offset, $itemsPerPage);

		$this->view->render('redirects/index');

	}

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
			'url' => $_POST['url'],
			'category' => $_POST['category'],
		];
		$this->Redirects->set($data, $id);
		$this->view->redirect('/cms');
	}

	public function delete($id) {
		$this->Redirects->delete($id);
		$this->Redirects->remove_tracking($id);
		$this->view->redirect('/cms');
	}

	public function stats() {
		$this->view->render('redirects/stats');
	}

	public function search() {

		$query = $_GET['q'] ?? null;
		if (!$query) {throw new \Exception("Kein Suchbegriff angegeben", 404);}

		$this->view->query = $query;
		$this->view->redirects = $this->Redirects->find($query);
		$this->view->render('redirects/index');

	}

}
