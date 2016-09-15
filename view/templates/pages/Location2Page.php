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
    <script src="/view/static/js/lib/jquery-2.1.4.js"></script>
    <script src="/view/static/js/lib/angular-1.5.8.js"></script>
    <script src="/view/static/js/lib/underscore-1.4.3.min.js"></script>
		<script src="/view/static/js/lib/moment-2.14.1.min.js"></script>
		<script type="text/javascript">
		  (function() {
		  	angular.module('app', [ 'app.directives', 'app.filters', 'app.services', 'app.controllers' ]);
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
				services.factory('urls', [
					function(){
						return {
							noaaBuoy: function(buoyId){
								return 'http://www.ndbc.noaa.gov/station_page.php?station=' + buoyId;
							},
							api: {
								reportFormHandler: '/controllers/report/report-form-handler.php'
							}
						};
					}
				]);

			  services.run([
			    '$rootScope', 'urls',
			    function($rootScope, urls){
			      $rootScope.urls = urls;
			    }
			  ]);

			  var controllers = angular.module('app.controllers', []);
			  controllers.controller('SnapshotFormCtrl', [
			  	'$scope', 'urls', '$http',
			  	function($scope, urls, $http){
			  		$scope.post = {};
			  		// $scope.submit = function(){
			  		// 	$http.post(urls.api.reportFormHandler, _.extend($scope.post, {
			  		// 		'locationId': $scope.locationId,
			  		// 		'foo': 'bar'
			  		// 	})).then(function(data){
			  		// 		console.log(data);
			  		// 	});
			  		// };
			  		$scope.closeForm = function(){
			  			$scope.isFormOpen = false;
			  		};
			  		$scope.toggleForm = function(){
			  			$scope.isFormOpen = !$scope.isFormOpen;
			  		};
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
		<div ng-controller="SnapshotFormCtrl">
			<h1>
				<a class="loc-name" href="<?=Path::toLocation($this->location->id)?>">
					<?= html($this->location->locname) ?>
				</a>
				<button class="btn save-btn"
					ng-disabled="saveFormOpen"
					ng-click="toggleForm()"
				>
					Save snapshot
				</button>
			</h1>
			<div class="snapshot-form"
				ng-if="isFormOpen"
			>
				<form class="centered-form" action="<?=Path::toHandleReportSubmission2()?>" method="post">
					<? FormFields::renderTimeSelect($this->location); ?>
					<div class="open-subfields" ng-click="subFieldsOpen = !subFieldsOpen">Report &darr;</div>
					<div class="subfields" ng-show="subFieldsOpen">
						<?
						FormFields::renderQualitySelect(); 
						if ($this->location->sublocations) {
							FormFields::renderSubLocationSelect($this->location->sublocations);
						}
						FormFields::renderWaveHeightField(ReportOptions::getWaveHeights());?>
						<div class="field image last">
							<? FormFields::renderImageInput() ?>
						</div>
					</div>
					
					<input type="hidden" name="locationid" value="<?=$this->location->id?>" />
					<input type="hidden" name="locationname" value="<?=$this->location->locname?>" />
					<input type="hidden" name="submit" value="submit-report" />					
					<input class="submit-btn" type="submit" name="submit_report" value="Submit">

					<div>
						<span class='cancel' ng-click='closeForm()'>Cancel</span>
					</div>
				</form>
			</div>
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
				<a ng-href="{{ ::urls.noaaBuoy(buoyId) }}">
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