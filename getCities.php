<?
require_once("include/bittorrent.php");
dbconn(false);

header('Content-Type: text/html; charset=windows-1251');
header("Cache-control: no-store");
header("Pragma: no-cache");

if(isset($_GET['countryCode'])) {
$codecity=$_GET['countryCode']+0;


$res = mysql_query("SELECT name, ID FROM cities WHERE country_id = ".$codecity) or die(mysql_error());
echo "obj.options[obj.options.length] = new Option('Выберите город','0',true,true);\n";
echo "obj.options[(obj.options.length-1)].style.color='gray;';\n";
echo "obj.options[obj.options.length] = new Option('Другой...','Other');\n";
while($row = mysql_fetch_array($res)){
echo "obj.options[obj.options.length] = new Option('".$row["name"]."','".$row["ID"]."');\n"; 
}
}
?>