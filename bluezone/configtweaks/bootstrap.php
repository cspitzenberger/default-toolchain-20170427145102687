<?php
# Copyright IBM 2017
# Filename: bootstrap.php
# Purpose: Populate global environment vars with Bluemix runtime values

$_ENV["SQLDB"] = NULL;
$_ENV["SQLHOST"] = NULL;
$_ENV["SQLPORT"] = NULL;
$_ENV["SQLUSER"] = NULL;
$_ENV["SQLPASSWORD"] = NULL;

$application = getenv("VCAP_APPLICATION");
$application_json = json_decode($application,true);
if (isset($application_json["application_uris"])) {
  $_ENV["APPURIS"] = $application_json["application_uris"];
}

$services = getenv("VCAP_SERVICES");
$services_json = json_decode($services,true);
if (isset($services_json)) {
    if (isset($services_json["mysql-5.5"][0]["credentials"])) {
        $mysql_config = $services_json["mysql-5.5"][0]["credentials"];
        $_ENV["SQLDB"] = $mysql_config["name"];
        $_ENV["SQLHOST"] = $mysql_config["host"];
        $_ENV["SQLPORT"] = $mysql_config["port"];
        $_ENV["SQLUSER"] = $mysql_config["user"];
        $_ENV["SQLPASSWORD"] = $mysql_config["password"];
    }

    if (isset($services_json["cleardb"][0]["credentials"])) {
        $mysql_config = $services_json["cleardb"][0]["credentials"];
        $_ENV["SQLDB"] = $mysql_config["name"];
        $_ENV["SQLHOST"] = $mysql_config["hostname"];
        $_ENV["SQLPORT"] = $mysql_config["port"];
        $_ENV["SQLUSER"] = $mysql_config["username"];
        $_ENV["SQLPASSWORD"] = $mysql_config["password"];
    }
}
?>