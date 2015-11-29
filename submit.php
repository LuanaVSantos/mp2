<?php
error_reporting(1);
ini_set("display_errors", 1);

require 'config.php';
require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Sns\SnsClient;

$client = S3Client::factory(array(
  'version' => 'latest',
  'region' => 'us-west-2',
  'credentials' => array(
	'key'=>'AKIAJPKY6CJYCQADTGGA',
	'secret'=>'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'
  )
));

$bucket_name = 'lukabucket';
//create a bucket if it doesn't exist
if(!$client->doesBucketExist('lukabucket')){
	$client->createBucket(array(
		'Bucket' => $bucket_name
	));
}

$key = 'luka_' . uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

$result = $client->putObject([
	'ACL' => 'public-read',
	'Bucket' => $bucket_name,
	'ContentType' => $_FILES['image']['type'],
	'Key' => $key,
	'SourceFile' => $_FILES['image']['tmp_name']
]);

//poll the object until it is accessible
$client->waitUntil('ObjectExists', array(
	'Bucket' => $bucket_name,
	'Key' => $key
));

$s3_raw_url =  $result['ObjectURL'];

$uname = $_POST['uname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$s3_finished_url = 'none';
$filename = $key;
$status = 1;

$dbh = new PDO($DB['dsn'], $DB['username'], $DB['password']);

$query = 'insert into images_mp2(uname, email, phone, s3_raw_url, s3_finished_url, filename, status,  timestamp) values(?, ?, ?, ?, ?, ?, ?, now())';

$stmt = $dbh->prepare($query);
$stmt->bindParam(1, $uname);
$stmt->bindParam(2, $email);
$stmt->bindParam(3, $phone);
$stmt->bindParam(4, $s3_raw_url);
$stmt->bindParam(5, $s3_finished_url);
$stmt->bindParam(6, $filename);
$stmt->bindParam(7, $status);

if($stmt->execute()){
	echo 'Added';
	//notify user of successful upload and display
	$snsClient = SnsClient::factory([
		'version' => 'latest',
		'region' => 'us-east-1',
		'credentials' =>[
			'key' => 'AKIAJPKY6CJYCQADTGGA',
			'secret' => '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'
		]
	]);

	//create a SNS topic
	$snsTopicResult = $snsClient->createTopic([
		'Name' => 'Image-Upload-and-Display-Success'
	]);
	$snsTopicArn = $snsTopicResult['TopicArn'];

	$snsTopicResult = $snsClient->setTopicAttributes([
		'AttributeName' => 'DisplayName',
		'AttributeValue' => 'MP2-SNS-Topic',
		'TopicArn' => $snsTopicArn
	]);

	echo $email;
	$snsTopicResult = $snsClient->subscribe([
		'TopicArn' => $snsTopicArn,
		'Protocol' => 'email',
		'Endpoint' => $email
	]);

	$snsTopicResult = $snsClient->publish([
		'TopicArn' => $snsTopicArn,
		'Message' => 'Your image uploaded successfully.'
	]);
	header('Location: gallery.php');
}else{
	echo 'failed';
}

$dbh = null;

