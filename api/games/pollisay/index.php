<?php
	require_once "/home3/deamon/public_html/lib.php";
	lib_database();
	lib_perms();

if(is_numeric($_GET['polly'])){

echo json_encode(arraySQL("select * from Game_PolliSay_quotes where polly = '$_GET[polly]' order by RAND()"));

}else if(isset($_GET['list'])){
	
echo json_encode(arraySQL("select * from Game_PolliSay_polly"));
}else{
	echo "Either request a '?list' or a politcan's quotes '?polly={id}'";
}
?>