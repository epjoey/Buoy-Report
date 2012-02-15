<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LocationList.php';

class Header {

	public function renderHeader($user) {
		?>
		<div id="header" class="header"> 
			<div class="container">
				<span class="header-left">
					<a href="<?= $user->isLoggedIn ? Path::toUserHome() : Path::toIntro();?>" class="br-icon"><img class="logo-graphic" id="large-logo" src="<?= Path::toImages() ?>logo-lrg.png" width="46" height="46"/></a>
					<a class="home left-link" href="<?=$user->isLoggedIn ? Path::toUserHome() : Path::toIntro();?>">Home</a>
					<a class="location left-link" href="<?=Path::toLocations();?>">Locations</a>
				</span>
				<div class="header-right">

				<? if ($user->isLoggedIn) { ?>

					<div class="user-menu" id="user-menu">
						<? if (!$user->hasLocations) { ?>
							<a class="block-link post-report" href="<?=Path::toLocations($reporter = null,$toPost = true)?>">Report</a>
						<? 
						} else { 
							$locOptions['locations'] = $user->locations;
							$locOptions['showSeeAll'] = TRUE;
							$locOptions['toPost'] = TRUE;
							$locationDrop = new LocationList($locOptions);
							?>
							<div class="block-link post-report">
								<span id="pr-trigger">Report</span>
								<div class="location-drop-down">
									<? $locationDrop->renderLocations(); ?>
								</div>
							</div>
						<? } ?>
						<div class="block-link user-options">
							<span class="user" id="user-trigger">
								<span class="username"><?= html($user->name); ?></span>
								<img src="<?= Path::toImages() ?>down-arrow.png" width="15" height="9"/>
							</span>
							<ul class="inner-user-menu" id="inner-user-menu">
								<li><a class="block-link" href="<?=Path::toUserHome($user->id);?>">My Home</a></li>
								<li id="my-locations-btn">
									<a class="block-link" href="<?=Path::tolocations($user->id);?>">My Locations</a>
								</li>
								<li><a class="block-link" href="<?=Path::toProfile($user->id);?>">My Account</a></li>
								<li><a class="block-link" href="<?=Path::toLogout();?>">Log Out</a></li>
							</ul>		
						</div>
					</div>
									
				<? } else { ?>

					<a class="block-link" href="<?=Path::toLogin();?>">Log In</a><a class="block-link" href="<?=Path::toRegister();?>">Sign Up</a>
			
				<? } ?>	

				</div>
				<div class="clear"></div>		
			</div>		
		</div>
		<script type="text/javascript">
			var isTouchScreen = 'createTouch' in document;
			if (isTouchScreen) {
				jQuery('#header #user-trigger').click(function(){
					jQuery('#header').removeClass('expanded-report').toggleClass('expanded-user-menu');
				});
				jQuery('#header #pr-trigger').click(function(){
					jQuery('#header').removeClass('expanded-user-menu').toggleClass('expanded-report');
				});
			} else {
				jQuery('#header .user-options').hover(
					function(){
						jQuery('#header').removeClass('expanded-report').addClass('expanded-user-menu')
					}, function(){
						jQuery('#header').removeClass('expanded-user-menu')
					}
				);
				jQuery('#header .post-report').hover(
					function(){
						jQuery('#header').removeClass('expanded-user-menu').toggleClass('expanded-report')
					}, function(){
						jQuery('#header').removeClass('expanded-report')
					}
				);
			}
		</script>
		<?
	}
	
}
?>