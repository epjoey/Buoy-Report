<?
class FilterService {
	static function getReportFilterRequests(){
		$filters = array();
		$reporters = $_REQUEST['reporters'];
		$reporterId = $_REQUEST['reporter'];
		$locations = $_REQUEST['locations'];
		$locationId = $_REQUEST['location'];
		$date = $_REQUEST['date'];
		
		if (isset($reporters)) {
			$filters['reporters'] = explode(',', $reporters);
		}
		if (isset($reporterId)) {
			$filters['reporterId'] = intval($reporterId);
		}		
		if (isset($locations)) {
			$filters['locations'] = explode(',', $locations);
		}
		if (isset($locationId)) {
			$filters['locationId'] = intval($locationId); //overwrite multiple locations with specific one
		}		
		if (isset($date)) {
			if (strlen($date) < 6 || !strtotime($date)) 
				$filters['error'] = 'Date is not valid. Must be in m/d/yy format.';
			else 
				$filters['date'] = strtotime($date) + 59*60*24; //adding just under 24 hours to catch that day's reports
		}
		$filters['sublocation'] = $_REQUEST['sublocation'];
		$filters['text'] = $_REQUEST['text'];
		$filters['image'] = $_REQUEST['image'];
		$filters['quality'] = $_REQUEST['quality'];
		return $filters;		
	}
}
?>