<?php

define("mysqlUserName", "");
define("mysqlPassword", "");
define("dbName", "");
define("path", ""); // /var/www/html/

$mysqlUserName = mysqlUserName;
$mysqlPassword = mysqlPassword;
$dbName = dbName;
$path = path;

include('Net/SFTP.php');
include('Crypt/RSA.php');

$ssh = new Net_SFTP(NET_SFTP_SERVER_NAME);
$key = new Crypt_RSA();

$key->loadKey(file_get_contents($ssh->privatePemKey));
if (!$ssh->login($ssh->userName, $key)) {
    exit('Login Failed');
}

$ssh->write("mysqldump -u $mysqlUserName -p'$mysqlPassword' $dbName|gzip > $path/$dbName.sql.gz \n");
if($ssh->read($ssh->userName . '@' . $ssh->ipAddress . ':~$')){
	$file = "../$dbName.sql.gz";
	downloadZip($file);
}else{
	echo "Not Working";
	exit();
}

function downloadZip($file){
    $file_name = basename($file);
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=$file_name");
    header("Content-Length: " . filesize($file));

    readfile($file);
    exit;
}

?>