<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class Image {
	public static function render($path, $thumbnail=FALSE) {
		if (!$path) {
			return;
		}
		$detect = new Mobile_Detect();
		if ($thumbnail) {
			$detect->isSmallDevice() ? $dims = array(50,50) : $dims = array(80,80);	
		}
		else if (!$thumbnail) {
			$detect->isSmallDevice() ? $dims = array(280,260) : $dims = array(520,400);	
		}
		$image = getImageInfo($path, $dims[0], $dims[1]);
		if (!$image) {
			return;
		}
		if ($thumbnail) {
			?>
			<li class="image-container thumbnail-image">
				<img src="" imgsrc="<?= $image['src'] ?>" width="<?=$image['width']?>" height="<?=$image['height']?>"/>
			</li>
			<?
		} else {
			?>
			<div class="image-container large-image">
				<a href="<?=$image['src']?>" target="_blank">
					<img src="" imgsrc="<?= $image['src'] ?>" width="<?=$image['width']?>" height="<?=$image['height']?>"/>
				</a>
			</div>
			<?	
		}		
	}
}
?>