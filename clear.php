<?php

require "include/bittorrent.php";
dbconn();

stdhead();
?>

<link rel="stylesheet" type="text/css" href="ajaxtabs/ajaxtabs.css" />

<script type="text/javascript" src="ajaxtabs/ajaxtabs.js"></script>

</head>

<body>

<h3><a href="http://www.dynamicdrive.com/dynamicindex17/ajaxtabscontent/">Ajax Tabs Content script Instructions</a></h3>

<h3>Demo #1- Basic implementation</h3>

<p>
- First tab shows default, inline content directly added inside container DIV<br />
- 2nd and 3rd tabs show external pages fetched via <b>Ajax</b><br />
- 4th tab shows an external page fetched via <b>IFRAME</b><br />
</p>

<ul id="countrytabs" class="shadetabs">
<li><a href="#" rel="#default" class="selected">Tab 1</a></li>
<li><a href="#" rel="#two">Tab 2</a></li>
<li><a href="external3.htm" rel="countrycontainer">Tab 3</a></li>
<li><a href="external4.htm" rel="#iframe">Tab 4</a></li>
<li><a href="http://www.dynamicdrive.com">Dynamic Drive</a></li>
</ul>
<div id="countrydivcontainer" style="border:1px solid gray; width:450px; margin-bottom: 1em; padding: 10px">
<p>This is some default tab content, embedded directly inside this space and not via Ajax. It can be shown when no tabs are automatically selected, or associated with a certain tab, in this case, the first tab.</p>
</div>
<div id="#two" style="border:1px solid gray; width:450px; margin-bottom: 1em; padding: 10px">
<p>This is some default tab content, embedded directly inside this space and not via Ajax. It can be shown when no tabs are automatically selected, or associated with a certain tab, in this case, the first tab.</p>
</div>

<script type="text/javascript">

var countries=new ddajaxtabs("countrytabs", "countrydivcontainer")
countries.setpersist(true)
countries.setselectedClassTarget("link") //"link" or "linkparent"
countries.init()

</script>
<?php
stdfoot();

?>