<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class StatusMessageService {	

	static function setStatusMsgForAction($msg, $action) {
		if (!isset($_SESSION)) {
			session_start();
		}
		$_SESSION['statusMsg'][$action] = $msg;
	}

	static function getStatusMsgForAction($action) {
		return $_SESSION['statusMsg'][$action];
	}

	static function clearStatusForAction($action) {
		unset($_SESSION['statusMsg'][$action]);
	}

}
?>