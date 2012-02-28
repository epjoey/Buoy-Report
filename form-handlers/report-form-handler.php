<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';

/* --------------- HANDLE REPORT FORM SUBMISSION --------------- */


/* Start off by validating required form fields */

if (!isset($_POST['quality']) || $_POST['quality'] === '') {
	header('Location:'.Path::toPostReport($_POST['locationid'], 'no-quality'));
	exit();
}

//unset empty text input
if ($_POST['text'] === '') {
	$_POST['text'] = null;
}


/* the current date to be stored */
$_POST['reportdate'] = intval(gmdate("U")); 

/* calculates date of observation if in the past */	
$offset = abs(intval($_POST['time_offset'])) * 60 * 60;
$_POST['obsdate'] = intval(gmdate("U", time()-$offset));
	
		

/* image copied into directory during form handle. wierd, I know. */
if (isset($_FILES['upload']['tmp_name']) && $_FILES['upload']['tmp_name'] !='') {
	
	$uploadStatus = handleFileUpload($_FILES['upload'], $_POST['reporterid']);

	/* redirect back to form if handleFileUpload returns error */
	if (isset($uploadStatus['error'])) {
		header('Location:'.Path::toPostReport($_POST['locationid'], $uploadStatus['error']));
		exit();	
	
	}

	if (isset($uploadStatus['imagepath'])) {
		$_POST['imagepath'] = $uploadStatus['imagepath'];
	}
				
} else if (isset($_POST['remoteImageURL']) && $_POST['remoteImageURL'] !='') {
	$_POST['imagepath'] = rawurldecode($_POST['remoteImageURL']);
}


	
/* Storing report in session */			
if (!isset($_SESSION)) session_start();
$_SESSION['new-report'] = $_POST; 



header('Location:'.Path::toUserHome());
?>