<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';

class Classloader {

    static protected $classloader_map = array(

        "BaseModel" =>                  "/platform/models/BaseModel.php",
        "Buoy" =>                       "/platform/models/Buoy.php",
        "BuoyPersistence" =>            "/platform/persistence/BuoyPersistence.php",
        "BuoyService" =>                "/platform/services/BuoyService.php",
        "BuoyReport" =>                 "/platform/models/BuoyReport.php",
        "BuoyReportPersistence" =>      "/platform/persistence/BuoyReportPersistence.php",
        "BuoyReportService" =>          "/platform/services/BuoyReportService.php",
        "Location" =>                   "/platform/models/Location.php",
        "LocationPersistence" =>        "/platform/persistence/LocationPersistence.php",
        "LocationService" =>            "/platform/services/LocationService.php",
        "NOAABuoyReportPersistence" =>  "/platform/persistence/NOAABuoyReportPersistence.php",
        "NOAATidePersistence" =>        "/platform/persistence/NOAATidePersistence.php",
        "ModelCache" =>                 "/platform/persistence/ModelCache.php",
        "Persistence" =>                "/platform/persistence/Persistence.php",
        "Report" =>                     "/platform/models/Report.php",
        "ReportPersistence" =>          "/platform/persistence/ReportPersistence.php",
        "ReportService" =>              "/platform/services/ReportService.php",
        "Reporter" =>                   "/platform/models/Reporter.php",
        "ReporterPersistence" =>        "/platform/persistence/ReporterPersistence.php",
        "ReporterService" =>            "/platform/services/ReporterService.php",
        "StatusMessageService" =>       "/platform/services/StatusMessageService.php",
        "Sublocation" =>                "/platform/models/Sublocation.php", //remove
        "TideReport" =>                 "/platform/models/TideReport.php",
        "TideReportPersistence" =>      "/platform/persistence/TideReportPersistence.php",
        "TideReportService" =>          "/platform/services/TideReportService.php",
        "TideStation" =>                "/platform/models/TideStation.php",
        "TideStationPersistence" =>     "/platform/persistence/TideStationPersistence.php",
        "TideStationService" =>         "/platform/services/TideStationService.php",
        "User" =>                       "/platform/models/User.php",
        "UserService" =>                "/platform/services/UserService.php",


        //PAGES
        "AboutPage" =>                  "/view/templates/pages/AboutPage.php",
        "AddBuoyPage" =>                "/view/templates/pages/AddBuoyPage.php",
        "AddCrewPage" =>                "/view/templates/pages/AddCrewPage.php",
        "AddLocationPage" =>            "/view/templates/pages/AddLocationPage.php",
        "BuoyPage" =>                   "/view/templates/pages/BuoyPage.php",
        "BuoyDetailPage" =>             "/view/templates/pages/BuoyDetailPage.php",
        "CrewPage" =>                   "/view/templates/pages/CrewPage.php",
        "EditBuoyPage" =>               "/view/templates/pages/EditBuoyPage.php",
        "EditLocationPage" =>           "/view/templates/pages/EditLocationPage.php",
        "EditReportPage" =>             "/view/templates/pages/EditReportPage.php",
        "EditProfilePage" =>            "/view/templates/pages/EditProfilePage.php",
        "ErrorPage" =>                  "/view/templates/pages/ErrorPage.php",
        "ReportsPage" =>                "/view/templates/pages/ReportsPage.php",
        "IntroPage" =>                  "/view/templates/pages/IntroPage.php",
        "LocationDetailPage" =>         "/view/templates/pages/LocationDetailPage.php",
        "LocationPage" =>               "/view/templates/pages/LocationPage.php",
        "LoginPage" =>                  "/view/templates/pages/LoginPage.php",
        "Page" =>                       "/view/templates/pages/Page.php",
        "ProfilePage" =>                "/view/templates/pages/ProfilePage.php",
        "RegisterPage" =>               "/view/templates/pages/RegisterPage.php",
        "ReporterPage" =>               "/view/templates/pages/ReporterPage.php",
        "ReportFormPage" =>             "/view/templates/pages/ReportFormPage.php",
        "SingleReportPage" =>           "/view/templates/pages/SingleReportPage.php",

        //VIEWS
        "AddBuoyForm" =>                "/view/templates/forms/AddBuoyForm.php",
        "AddTideStationForm" =>         "/view/templates/forms/AddTideStationForm.php",
        "BuoyReportView" =>             "/view/templates/report/BuoyReportView.php",
        "DeleteBuoyForm" =>             "/view/templates/forms/DeleteBuoyForm.php",
        "EditAccountForm" =>            "/view/templates/forms/EditAccountForm.php",
        "EditBuoyForm" =>               "/view/templates/forms/EditBuoyForm.php",
        "EditReportForm" =>             "/view/templates/forms/EditReportForm.php",
        "FilterForm" =>                 "/view/templates/forms/FilterForm.php",
        "FilterNote" =>                 "/view/templates/report/FilterNote.php",
        "FormFields" =>                 "/view/templates/forms/FormFields.php",
        "Header" =>                     "/view/templates/components/Header.php",
        "Image" =>                      "/view/templates/components/Image.php",
        "ItemList" =>                   "/view/templates/components/ItemList.php",
        "LocationList" =>               "/view/templates/components/LocationList.php",
        "LocationRemoveBuoysForm" =>    "/view/templates/forms/LocationRemoveBuoysForm.php",
        "LoginForm" =>                  "/view/templates/forms/LoginForm.php",
        "LogoutForm" =>                 "/view/templates/forms/LogoutForm.php",
        "RegisterForm" =>               "/view/templates/forms/RegisterForm.php",
        "ReportFeed" =>                 "/view/templates/report/ReportFeed.php",
        "ReportForm" =>                 "/view/templates/forms/ReportForm.php",
        "SearchModule" =>               "/view/templates/forms/SearchModule.php",
        "SingleReport" =>               "/view/templates/report/SingleReport.php",
        "TideReportView" =>             "/view/templates/report/TideReportView.php",        

        //UTILITY
        "BuoyReportViewUtils" =>        "/utility/report/BuoyReportViewUtils.php",
        "Classloader" =>                "/utility/Classloader.php",
        "JSMin" =>                      "/utility/JSMin.php",
        "Mobile_Detect" =>              "/utility/Mobile_Detect.php",
        "NOAAUtils" =>                  "/utility/report/NOAAUtils.php",
        "Path" =>                       "/utility/Path.php",
        "ReportOptions" =>              "/utility/report/ReportOptions.php",
        "ReportUtils" =>                "/utility/report/ReportUtils.php",
        "SimpleImage" =>                "/utility/SimpleImage.php",
        "Text" =>                       "/utility/Text.php",
        "TideReportViewUtils" =>        "/utility/report/TideReportViewUtils.php",
        "Utils" =>                      "/utility/Utils.php",

        //EXCEPTIONS
        "AddStationException" =>        "/utility/exceptions/AddStationException.php",
        "InvalidSubmissionException" => "/utility/exceptions/InvalidSubmissionException.php"

    );

    /**
     * dynamically load a class
     * @param type $classname
     * @return type 
     */
    static function loadClass($classname) {
        $classloader_map = self::$classloader_map;
    
        if(array_key_exists($classname, $classloader_map)) {
            $resource = $classloader_map[$classname];
            // include the class being requested
            //error_log("Found a file that exists for this class $classname, loading $resource");
            include_once( $_SERVER['DOCUMENT_ROOT'] . $resource);
        } else {
            error_log("Did not find a file to load for class $classname");
        }
    }
}

spl_autoload_register( 'Classloader::loadClass' );
if( function_exists('__autoload') ) spl_autoload_register( '__autoload' ); // this basically says, if the old-school autoloader existed, make sure we use it as well as our custom autoloader
?>