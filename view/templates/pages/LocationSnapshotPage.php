<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class LocationSnapshotPage extends Page {

	public function getBodyClassName() {
		return 'snapshot-page';
	}

	public function renderCss() {
		parent::renderCss();
	}

	public function renderJs() {
		?>
    <script
  src="https://code.jquery.com/jquery-2.1.4.min.js"
  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
  crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.8/angular.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.3/underscore-min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>
		<script src="/view/static/js/ng-app/lib/filters.js"></script>
		<script src="/view/static/js/ng-app/lib/services.js"></script>
		<script src="/view/static/js/ng-app/lib/controllers.js"></script>
		<script src="/view/static/js/ng-app/lib/directives.js"></script>
		<script src="/view/static/js/ng-app/lib/index.js"></script>
		<script type="text/javascript">

		  // Drag/drop.
			function allowBuoyDrop(ev) {
		    ev.preventDefault();
			}

			function dragBuoy(ev) {
				var buoyId = $(ev.target).attr('ng-buoy');
			  ev.dataTransfer.setData("text", buoyId);
			}

			function dropBuoy(ev) {
		    ev.preventDefault();
		    var buoyId = ev.dataTransfer.getData("text");
		    var dragged = $("[ng-buoy='"+buoyId+"']");
		    var dropTarget = $(ev.target).parents('[ng-buoy]');
		    dragged.insertBefore(dropTarget);
		    angular.element(dragged).scope().buoyCtrl.saveSortOrder();
			}

		</script>
		<?
	}

	public function bodyAttrs() {
		return "ng-app='app'";
	}

	public function renderHeader() {
		Header::renderSimpleHeader($this->user, $this->location);
	}

	public function renderBodyContent() {
		?>
		<div ng-location="<?= $this->location->id ?>">
			<div class="ss-outer" ng-controller="SnapshotFormCtrl">
				<div class="ss-header">
					<h1>
						<button class="btn save-btn"
							ng-click="toggleForm()"
							ng-cloak
							ng-disabled="isFormOpen"
						>
							Save snapshot &darr;
						</button>
					</h1>
				</div>

				<div class="ss-form"
					ng-show="isFormOpen"
				>
					<form action="<?=Path::toHandleReportSubmission()?>" method="post">
						<? FormFields::renderTimeSelect($this->location); ?>

						<input type="hidden" name="locationid" value="<?=$this->location->id?>" />
						<input type="hidden" name="locationname" value="<?=$this->location->locname?>" />
						<input type="hidden" name="submit" value="submit-report" />
						<input class="submit-btn" type="submit" name="submit_report" value="Submit">
						<div class="open-subfields clickable pull-left" ng-click="subFieldsOpen = !subFieldsOpen">+ Add Report</div>

						<span class="pull-right clickable"
							ng-click="toggleForm()"
							ng-cloak
						>
							Cancel
						</span>
						<div class="clear"></div>
						<div class="subfields" ng-show="subFieldsOpen">
							<div class="field text">
								<textarea name="text" class="text-input" placeholder="Note"></textarea>
							</div>
							<?
							FormFields::renderQualitySelect();
							if ($this->location->sublocations) {
								FormFields::renderSubLocationSelect($this->location->sublocations);
							}
							FormFields::renderWaveHeightField(ReportOptions::getWaveHeights());
							?>
							<div class="field image last">
								<? FormFields::renderImageInput(null) ?>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="buoys-wrap">
				<div class="buoys">
					<?
					foreach($this->location->buoys as $buoy){
						?>
						<div class="buoy"
							ng-buoy="<?= $buoy->buoyid ?>"
							ng-buoy-name="<?= $buoy->name ?>"
							draggable="true"
							ondragstart="dragBuoy(event)"
							ondrop="dropBuoy(event)"
							ondragover="allowBuoyDrop(event)"
						></div>
						<?
					}
					?>
				</div>
			</div>
		</div>


		<script type="text/ng-template" id="buoy.template">
			<h3>
				<a ng-href="{{ ::urls.noaaBuoy(buoyId) }}">
					{{ buoyId }}: {{ buoyName }}
				</a>
			</h3>
			<div class="buoy-data-window">
				<div ng-repeat="page in buoyCtrl.pages">
					<span class="data-loading"
						ng-if="!page.data"
					>
						Loading...
					</span>
					<table ng-if="page.data">
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
							<tr ng-repeat="d in page.data">
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
					<a class="paginate"
					 	ng-if="$last && page.data.length"
						ng-click="buoyCtrl.paginate()"
					>
						&darr;
					</a>
				</div>
			</div>
		</script>
		<?
	}

}