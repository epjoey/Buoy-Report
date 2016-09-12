<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class Location2Page extends Page {

	public function getBodyClassName() {
		return 'location-page';
	}

	public function renderCss() {
		parent::renderCss();
		?>
		<style>
			h1 {
				margin-bottom: 20px;
				font-size: 22px;
				text-align: center;
			}		
			.buoys {
				text-align: center;
				font-family: helvetica, arial;
			}
			.buoy {
				display: inline-block;
				margin-bottom: 30px;
			}
			.data-loading {
				font-size: 12px;
			}
			h3 {
				color: white;
				font-size: 13px;
				font-weight: bold;
			}						
			table {
				width: 300px;
			}
			thead {
				background: rgba(255,255,255,.35);
				border-bottom: 2px solid #06223c
			}
			thead tr:first-child th {
				padding-top: 4px;
			}
			thead tr.units th {
				font-size: 8px;
				line-height: 12px;
				text-transform: uppercase;
			}
			th {
				text-align: left;
				color: black;
				font-weight: bold;
				font-size: 11px;
				line-height: 12px;
				padding-right: 2px;
			}
			tbody tr:nth-child(odd) {
				background: rgba(255,255,255,.1);
			}						
			td {
				color: white;
				font-size: 13px;
				line-height: 22px;
				text-align: left;
				padding-right: 2px;
			}
			td:first-child, th:first-child {
				padding-left: 4px;
			}
			td sub { 
				font-size: 9px;
				line-height: 12px;
				padding-left: 1px;
			}
			@media only screen and (min-width: 768px) {
				.buoy {
					margin-right: 20px;
					padding-left: 12px;
					width: 300px;
				}
				tr {
					border-left: 4px solid #06223c;
				}

			}
		</style>
		<?
	}

	public function renderJs() {
		?>
    <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://code.angularjs.org/1.5.8/angular.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>
		<script type="text/javascript">
		  (function() {
		  	angular.module('app', [ 'app.directives', 'app.filters', 'app.services' ]);
				var directives = angular.module('app.directives', []);
				directives.directive('ngBuoy', [
					'$http', '$parse',
					function($http, $parse){
						return {
							templateUrl: 'buoy',
							scope: true,
							compile: function($el, $attrs){
								return function(scope, el, attrs){
									scope.buoyId = attrs.ngBuoy;
									scope.buoyName = attrs.ngBuoyName;
									var url = '/controllers/buoy/buoy.php?buoyid=' + scope.buoyId;
									$http.get(url).success(function(data){
										scope.data = data;
									});
								};
							}
						};
					}
				]);

				var filters = angular.module('app.filters', []);
				filters.filter('unix2date', function(){
					return function(input){
						return moment.unix(input).format("M/D h:mm a");
					};
				});
				filters.filter('m2f', function(){
					return function(m){
						if(m == 'MM') return '-';
						return (m * 3.281).toFixed(1);
					};
				});
				filters.filter('clean', function(){
					return function(d){
						return d === 'MM' ? '-' : d;
					};
				});
				filters.filter('mps2kn', function(){
					return function(d){
						if(d == 'MM') return '-';
						return (d * 1.944).toFixed(1);
					};
				});

				1.94384
				filters.filter('dir2str', function(){
					return function(d){
						if(d === 'MM') return '';
						d = parseInt(d);
						return (
							(d < 11.25) ? 'N' :
							(d < 33.75) ? 'NNE' :
							(d < 56.25) ? 'NE' :
							(d < 78.75) ? 'ENE' :
							(d < 101.25) ? 'E' :
							(d < 123.75) ? 'ESE' :
							(d < 146.25) ? 'SE' :
							(d < 168.75) ? 'SSE' :
							(d < 191.25) ? 'S' :
							(d < 213.75) ? 'SSW' :
							(d < 236.25) ? 'SW' :
							(d < 258.75) ? 'WSW' :
							(d < 281.25) ? 'W' :
							(d < 303.75) ? 'WNW' :
							(d < 326.25) ? 'NW' :
							(d < 348.75) ? 'NNW' : 'N'
						);
					};
				});

				var services = angular.module('app.services', []);
				services.factory('path', [
					function(){
						return {
							toNoaaBuoy: function(buoyId){
								return 'http://www.ndbc.noaa.gov/station_page.php?station=' + buoyId;
							}
						};
					}
				]);

			  services.run([
			    '$rootScope', 'path',
			    function($rootScope, path){
			      $rootScope.path = path;
			    }
			  ]);
		  })();
		</script>
		<?
	}

	public function bodyAttrs() {
		return "ng-app='app'";
	}

	public function renderHeader() {
		Header::renderSimpleHeader();
	}


	public function renderBodyContent() {
		?>
		<h1>
			<a href="<?=Path::toLocation($this->location->id)?>">
				<?= html($this->location->locname) ?>
			</a>
			<!-- <span class="btn save-btn">Save</span> -->
		</h1>
		<div class="buoys">
			<?
			foreach($this->location->buoys as $buoy){
				?>
				<div class="buoy" ng-buoy="<?= $buoy->buoyid ?>"
					ng-buoy-name="<?= $buoy->name ?>"></div>
				<?
			}
			?>
		</div>
		<script type="text/ng-template" id="buoy">
			<h3>
				<a ng-href="{{ ::path.toNoaaBuoy(buoyId) }}">
					{{ buoyId }}: {{ buoyName }}
				</a>
			</h3>
			<div ng-if="!data">
				<span class="data-loading">
					Loading...
				</span>
			</div>
			<table ng-if="data">
				<thead>
					<tr>
						<th>TIME</th>
						<th>WVHT</th>
						<th>DPD</th>
						<th>MWD</th>
						<th>WSPD</th>
						<th>WDIR</th>
					</tr>
					<tr class='units'>
						<th></th>
						<th>ft</th>
						<th>sec</th>
						<th></th>
						<th>kn</th>
						<th></th>
					</tr>									
				</thead>
				<tbody>
					<tr ng-repeat="d in data">
						<td>{{ ::d.gmttime|unix2date }}</td>
						<td>{{ ::d.swellheight|m2f }}</td>
						<td>{{ ::d.swellperiod|clean }}</td>
						<td>
							{{ ::d.swelldir|clean }}
							<sub>{{::d.swelldir|dir2str}}</sub>
						</td>
						<td>{{ ::d.windspeed|mps2kn }}</td>
						<td>
							{{ ::d.winddir|clean }}
							<sub>{{::d.winddir|dir2str}}</sub>
						</td>
					</tr>
				</tbody>
			</table>
		</script>
		<?
	}

}