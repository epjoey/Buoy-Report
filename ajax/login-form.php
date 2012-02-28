<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LoginForm.php';

$loginError = NULL;
$loginRel = NULL;

if (isset($_REQUEST['error']) && $_REQUEST['error'])
	$loginError = $_REQUEST['error'];
if (isset($_REQUEST['rel']) && $_REQUEST['rel'])
	$loginRel = $_REQUEST['rel'];
?>
<h1 class="form-head">Log In</h1>

<?	
$login = new LoginForm;	
$login->renderForm($loginError, $loginRel);
?>

<p class="need-account">Need an account? <a href="<?=Path::toRegister();?>">Sign up!</a></p>
