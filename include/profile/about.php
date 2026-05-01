<?php ?>
<script type="text/javascript" src="js/city_country.js"></script>  
<script type="text/javascript">  
var ajax = new Array();  
function getCityList(sel) 
{ 
    var countryCode = sel.options[sel.selectedIndex].value; 
    document.getElementById('city').options.length = 0;    // Empty city select box 
    if(countryCode.length>0){ 
        var index = ajax.length; 
        ajax[index] = new sack(); 
         
        ajax[index].requestFile = 'getCities.php?countryCode='+countryCode;    // Specifying which file to get 
        ajax[index].onCompletion = function(){ createCities(index) };    // Specify function that will be executed after file has been found 
        ajax[index].runAJAX();        // Execute AJAX function 
    } 
}
function newCity(e)
{
	if (e.options[e.options.selectedIndex].value=='Other') {
	document.getElementById('newCity').innerHTML='<input type="text" size="35" name="newcity" id="newC">';
	document.getElementById('newC').focus();
	return true;}
	else {
	document.getElementById('newCity').innerHTML='';
	e.focus();}
}

function createCities(index) 
{ 
    var obj = document.getElementById('city'); 
    eval(ajax[index].response);    // Executing the response from Ajax as Javascript code     
} 
</script>
<?
$select_contry_city = "<select id=\"country\" name=\"country\" onchange=\"getCityList(this)\">"; 
$select_contry_city .= "<option value=\"0\" style=\"color: gray;\"".($CURUSER['country'] ? '' : ' selected').">Выбрать страну</option>"; 
$countries = mysql_query("SELECT `id`, `name`, `order` FROM `countries` ORDER BY `order` DESC, `name`") or die(mysql_error()); 
if(mysql_num_rows($countries) > 0){
while($lista = mysql_fetch_array($countries)) { 
$select_contry_city .= "<option value=".$lista['id']."" . ($CURUSER["country"] == $lista['id'] ? " selected" : "").">".$lista['name']."</option>\n"; 
} 
}
$select_contry_city .= "</select>"; 

$select_contry_city .= "&nbsp;<select id=\"city\" name=\"city\" onChange=\"newCity(this);\">";
$select_contry_city .= "<option value=\"0\" style=\"color: gray;\"".($CURUSER['city'] ? '' : ' selected').">Выберите город</option>";
$select_contry_city .= "<option value=\"Other\">Другой...</option>";

$cities = mysql_query("SELECT ci.id, ci.name FROM cities ci INNER JOIN countries co ON ci.country_id = co.ID WHERE co.ID = ".$CURUSER['country']." ORDER BY ci.name") or die;  
if(mysql_num_rows($cities) > 0){ 
while($lista = mysql_fetch_array($cities)) { 
$select_contry_city .= "<option value=".$lista['id']."" . ($CURUSER['city'] == $lista['id'] ? " selected" : "") . ">".$lista['name']."</option>\n"; 
} 
} 
$select_contry_city .= "</select>";
$select_contry_city .= "<span id=\"newCity\"></span>";
tr("Страна/Город", "".$select_contry_city."",1);
/*if(!empty($CURUSER['avatar']))
$avatar='<img src="'.$DEFAULTBASEURL.'/avatars/'.$CURUSER['avatar'].'" alt="Автатар" title="Аватар"> <img src="'.$DEFAULTBASEURL.'/avatars/small/'.$CURUSER['avatar'].'" alt="Уменьшенная версия аватара" title="Уменьшенная версия аватара"><br />';
else $avatar='';
tr("".$tracker_lang['my_avatar_url']."", "".$avatar."<b><span style=\"cursor:pointer;\" title=\"Загрузить аватар\" onmouseover=\"this.style.color='red';\" onmouseout=\"this.style.color='';\" onClick=\"javascript:window.open('uploadavatar.php', '', 'width=500, height=380, toolbar=no, resizable=no, status=no, scrollbars=yes');\">Загрузить аватар</span></b>",1);
*/
tr($tracker_lang['my_gender'],
"<input type=radio name=gender" . ($CURUSER["gender"] == "1" ? " checked" : "") . " value=1>".$tracker_lang['my_gender_male']."
<input type=radio name=gender" .  ($CURUSER["gender"] == "2" ? " checked" : "") . " value=2>".$tracker_lang['my_gender_female']
,1);

///////////////// BIRTHDAY MOD /////////////////////
$birthday = $CURUSER["birthday"];
$birthday = date("Y-m-d", strtotime($birthday));
list($year1, $month1, $day1) = split('-', $birthday);
if ($CURUSER[birthday] == "0000-00-00") {
        $year .= "<select name=year><option value=\"0\">".$tracker_lang['my_year']."</option>\n";
        $i = "1920";
		$y = (date('Y',time())-13);
        while($i <= $y) {
                $year .= "<option value=" .$y. ">".$y."</option>\n";
                $y--;
        }
        $year .= "</select>\n";
        $birthmonths = array(
        "01" => $tracker_lang['my_months_january'],
        "02" => $tracker_lang['my_months_february'],
        "03" => $tracker_lang['my_months_march'],
        "04" => $tracker_lang['my_months_april'],
        "05" => $tracker_lang['my_months_may'],
        "06" => $tracker_lang['my_months_june'],
        "07" => $tracker_lang['my_months_jule'],
        "08" => $tracker_lang['my_months_august'],
        "09" => $tracker_lang['my_months_september'],
        "10" => $tracker_lang['my_months_october'],
        "11" => $tracker_lang['my_months_november'],
        "12" => $tracker_lang['my_months_december'],
        );
        $month = "<select name=\"month\"><option value=\"0\">".$tracker_lang['my_month']."</option>\n";
        foreach ($birthmonths as $month_no => $show_month)
        {
                $month .= "<option value=$month_no>$show_month</option>\n";
        }
        $month .= "</select>\n";
        $day .= "<select name=day><option value=\"0\">".$tracker_lang['my_day']."</option>\n";
        $i = 1;
        while ($i <= 31) {
                if($i < 10) {
                        $day .= "<option value=0".$i. ">0".$i."</option>\n";
                } else {
                        $day .= "<option value=".$i.">".$i."</option>\n";
                }
                $i++;
        }
        $day .="</select>\n";
        tr($tracker_lang['my_birthdate'], $year . $month . $day ,1);
}

/* Думаю, это какая-то фигня не нужная...
if($CURUSER[birthday] != "0000-00-00") {
        tr($tracker_lang['my_birthdate'],"<b><input type=hidden name=year value=$year1>$year1<input type=hidden name=month value=$month1>.$month1<input type=hidden name=day value=$day1>.$day1</b>",1);
}*/

tr($tracker_lang['my_info'], "<textarea name=info cols=50 rows=4>" . $CURUSER["info"] . "</textarea><br />Показывается на вашей публичной странице. Может содержать <a href=tags.php target=_new>BB коды</a>.", 1);
tr("Любимые фильмы", "<textarea name=lovemovie cols=50 rows=4>" . $CURUSER["lovemovies"] . "</textarea>", 1);

?>