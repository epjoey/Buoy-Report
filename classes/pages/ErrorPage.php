<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/Page.php';

class ErrorPage extends Page {

	public function loadData() {
		parent::loadData();
		$this->pageTitle = '404 Not Found';
	}

	public function getBodyClassName() {
		return '404';
	}	

	public function renderBodyContent() {
		?>
		<h1>Sorry, reporter.</h1>

		<p>Error 404 (page does not exist).</p>
		<p>If you think its a bug, shoot over a bug report to jhodara(at)gmail.com</p>
		<?	
	}

}

