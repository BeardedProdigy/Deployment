#!/usr/bin/php

<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('login.php.inc');
#include <unistd.h>
	
function doLogin($username,$password)
{
    // lookup username in databas
    // check password
	$login = new loginDB();
	return $login->validateLogin($username,$password);
	//return false if not valid
	
}

function BEsend($VersionName,$host,$ip)
{
   $con =  mysqli_connect("localhost", "waduhek", "password", "zipversions");
   
   echo "connected";
	    
   mysqli_select_db($con, 'zipversions');
   
   $s = "select * from versions where VersionName = '$VersionName'";
   ($duplicate = mysqli_query($con, $s)) or die (mysqli_error());
   $num = mysqli_num_rows($duplicate);
   if($num ==  1)
   {
	  return "Duplicate version value entered..";
   }
   else
   {
	//doesn't exist
	$s = "Insert into versions(VersionName,Status) values('$VersionName','null')"; 
	($t = mysqli_query ($con,$s)) or die(mysqli_error());

	//The script below is meant to unzip the tar file.
	shell_exec("scp /home/waduhek2/readytogo/$VersionName.tar.gz $host@$ip:/home/$host/new/zip");
	shell_exec("ssh $host@$ip php /home/$host/new/zip/install.php");
	
	return "Package Installed Successfully!";	
   } 
}

function Rollback($VersionName,$host,$ip)
{
   $con =  mysqli_connect("localhost", "waduhek", "password", "zipversions");

   echo "connected";

   $s = "select * from versions WHERE VersionName LIKE '%$VersionName%' AND Status = 'good' ORDER BY VersionName DESC LIMIT 1";
        $t = mysqli_query($con, $s) or die (mysqli_error());
	while ($row = $t->fetch_assoc()) 
	{
	    $x = $row['VersionName'];
	    shell_exec("scp /home/waduhek2/readytogo/$x.tar.gz $host@$ip:/home/$host/new/zip/");
	    shell_exec("ssh $host@$ip php /home/$host/new/zip/install.php");
	}
	return "Package Installed Successfully!";
}

function Status($VersionName, $Status)
{
   $con =  mysqli_connect("localhost", "waduhek", "password", "zipversions");

   echo "connected";

   mysqli_select_db($con, 'zipversions');

   $s = "UPDATE versions SET Status = '$Status' where VersionName = '$VersionName'";
        mysqli_query($con, $s) or die (mysqli_error());
	
	return "Status Successfully Updated!";
}

function Version($VersionName)
{
   $con =  mysqli_connect("localhost", "waduhek", "password", "zipversions");

   echo "connected";

   mysqli_select_db($con, 'zipversions');

   $s = "select * from versions WHERE VersionName LIKE '%$VersionName%' AND Status = 'good' ORDER BY VersionName DESC LIMIT 1";
        $t = mysqli_query($con, $s) or die (mysqli_error());
	while ($row = $t->fetch_assoc())
	{
	    $x = $row['VersionName'];
	    return "Current Version: " .$x;
	}
}

function FEsend($VersionName,$host,$ip)
{
  $con =  mysqli_connect("localhost", "waduhek", "password", "zipversions");

   echo "connected";

   mysqli_select_db($con, 'zipversions');

   $s = "select * from versions where VersionName = '$VersionName'";
   ($duplicate = mysqli_query($con, $s)) or die (mysqli_error());
   $num = mysqli_num_rows($duplicate);
   if($num ==  1)
   {
          return "Duplicate version value entered.....";
   }
   else
   {
        //doesn't exist
        $s = "Insert into versions(VersionName,Status) values ('$VersionName','null')";
        ($t = mysqli_query ($con,$s)) or die(mysqli_error());
	
	//shell_exec("scp /home/waduhek2/readytogo/$VersionName.tar.gz $host@$ip:/home/$host/var/FE/");
	shell_exec("scp /home/waduhek2/readytogo/$VersionName.tar.gz $host@$ip:/var/www/");
	shell_exec("ssh $host@$ip php /var/www/install.php");
	   return "Package Installed Successfully!";	
   } 
}

function FErollback($VersionName,$host,$ip){

   $con =  mysqli_connect("localhost", "waduhek", "password", "zipversions");

   echo "connected";

   $s = "select * from versions WHERE VersionName LIKE '%$VersionName%' AND Status = 'good' ORDER BY VersionName DESC LIMIT 1";
        $t = mysqli_query($con, $s) or die (mysqli_error());
        while ($row = $t->fetch_assoc())
        {
            $x = $row['VersionName'];
            shell_exec("scp /home/waduhek2/readytogo/$x.tar.gz $host@$ip:/home/$host/new/zip/");
            shell_exec("ssh $host@$ip php /home/$host/var/www/installer.php");
        }
        return "Package Installed Successfully!";

}

function FEstatus($VersionName,$Status)
{
   $con =  mysqli_connect("localhost", "waduhek", "password", "zipversions");

   echo "connected";

   mysqli_select_db($con, 'zipversions');

   $s = "UPDATE versions SET Status = '$Status' where VersionName = '$VersionName'";
        mysqli_query($con, $s) or die (mysqli_error());

        return "Status Successfully Updated!";

}

function FEversion($VersionName)
{
   $con =  mysqli_connect("localhost", "waduhek", "password", "zipversions");

   echo "connected";

   mysqli_select_db($con, 'zipversions');

   $s = "select * from versions WHERE VersionName LIKE '%$VersionName%'ORDER BY VersionName DESC LIMIT 1";
        $t = mysqli_query($con, $s) or die (mysqli_error());
        while ($row = $t->fetch_assoc())
        {
            $x = $row['VersionName'];
            return "Current Version: " .$x;
        }

}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
 
  if(!isset($request['type']))
  {
    return "Not supported";
  }
  
  switch ($request['type']){
	  case "send":
	       return BEsend($request['name'],$request['host'],$request['ip']);
	  case "rollback":
 	       return Rollback($request['name'],$request['host'],$request['ip']);
  	  case "status":
   	       return Status($request['name'], $request['status']);
   	  case "versionchecker":
               return Version ($request['name']);
	  case "FEsend":
	     return FEsend($request['name'], $request['host'], $request['ip']);
	  case "FErollback":
		return FErollback($request['name']);
	  case "FEstatus":
		return FEstatus($request['name']);
	  case "FEversionchecker":
		return FEversion ($request['name']);
	     
  }

  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}


$server = new rabbitMQServer("testRabbitMQ.ini","DeploymentHost");

$server->process_requests('requestProcessor');

echo "testRabbitMQServer END".PHP_EOL;

exit();
?>
