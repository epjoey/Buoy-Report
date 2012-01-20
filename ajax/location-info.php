<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';

$locationId = $_REQUEST['locationid'];
if ($_REQUEST['info'] == 'forecast') {
	if (isset($_REQUEST['url']) && $_REQUEST['url']) {
		if (!Persistence::dbContainsLocationForecast($locationId, $_REQUEST['url']))
			Persistence::insertLocationForecastUrl($locationId, $_REQUEST['url']);
	}

	$forecastLinks = Persistence::getForecastUrlsByLocationId($locationId);

	if (!empty($forecastLinks)) {
		foreach($forecastLinks as $link) {
			?>
			<p class="fc-link"><input type="checkbox" value="<?=$link?>" class="delete-link-check" style="display:none"><a target="_blank" href="<?=$link?>"><?=$link?></a></p>
			<?
		}
	} else {
		?> 
		<span class="no-data">None</span>
		<?
	}
	?>

	<?	
}

if ($_REQUEST['info'] == 'deletelinks') {
	$links = $_REQUEST['links'];
	Persistence::deleteLocationForecastUrl($locationId, $links);
}
