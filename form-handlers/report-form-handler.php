<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';

/* --------------- HANDLE REPORT FORM SUBMISSION --------------- */


/* Start off by validating required form fields */

if (!isset($_POST['quality']) || $_POST['quality'] === '') {
	header('Location:'.Path::toPostReport($_POST['locationid'], 'no-quality'));
	exit();
}

//unset empty inputs
foreach($_POST as $key => $post) {
	if ($post === '') {
		$_POST[$key] = null;
	}
}


/* the current date to be stored */
$_POST['reportdate'] = intval(gmdate("U")); 

/* calculates date of observation if in the past */	
$offset = abs(intval($_POST['time_offset'])) * 60 * 60;
$_POST['obsdate'] = intval(gmdate("U", time()-$offset));
	
		

/* image handling */
if (isset($_FILES['upload']['tmp_name']) && $_FILES['upload']['tmp_name'] !='') {

	/* handleFileUpload either saves photo and returns path, or returns an error */	
	$uploadStatus = handleFileUpload($_FILES['upload'], $_POST['reporterid']);

	/* redirect back to form if handleFileUpload returns error */
	if (isset($uploadStatus['error'])) {
		header('Location:'.Path::toPostReport($_POST['locationid'], $uploadStatus['error']));
		exit();	
	
	}

	/* store image path in post if saved succesfully */
	if (isset($uploadStatus['imagepath'])) {
		$_POST['imagepath'] = $uploadStatus['imagepath'];
	}

/* in case they used picup, its a remote url */	
} else if (isset($_POST['remoteImageURL']) && $_POST['remoteImageURL'] !='') {
	$_POST['imagepath'] = rawurldecode($_POST['remoteImageURL']);
}


	
/* Storing report in session */			
if (!isset($_SESSION)) session_start();
$_SESSION['new-report'] = $_POST; 


/* redirect to user home page where page will look for session[new-report] and load via ajax. */
header('Location:'.Path::toUserHome());
?>