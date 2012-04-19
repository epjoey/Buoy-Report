<?
class FormHandlers {
	public static function handleReportForm($post = array(), $files = array()) {
		
		/* Start off by validating required form fields */

		if (!isset($post['quality']) || $post['quality'] === '') {
			throw new Exception('no-quality');
		}

		//unset empty inputs
		foreach($post as $key => $value) {
			if ($value == '') {
				$post[$key] = null;
			}
		}


		/* the current date to be stored */
		$post['reportdate'] = intval(gmdate("U")); 

		/* calculates date of observation if in the past */	
		$offset = abs(intval($post['time_offset'])) * 60 * 60;
		$post['obsdate'] = intval(gmdate("U", time()-$offset));
			

		/* in case image was deleted on edit report form */
		if (isset($post['delete-image']) && $post['delete-image'] == 'true') {
			$post['imagepath'] = '';
		}			

		/* image handling. also, if new image was uploaded on edit report form */
		if (isset($files['upload']['tmp_name']) && $files['upload']['tmp_name'] !='') {

			/* handleFileUpload either saves photo and returns path, or returns an error */	
			$uploadStatus = handleFileUpload($files['upload'], $post['reporterid']);

			/* redirect back to form if handleFileUpload returns error */
			if (isset($uploadStatus['error'])) {
				throw new Exception($uploadStatus['error']);	
			}

			/* store image path in post if saved succesfully */
			if (isset($uploadStatus['imagepath'])) {
				$post['imagepath'] = $uploadStatus['imagepath'];
			}

		/* in case they used picup, its a remote url */	
		} else if (isset($post['remoteImageURL']) && $post['remoteImageURL'] !='') {
			$post['imagepath'] = rawurldecode($post['remoteImageURL']);
		}

		return $post;


	}

}
?>