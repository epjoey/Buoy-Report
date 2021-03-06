<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class Header {

  static function renderSimpleHeader($user, $location=NULL, $status=NULL) {
    ?>
    <div id="header" class="header"> 
      <div class="container">
        <span class="header-left">
          <a href="<?= Path::toIntro();?>" class="br-icon"><img class="logo-graphic" id="large-logo" src="<?= Path::toImages() ?>logo-lrg.png" width="46" height="46"/></a>
          <? if(isset($location)){
            ?>
            <h1><a class="loc-name" href="<?=Path::toLocation($location->id)?>">
              <?= html($location->locname) ?>
            </a></h1>
            <?
          }
          ?>
          <? if(isset($status)){
            ?>
            <h1><?= html($status) ?></h1>
            <?
          }
          ?>
        </span>
        <div class="header-right">
          <div class="dd-menu" ng-init="open = false" ng-class="{ open: open }">
            <span class="block-link dd-trigger" ng-click="open = !open">
              <img src="<?= Path::toImages() ?>down-arrow.png" width="15" height="9"/>
            </span>
            <ul class="inner-dd-menu">
              <li><a class="block-link" href="<?= Path::toLocations(); ?>">Locations</a></li>
              <li><a class="block-link" href="<?=Path::toBuoys()?>">Buoys</a></li>
              <li><a class="block-link" href="<?= Path::toReports(); ?>">Reports</a></li>
              <? if ($user->isLoggedIn) { ?>
                <li><a class="block-link" href="<?=Path::toProfile($user->id);?>">My stuff</a></li>
                <li><a class="block-link" href="<?=Path::toLogout();?>">Log out</a></li>                
              <? } else { ?>
                <li><a class="block-link" href="<?=Path::toLogin();?>">Log in</a></li>
              <? } ?>                 
            </ul>
          </div>
        </div>
      </div>
    </div>
    <?
  }

	static function renderHeader($user, $location=NULL, $status=NULL) {
    self::renderSimpleHeader($user, $location, $status)
		?>
		<script type="text/javascript">
      jQuery(".dd-menu .dd-trigger").on('click', function() {
        jQuery(this).parent('.dd-menu').toggleClass('open');
      });
		</script>
		<?
	}
	
}
?>