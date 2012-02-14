<?

function setPicupSessionId($form, $id) {

	if (!isset($_SESSION)) 
		session_start();

	if ($form == 'report-form') {

		if (isset($_SESSION['edit-report-form-picup-id']))
			unset($_SESSION['edit-report-form-picup-id']);

		$_SESSION['report-form-picup-id'] = $id;
	}

	if ($form == 'edit-report-form') {

		if (isset($_SESSION['report-form-picup-id']))
			unset($_SESSION['report-form-picup-id']);	

		$_SESSION['edit-report-form-picup-id'] = $id;
	}
}

function getPicupSessionInfo() {

	if (!isset($_SESSION)) 
		session_start();

	if (isset($_SESSION['report-form-picup-id']))
		return array('form'=>'report-form', 'id'=>$_SESSION['report-form-picup-id']);	

	if (isset($_SESSION['edit-report-form-picup-id']))
		return array('form'=>'edit-report-form', 'id'=>$_SESSION['edit-report-form-picup-id']);
}

?>