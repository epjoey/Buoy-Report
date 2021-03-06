<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class ReportUtils {
	static function getQualityOptions() {
		return array(1=>'Terrible',2=>'Crap',3=>'OK', 4=>'Pretty Fun',5=>'Great' );
	}	
	static function getWaveHeightsOptions() {
		return array(
			'1.5'=>array(1,2),
			'2.5'=>array(2,3),
			'3.5'=>array(3,4),
			'5'=>array(4,6),
			'7'=>array(6,8),
			'9'=>array(8,10),
			'11'=>array(10,12),
			'13.5'=>array(12,15),
			'17.5'=>array(15,20),
			'25'=>array(20,30)
		);		
	}

	static function getFiltersFromRequest($req) {
		$filters = array();
		$filters['quality'] 	  = $req['quality'];
		$filters['image']   	  = $req['image'];
		$filters['text']    	  = $req['text'];
		$filters['obsdate']    	  = $req['obsdate'];
		$filters['subLocationId'] = $req['sublocation'];
		$filters['locationIds']   = !empty($req['locationIds']) ? $req['locationIds'] : ($req['location'] ? array($req['location']) : array());
		$filters['reporterId']    = $req['reporterId'];
		return $filters;
	}
}


?>