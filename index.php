<!doctype html>
<html>
<head>
	<title>Mini Project 1</title>
</head>
<body>
<form action="submit.php" method="POST" enctype="multipart/form-data">
<div>
<label>Username*</label>
<input type="text" name="uname" required/>
</div>
<div>
<label>Email*</label>
<input type="text" name="email" required/>
</div>
<div>
<label>Phone</label>
<input type="text" name="phone"/>
</div>
<div>
<label>Image</label>
<input type="file" name="image" required/>
</div>
<input type="submit"/>
</form>
</body>
</html>
