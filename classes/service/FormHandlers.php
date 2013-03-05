<?
class FormHandlers {
	public static function handleReportForm($post = array(), $files = array()) {
		
		//unset empty inputs
		foreach($post as $key => $value) {
			if ($value === '' || $value === false) {
				$post[$key] = null;
			}
		}


		/* Start off by validating required form fields */

		if (!isset($post['quality'])) {
			throw new Exception('no-quality');
		}

		if (!isset($post['time_offset']) && !isset($post['arbitrary_date'])) {
			throw new Exception('no-time');
		}	

		if (isset($post['arbitrary_date'])) {
		
			$date = $post['arbitrary_date'];
			//if ($date not in format) { throw new ... }
			$post['obsdate'] = intval(gmdate("U", time(strtotime($date))));
		
		} else {

			/* calculates date of observation if in the past */	
			$offset = abs(intval($post['time_offset'])) * 60 * 60;
			$post['obsdate'] = intval(gmdate("U", time()-$offset));			

		}

		/* the current date to be stored */
		$post['reportdate'] = intval(gmdate("U")); 

				
		if (isset($files['upload']['tmp_name']) && $files['upload']['tmp_name'] !='') {
			$post['imagepath'] = self::uploadFile($files, $post);
		}
		/* in case they used picup, its a remote url */	
		else if (isset($post['remoteImageURL']) && $post['remoteImageURL'] !='') {
			$post['imagepath'] = rawurldecode($post['remoteImageURL']);
		}
		
		return $post;


	}

	/* image handling. also, if new image was uploaded on edit report form */
	function uploadFile($files, $post) {

		/* handleFileUpload either saves photo and returns path, or returns an error */	
		$uploadStatus = handleFileUpload($files['upload'], $post['reporterid']);

		/* redirect back to form if handleFileUpload returns error */
		if (isset($uploadStatus['error'])) {
			throw new Exception($uploadStatus['error']);	
		}

		/* store image path in post if saved succesfully */
		return $uploadStatus['imagepath'];
	}

	function handleEditReportForm($post = array(), $files = array()) {
		
		//unset empty inputs
		foreach($post as $key => $value) {
			if ($value === '' || $value === false) {
				$post[$key] = null;
			}
		}

		if (!isset($post['quality'])) {
			throw new Exception('no-quality');
		}

		/* in case image was deleted on edit report form */
		if (isset($post['delete-image']) && $post['delete-image'] == 'true') {
			$post['imagepath'] = '';
		}
		
		/* handleFileUpload either saves photo and returns path, or returns an error */	
		if (isset($files['upload']['tmp_name']) && $files['upload']['tmp_name'] !='') {
			$post['imagepath'] = self::uploadFile($files, $post);
		}
		/* in case they used picup, its a remote url */	
		else if (isset($post['remoteImageURL']) && $post['remoteImageURL'] !='') {
			$post['imagepath'] = rawurldecode($post['remoteImageURL']);
		}

		return $post;		
	}

}
?>