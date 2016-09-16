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
			<p><strong>Buoy Report</strong> lets you quickly record buoy observations for later study.</p>
			<p>Record buoy and tide data after you surf with a click of a button. You can also include a photo, surf height estimation, and more to create an in-depth personal surf log. Also, the location logs can be a very helpful resource when predicting surfability of certain hard-to-please breaks. Submitting a report is quick and works on computers, <strong>phones</strong>, and tablets.</p>
			<span class="border"></span>
			<p class="joey">Buoy Report was designed and developed by me, Joey Hodara, a San Francisco web designer from Maui, Hawai'i. This project was initially designed so that I could gain a better understanding of the complexity of Ocean Beach, SF.</p>
			<span class="border"></span>
			<p class="dev">Please report any bugs to jhodara(at)gmail.com. If you would like to help with the development of Buoy Report, check out the repo <a target="_blank" href="https://github.com/epjoey/Buoy-Report"> on github</a>.</p>
		</div>
		<?	
	}

}

