<?php
error_reporting(1);
ini_set('display_errors', 1);
require 'config.php';
require 'vendor/autoload.php';

use Aws\Rds\RdsClient;

$client = RdsClient::factory(array(
	'region' => 'us-east-1',
	'version' => 'latest'
));

try{
	$dbh = new PDO($DB['dsn'], $DB['username'], $DB['password']);

	$query = 'CREATE TABLE IF NOT EXISTS images_mp2
	(
	id int primary key auto_increment,
	uname varchar(20),
	email varchar(200),
	phone varchar(20),
	s3_raw_url varchar(256),
	s3_finished_url varchar(256),
	filename varchar(256),
	status  tinyint(3),
	timestamp datetime
	)';

//	$query = 'alter table images add column filename varchar(256) after s3_finished_url';
	$query = 'delete from images_mp2';

	var_dump($dbh->exec($query));

}catch(PDOException $e){
	echo $e->getMessage();
}

$dbh = null;
