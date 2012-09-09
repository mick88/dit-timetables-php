<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>declancurran.me/timetables</title>
	<style type="text/css">
		body
		{
			background-color: #EEEEEE;
			color: #222222;
			font-family:Helvetica,Arial,'DejaVu Sans','Liberation Sans',Freesans,sans-serif;

			margin: 30px;
		}

		a, a:link, a:visited, a:active
		{
			color: #333333;
		}
		a:hover
		{
			color: #222222;
		}

		input[type="text"]
		{
			width: 180px;
			height: 14px;
		}

		img
		{
			border: #BBBBBB solid 1px;
		}

		#column1
		{
			float: left;
			width: 400px;
			line-height: 22px;
			padding-right: 30px;
			border-right: 1px solid #CCCCCC;
		}

		#column2
		{
			float: left;
			width: 400px;
			line-height: 22px;
			padding-left: 30px;
		}

		#filterbox
		{
			display: none;
			margin-bottom: 20px;
		}

		#urlbox
		{
			background-color: #FFFFFF;
			border: #BBBBBB 1px solid;
			min-height: 40px;
			padding: 10px;
			font-size: 0.9em;
			word-wrap: break-word;
		}

		.filters
		{
			position: absolute;
			left: 200px;
		}

	</style>
	<script type="text/javascript">
		function filterbox(link)
		{
			//alert(link.innerHTML);
			var box = document.getElementById("filterbox");
			
			if (box.style.display == "block")
			{
				box.style.display = "none";
				link.innerHTML = "[open]";
			}
			else if (box.style.display == "" || box.style.display == "none")
			{
				box.style.display = "block";
				link.innerHTML = "[close]";
			}
		}

		function generate()
		{
			var box = document.getElementById("urlbox");
			var inputs = document.getElementsByTagName("input");

			var url = "http://declancurran.me/timetables2/calendar.php?";

			for (var i = 0; i < inputs.length; i++)
			{
				if (inputs[i].type == "text" && inputs[i].value != "")
				{
					url = url + inputs[i].name + "=" + inputs[i].value + "&amp;";
				}
				else if (inputs[i].type == "radio" && inputs[i].checked)
				{
					url = url + "semester=" + inputs[i].value + "&amp;";
				}
			}

			// Get rid of last ampersand
			url = url.slice(0, -5);

			box.innerHTML = url;
		}
	</script>
</head>
<body>
	<div id="column1">
		<h1>Hello</h1>
		Let's face it, webtimetables is a heap of shit.<br />
		This is a solution to that.<br />
		<br />
		With this tool you can generate an iCal calendar, based on your DIT timetable. With that calendar, you can link it to Google Calendar, have it update automatically every few hours, and sync it to your phone.<br />
		<h2>To get your calendar:</h2>
		DIT course - Must include year, for example: DT228/2
		<input type="text" name="course" />
		<br />
		<br />
		Semester<br />
		<input type="radio" name="semester" value="1" checked="checked" /> 1 (Weeks 4-17)<br />
		<input type="radio" name="semester" value="2" /> 2 (Weeks 23-37)<br />
		<br />
		Wanna filter it? <a href="#" onclick="filterbox(this);">[open]</a><br />
		<br />
		<div id="filterbox">
			For example, if you're in a sub-group of your class, or if you just want to see labs.<br />
			<br />
			Class group name <input type="text" name="f_classgroup" class="filters" /><br />
			Class group code <input type="text" name="f_classgroupcode" class="filters" /><br />
			Sub-group name <input type="text" name="f_clsgrpsubgrp" class="filters" /><br />
			Sub-group code <input type="text" name="f_clsgrpsubgrpcode" class="filters" /><br />
			Weeks <input type="text" name="f_weeks" class="filters" /><br />
			Time <input type="text" name="f_time" class="filters" /><br />
			Lecturer <input type="text" name="f_lecturer" class="filters" /><br />
			Room <input type="text" name="f_siteroomcode" class="filters" /><br />
			Activity type <input type="text" name="f_activitytype" class="filters" /><br />
			Module <input type="text" name="f_module" class="filters" /><br />
		</div>

		<input type="button" value="Get calendar URL" onclick="generate();"/>
		<br />
		<br />
		<div id="urlbox"></div>
	</div>
	<div id="column2">
		<h1>How to use it</h1>
		To add it to Google Calendar:
		<img src="howtoadd.jpg" alt="" />
		<br />
		Or, you can just go to the URL and download it, if you want to use your own calendar software.
		<br />
		<br />
		Thanks for coming. This project is open source, public domain.<br />
		<br />
		<a href="https://gitorious.org/dit-timetables">Gitorious</a>
		<br />
		<br />
		P.S. If you share your Google Calendar with your class, it'll lessen the load on my server. Thanks.
	</div>
</body>
</html>