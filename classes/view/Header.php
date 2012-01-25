<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/LocationList.php';

class Header {

	protected $userIsLoggedIn = FALSE;
	protected $userHasLocations = FALSE;
	protected $userLocations = NULL;
	protected $userName = NULL;
	protected $userEmail = NULL;
	protected $userId = NULL;	


	public function __construct($options = array()) {
		$this->userIsLoggedIn = $options['userIsLoggedIn'];
		$this->userHasLocations = $options['userHasLocations'];
		$this->userLocations = $options['userLocations'];
		$this->userName = $options['userName'];		
		$this->userEmail = $options['userEmail'];		
		$this->userId = $options['userId'];		
	}

	public function renderHeader() {
		?>
		<div id="header" class="header"> 
			<div class="container">
				<span class="header-left">
					<a href="<?= $this->userIsLoggedIn ? Paths::toUserHome() : Paths::toIntro();?>" class="br-icon"><img class="logo-graphic" id="large-logo" src="<?= Paths::toImages() ?>logo-lrg.png" width="46" height="46"/></a>
					<a class="home left-link" href="<?=$this->userIsLoggedIn ? Paths::toUserHome() : Paths::toIntro();?>">Home</a>
					<a class="location left-link" href="<?=Paths::toLocations();?>">Locations</a>
				</span>
				<div class="header-right">

				<? if ($this->userIsLoggedIn) { ?>

					<div class="user-menu" id="user-menu">
						<? if (!$this->userHasLocations) { ?>
							<a class="block-link post-report" href="<?=Paths::toLocations($reporter = null,$toPost = true)?>">Report</a>
						<? 
						} else { 
							$locOptions['locations'] = $this->userLocations;
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
								<span class="username"><?= html($this->userName); ?></span>
								<img src="<?= Paths::toImages() ?>down-arrow.png" width="15" height="9"/>
							</span>
							<ul class="inner-user-menu" id="inner-user-menu">
								<li><a class="block-link" href="<?=Paths::toUserHome($this->userId);?>">My Home</a></li>
								<li id="my-locations-btn">
									<a class="block-link" href="<?=Paths::tolocations($this->userId);?>">My Locations</a>
								</li>
								<li><a class="block-link" href="<?=Paths::toProfile($this->userId);?>">My Account</a></li>
								<li><a class="block-link" href="<?=Paths::toLogout();?>">Log Out</a></li>
							</ul>		
						</div>
					</div>
									
				<? } else { ?>

					<a class="block-link" href="<?=Paths::toLogin();?>">Log In</a><a class="block-link" href="<?=Paths::toRegister();?>">Sign Up</a>
			
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