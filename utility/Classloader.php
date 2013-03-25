<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';

class Classloader {

    static protected $classloader_map = array(

        "BaseModel" =>                  "/models/BaseModel.php",
        "BasePersistence" =>            "/persistence/BasePersistence.php",
        "Buoy" =>                       "/models/Buoy.php",
        "BuoyPersistence" =>            "/persistence/BuoyPersistence.php",
        "BuoyService" =>                "/services/BuoyService.php",
        "BuoyData" =>                   "/models/BuoyData.php",
        "BuoyDataPersistence" =>        "/persistence/BuoyDataPersistence.php",
        "BuoyDataService" =>            "/services/BuoyDataService.php",
        "Location" =>                   "/models/Location.php",
        "LocationPersistence" =>        "/persistence/LocationPersistence.php",
        "LocationService" =>            "/services/LocationService.php",
        "NOAABuoyPersistence" =>        "/persistence/NOAABuoyPersistence.php",
        "NOAATidePersistence" =>        "/persistence/NOAATidePersistence.php",
        "Persistence" =>                "/persistence/Persistence.php",
        "Report" =>                     "/models/Report.php",
        "ReportPersistence" =>          "/persistence/ReportPersistence.php",
        "ReportService" =>              "/services/ReportService.php",
        "Reporter" =>                   "/models/Reporter.php",
        "ReporterPersistence" =>        "/persistence/ReporterPersistence.php",
        "ReporterService" =>            "/services/ReporterService.php",
        "Sublocation" =>                "/models/Sublocation.php", //remove
        "TideData" =>                   "/models/TideData.php",
        "TideDataPersistence" =>        "/persistence/TideDataPersistence.php",
        "TideDataService" =>            "/services/TideDataService.php",
        "TideStation" =>                "/models/TideStation.php",
        "TideStationPersistence" =>     "/persistence/TideStationPersistence.php",
        "TideStationService" =>         "/services/TideStationService.php",
        "User" =>                       "/models/User.php", //remove


        //PAGES
        "AboutPage" =>                  "/views/pages/AboutPage.php",
        "AddBuoyPage" =>                "/views/pages/AddBuoyPage.php",
        "AddCrewPage" =>                "/views/pages/AddCrewPage.php",
        "AddLocationPage" =>            "/views/pages/AddLocationPage.php",
        "BuoyPage" =>                   "/views/pages/BuoyPage.php",
        "CrewPage" =>                   "/views/pages/CrewPage.php",
        "EditBuoyPage" =>               "/views/pages/EditBuoyPage.php",
        "EditLocationPage" =>           "/views/pages/EditLocationPage.php",
        "EditPostPage" =>               "/views/pages/EditPostPage.php",
        "EditProfilePage" =>            "/views/pages/EditProfilePage.php",
        "ErrorPage" =>                  "/views/pages/ErrorPage.php",
        "HomePage" =>                   "/views/pages/HomePage.php",
        "IntroPage" =>                  "/views/pages/IntroPage.php",
        "LocationDetailPage" =>         "/views/pages/LocationDetailPage.php",
        "LocationPage" =>               "/views/pages/LocationPage.php",
        "LoginPage" =>                  "/views/pages/LoginPage.php",
        "MobileImageProcessPage" =>     "/views/pages/MobileImageProcessPage.php",
        "Page" =>                       "/views/pages/Page.php",
        "ProfilePage" =>                "/views/pages/ProfilePage.php",
        "RegisterPage" =>               "/views/pages/RegisterPage.php",
        "ReporterPage" =>               "/views/pages/ReporterPage.php",
        "ReportFormPage" =>             "/views/pages/ReportFormPage.php",
        "SingleReportPage" =>           "/views/pages/SingleReportPage.php",

        //VIEWS
        "AddBuoyForm" =>                "/views/forms/AddBuoyForm.php",
        "AddTideStationForm" =>         "/views/forms/AddTideStationForm.php",
        "BuoyDataView" =>               "/views/report/BuoyDataView.php",
        "DeleteBuoyForm" =>             "/views/forms/DeleteBuoyForm.php",
        "EditAccountForm" =>            "/views/forms/EditAccountForm.php",
        "EditBuoyForm" =>               "/views/forms/EditBuoyForm.php",
        "EditReportForm" =>             "/views/forms/EditReportForm.php",
        "FilterForm" =>                 "/views/forms/FilterForm.php",
        "FilterNote" =>                 "/views/report/FilterNote.php",
        "Header" =>                     "/views/components/Header.php",
        "ItemList" =>                   "/views/components/ItemList.php",
        "LocationList" =>               "/views/components/LocationList.php",
        "LocationRemoveBuoysForm" =>    "/views/forms/LocationRemoveBuoysForm.php",
        "LoginForm" =>                  "/views/forms/LoginForm.php",
        "LogoutForm" =>                 "/views/forms/LogoutForm.php",
        "RegisterForm" =>               "/views/forms/RegisterForm.php",
        "ReportFeed" =>                 "/views/report/ReportFeed.php",
        "ReportForm" =>                 "/views/forms/ReportForm.php",
        "ReportFormFields" =>           "/views/forms/ReportFormFields.php",
        "SearchModule" =>               "/views/forms/SearchModule.php",
        "SingleReport" =>               "/views/report/SingleReport.php",
        "TideDataView" =>               "/views/report/TideDataView.php",        

        //UTILITY
        "BuoyDataViewUtils" =>          "/utility/report/BuoyDataViewUtils.php",
        "Classloader" =>                "/utility/Classloader.php",
        "JSMin" =>                      "/utility/JSMin.php",
        "Mobile_Detect" =>              "/utility/Mobile_Detect.php",
        "NOAAUtils" =>                  "/utility/report/NOAAUtils.php",
        "Path" =>                       "/utility/Path.php",
        "ReportOptions" =>              "/utility/report/ReportOptions.php",
        "ReportUtils" =>                "/utility/report/ReportUtils.php",
        "SimpleImage" =>                "/utility/SimpleImage.php",
        "Text" =>                       "/utility/Text.php",
        "TideDataViewUtils" =>          "/utility/report/TideDataViewUtils.php",
        "Utils" =>                      "/utility/Utils.php",

        //EXCEPTIONS
        "AddStationException" =>        "/utility/exceptions/AddStationException.php",
        "InternalException" =>          "/utility/exceptions/InternalException.php",
        "InvalidSubmissionException" => "/utility/exceptions/InvalidSubmissionException.php",

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