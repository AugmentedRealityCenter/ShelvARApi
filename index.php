<html><head>
<title>ShelvAR.com</title>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<meta property="fb:page_id" content="319826574695363" />
<style type="text/css">
body {
	background-color: #c60c30;
	width: 960px;
	margin:auto;
}

#header {
	padding-top:10px;
	padding-bottom:10px;
}

#license {
	border: 2px solid #dddddd; 
	background-color: #ffffff;
	float: right;
	width:auto;
}

h1 {
	background-color: #dddddd;
	font-family: sans-serif;
}

table.parsed_call th, td {
	border: 2px solid #aaaaaa; 
	background-color: #ffffff;
}

table.parsed_call th {
	border: 2px solid #aaaaaa; 
	background-color: #dddddd;
}

table.parsed_call
{
	border-collapse:collapse;
}

div.spacer {
  clear: both;
  }

div.content {
	border: 2px solid #dddddd; 
	background-color: #ffffff; 
	width: 100%;
}

ul.topnav {
	list-style: none;
	margin: 0; padding: 0;
	width:inherit;
	height:auto;
	background: #fff;
}

ul.topnav a {
	text-decoration: none;
	color: #555555; background-color: #bbbbbb;
}

ul.topnav li {
	background: #fff;
	float: left;
	margin: 0;
	position: relative;
	border: 1px solid #999;
}

ul.topnav li a{
	background: #fff;
	display: block;
	padding: 5px;
}

ul.topnav li a:hover{
	background: #eee;
}

ul.topnav li a:active{
	color: #000000; background-color: #dddddd;
}

ul.topnav li ul.subnav {
	list-style: none;
	position: absolute;
	background: #fff;
	margin: 0; padding: 0;
	display: none;
	float: left;
	width: 170px;
	border: 1px solid #111;
}

ul.topnav li ul.subnav li{
	margin: 0; padding: 0;
	border-top: 1px solid #252525;
	border-bottom: 1px solid #444;
	clear: both;
	width: 168px;
}

html ul.topnav li ul.subnav li a {
	float: left;
	width: 158px;
	background: #fff
}

html ul.topnav li ul.subnav li a:hover { /*--Hover effect for subnav links--*/
	background: #eee;
}

html ul.topnav li ul.subnav li a:active { /*--Hover effect for subnav links--*/
	color: #000000; background-color: #dddddd; 
}

</style>

<script src="librariantagger/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="librariantagger/librariantagger.js" type="text/javascript"></script>

<script type="text/javascript">

$(document).ready(function()
{   
    createTimeline();
    $("ul.topnav li").hover(function() {
        //if there is a submenu, drop it down
        $(this).find("ul.subnav").slideDown('fast').show();
	}, function() {
		$(this).find("ul.subnav").slideUp('slow');
		/*$(this).parent().hover(function() {  
        }, function(){  
            $(this).parent().find("ul.subnav").slideUp('slow'); //When the mouse hovers out of the subnav, move it back up  
        });*/
	});  
	
	$(".view").hide(); //Hide all content
	$(".view:first").show(); //Show first tab content

	//On Click Event
	$("ul.topnav a").click(function() {	
		var activeTab = $(this).attr("href"); //get div tag id
		
		if(activeTab != "#") {
			$(".view").hide(); //hide tabs
			$(activeTab).fadeIn(); //fade in newly selected tab
		}

		return false;
	});
  
});  

</script>

<script src="http://static.simile.mit.edu/timeline/api-2.3.0/timeline-api.js?bundle=true" type="text/javascript"></script>
<script>SimileAjax.History.enabled = false;</script>
</head>
<body onload="BSonLoad();" onresize="BSonResize();">

<div id="header">

    <img src="ShelvARLogo_Big.png">
    
    <div id="license">
        <b>License info:</b><br />
        Your IP address is: <?php echo $_SERVER['REMOTE_ADDR']; ?><br />
        Your access to ShelvAR is provided by: <?php
          include_once "institutions.php";
          echo $instnames[$_GET['institution']]; 
        ?><br />
        ShelvARWeb version: <?php include 'commitnumber.txt';?><br/>
    </div>

</div>

<ul class="topnav">
  <li><a href="#">Tag generation tools</a>
  	<ul class="subnav">
        <li><a href="#tagmulti">Create Multiple Tags</a></li>
    </ul>
  </li>
  <li><a href="#">Reporting tools</a>
  	<ul class="subnav">
        <li><a href="#findbookdiv">Find Book</a></li>
        <li><a href="#booksleuthdiv">Book Sleuth</a></li>
    </ul>
  </li>
</ul>

<hr style="clear:both;height:0px;border:0;" /><br />

<div class="content">	
    
    <div class="view" id="tagmulti">
     <?php
      include_once 'librariantagger/multi.html';
    ?>
    </div>
    
    <div class="view" id="findbookdiv">
    <?php
      include_once 'reporttool/find_book_user.html';
    ?>
    </div>
    
    <div class="view" id="booksleuthdiv">
    <?php
      include_once 'reporttool/book_sleuth_index.html';
    ?>
    </div>
    

</div>

</body>
</html>
