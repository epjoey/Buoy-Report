<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class Header {

  static function renderSimpleHeader() {
    ?>
    <div id="header" class="header"> 
      <div class="container">
        <span class="header-left">
          <a href="<?= Path::toIntro();?>" class="br-icon"><img class="logo-graphic" id="large-logo" src="<?= Path::toImages() ?>logo-lrg.png" width="46" height="46"/></a>
        </span>
        <div class="header-right">
          <div class="dd-menu pull-left">
            <span class="block-link dd-trigger">
              <span class="dd-title">Menu</span>
              <img src="<?= Path::toImages() ?>down-arrow.png" width="15" height="9"/>
            </span>
            <ul class="inner-dd-menu">
              <li><a class="block-link" href="<?= Path::toReports(); ?>">Reports</a></li>
              <li><a class="block-link" href="<?=Path::toBuoys()?>">Buoys</a></li>
              <li><a class="block-link" href="<?= Path::toLocations(); ?>">Locations</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      jQuery(".dd-menu").on('mouseover', function(){
        jQuery(this).addClass('open')
      });
      jQuery(".dd-menu").on('mouseout', function() {
        jQuery(this).removeClass('open')
      });
      jQuery(".dd-trigger").on('click', function() {
        jQuery(this).parent('.dd-menu.open').toggleClass('open');
      });
    </script>    
    <?
  }

	static function renderHeader($user) {
		?>
		<div id="header" class="header"> 
			<div class="container">
				<span class="header-left">
					<a href="<?= Path::toIntro();?>" class="br-icon"><img class="logo-graphic" id="large-logo" src="<?= Path::toImages() ?>logo-lrg.png" width="46" height="46"/></a>
				</span>
				<div class="header-right">
          
          <div class="dd-menu pull-left">
            <span class="block-link dd-trigger">
              <span class="dd-title">All</span>
              <img src="<?= Path::toImages() ?>down-arrow.png" width="15" height="9"/>
            </span>
            <ul class="inner-dd-menu">
							<li><a class="block-link" href="<?= Path::toReports(); ?>">Reports</a></li>
							<li><a class="block-link" href="<?=Path::toBuoys()?>">Buoys</a></li>
							<li><a class="block-link" href="<?= Path::toLocations(); ?>">Locations</a></li>
            </ul>   
          </div>


	        <? if ($user->isLoggedIn) { ?>

            <div class="dd-menu pull-left">
              <span class="block-link dd-trigger">
                <span class="dd-title"><?= html($user->name); ?></span>
                <img src="<?= Path::toImages() ?>down-arrow.png" width="15" height="9"/>
              </span>
              <ul class="inner-dd-menu">
                <li><a class="block-link" href="<?=Path::toReports($user->id);?>">My Reports</a></li>
                <li><a class="block-link" href="<?=Path::tolocations($user->id);?>">My Locations</a></li>
                <li><a class="block-link" href="<?=Path::toProfile($user->id);?>">My Account</a></li>
                <li><a class="block-link" href="<?=Path::toLogout();?>">Log Out</a></li>
              </ul>   
            </div>
	                  
	        <? } else { ?>

	          <a class="block-link" href="<?=Path::toLogin();?>">Log In</a>
	      
	        <? } ?> 
				</div>
				<div class="clear"></div>		
			</div>		
		</div>
		<script type="text/javascript">
			jQuery(".dd-menu").on('mouseover', function(){
        jQuery(this).addClass('open')
			});
      jQuery(".dd-menu").on('mouseout', function() {
        jQuery(this).removeClass('open')
      });
      jQuery(".dd-trigger").on('click', function() {
        jQuery(this).parent('.dd-menu.open').toggleClass('open');
      });
		</script>
		<?
	}
	
}
?>