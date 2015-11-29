<!doctype html>
<html>
<head>
	<title>Gallery</title>
</head>
<body>
<?php
require 'config.php';
$dbh = new PDO($DB['dsn'], $DB['username'], $DB['password']);

$query = 'select * from images_mp2';
$stmt = $dbh->prepare($query);
if($stmt->execute()){
	$records = $stmt->fetchAll();
	foreach($records as $value):
?>
	<img src="<?=$value['s3_raw_url']?>"/>
<?php
	endforeach;
}else{
	echo 'Failed to execute the query.';
}
?>
</body>
</html>
