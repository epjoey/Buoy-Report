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
			td { 
				text-align: right; 
				color: white;
				font-family: helvetica, arial;
				font-size: 14px;
			}
			td:first-child {
				text-align: left; 
			}
			th {
				text-align: right; 
				color: #aaa;
				font-weight: bold;
				font-size: 12px;
				font-family: helvetica, arial;
			}
			h3 {
				color: white;
				font-weight: bold;
				font-size: 18px;
			}
			.data-loading {
				font-size: 12px;
				font-family: helvetica, arial;				
			}
			.buoy {
				margin-bottom: 30px;
			}
			h1 {
				margin-bottom: 20px;	
				font-size: 22px;
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
		  	angular.module('app', [ 'app.directives', 'app.filters' ]);
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
						if(m == 'MM'){
							return '-';
						}
						return (m * 3.281).toFixed(1);
					};
				});
				filters.filter('clean', function(){
					return function(d){
						return d === 'MM' ? '-' : d;
					};
				});				
		  })();
		</script>		
		<?
	}

	public function bodyAttrs() {
		return "ng-app='app'";
	}

	public function renderBodyContent() {
		?>
		<h1>
			<?= html($this->location->locname) ?>
		</h1>
		<?

		foreach($this->location->buoys as $buoy){
			?>
			<div ng-buoy="<?= $buoy->buoyid ?>"
				ng-buoy-name="<?= $buoy->name ?>"></div>
			<?
		}

		?>
		<script type="text/ng-template" id="buoy">
			<div class="buoy">
				<h3>{{ buoyId }} {{ buoyName }}</h3>
				<div ng-if="!data">
					<span class="data-loading">
						Loading...
					</span>
				</div>
				<table ng-if="data">
					<thead>
						<tr>
							<th><? // time ?></th>
							<th>&nbsp;WVHT</th>
							<th>&nbsp;DPD</th>
							<th>&nbsp;MWD</th>
							<th>&nbsp;WSPD</th>
							<th>&nbsp;WDIR</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="d in data">
							<td>{{ ::d.gmttime|unix2date }}</td>
							<td>{{ ::d.swellheight|m2f }}</td>
							<td>{{ ::d.swellperiod|clean }}</td>
							<td>{{ ::d.swelldir|clean }}</td>
							<td>{{ ::d.windspeed|clean }}</td>
							<td>{{ ::d.winddir|clean }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</script>
		<?
	}

}