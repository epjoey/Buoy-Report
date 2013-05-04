<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class ErrorPage extends Page {


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

