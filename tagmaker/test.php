<html>
<body>
<?php 
include 'base64.php';

//Basic tests.
$test1 = bin2base64('101010111100111111001000111110');
$test2 = base642bin('GY_8-'); //101010 111100 111111 001000 111110

echo $test1 . "<BR>";
echo $test2;

?>
</body>
</html>