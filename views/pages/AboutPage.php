<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class AboutPage extends Page {

	public function getBodyClassName() {
		return 'about';
	}	

	public function renderBodyContent() {
		?>
		<div>
			<h1>Buoy Report</h1>
			<span class="border"></span>
			<p><strong>Buoy Report</strong> is a tool to help us further our understanding of the ocean.</p>
			<p>Current ocean conditions are heavily affected by <strong>swell, wind, and tide.</strong> Thanks to metereologists, their tools (such as buoys), and the internet, we have access to current measurements of those factors and forecasts. However, knowledge of <strong>past measurements</strong> can also be very helpful, especially if those numbers affected your interaction with the ocean. For example, knowing how high the swell period was the day you got swept off the rocks at Waimea Bay will help you be more safe in the future.</p>
			<p><strong>Buoy Report</strong> allows you to record buoy data, tide data, a personal rating, and a photo for each time you interact with the ocean. Both a personal log and a location log (an aggregate of all reports from each location) are strengthened for us to study, learn from, and remember. Plus, submitting a report is super quick and works on computers, <strong>phones</strong>, and tablets. </p>
			<span class="border"></span>
			<p class="joey">Buoy Report is designed and developed by me, Joey Hodara, a San Francisco web designer from Maui, Hawaii. This project was initially designed so that I could gain a better understanding of the complexity of Ocean Beach, SF.</p>
			<span class="border"></span>
			<p class="dev">Please report any bugs to jhodara(at)gmail.com. If you would like to help with the development of Buoy Report, check out the repo <a target="_blank" href="https://github.com/jhodara/bouyreport"> on github</a>.</p>
		</div>
		<?	
	}

}

