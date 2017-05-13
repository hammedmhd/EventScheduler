<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8'>
	<meta http-equiv='X-UA-Compatible' content='IE=edge'>
	<meta name='viewport' content='width=device-width, inital-scale=1.0'>
	<link rel='stylesheet' href='css/font-awesome/css/font-awesome.css'/>
	<link rel='stylesheet' href='css/bootstrap.css'>
	<!--<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>-->
	<link rel='stylesheet' href='fullcalendar/fullcalendar.css'/>
	<link rel='stylesheet' href='css/jquery-ui.min.css'>
	<script src='js/jquery-3.1.1.js'></script>
	<script src='js/bootstrap.js'></script>
	<!--<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
	<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>-->
	<script src='js/jquery-ui.min.js'></script>
	<script src='fullcalendar/lib/moment.min.js'></script>
	<script src='fullcalendar/fullcalendar.js'></script>
    <title>Basmah</title>
    <style>
    	::-webkit-scrollbar {
		      width: 8px;
		}
		::-webkit-scrollbar-track {
		      background-color: #2d2d2d;
		} /* the new scrollbar will have a flat appearance with the set background color */
		 
		::-webkit-scrollbar-thumb {
		      background-color: #eb81f9;
		} /* this will style the thumb, ignoring the track */
		 
		::-webkit-scrollbar-button {
		      background-color: grey;
		} /* optionally, you can style the top and the bottom buttons (left and right for horizontal bars) */
		::-webkit-scrollbar-corner {
		      background-color: rgba(255,255,255,0.8);
		}/* if both the vertical and the horizontal bars appear, then perhaps the right bottom corner also needs to be styled */
    	#notify{
    		position: fixed;
    		top:20px;
    		left:20px;
    		display:none;
    		z-index: 1;
    		font-weight: bold;
    		background-color: #eb81f9;
    		color:white;
    		border: none;
    	}
    	.navbar{
			margin-bottom:0px;
			//background-color:skyblue;
		}
		#clientlistheader{
			color:#2d2d2d;
		}
		#newclient{
			/*max-width:50%;*/
			margin: 0 auto 20px auto;
			display:none;
		}
		.loaderbox{
			position: fixed;
			z-index: 1;
			left:20px;
			top:20px;
		}
		#expenseload{
			display: none;
		}
		#calendar{
			margin-top: 30px;
		}
		#clientadd{
			font-size:1.0em;
			float:left;
			transform: translate(10px,0);
			cursor: pointer;
		}
		.external-events{
			padding:10px 10px 10px 10px;
			background-color: #a5cae7;
			border-radius:10px;
			margin-top:30px;
			margin-bottom:20px;
			min-width: 250px;
		}
		.expense-list{
			background-color: #a5cae7;
			color:#2d2d2d;
			border-radius:10px;
			padding:10px 10px 10px 10px;
			margin-bottom:20px;
			min-width:250px;
		}
		.expense-list .table.table-striped{
			font-weight:bold;
		}
		.payment-toggle{
			background-color: #a5cae7;
			border-radius:10px;
			padding:10px 10px 0px 10px;
			margin-bottom: 20px;
			min-width: 250px;
		}
		#togglepayment{
			color:#2d2d2d;
		}
		.payment-toggle .table.table-condensed thead, .payment-toggle .table.table-condensed tbody{
			color:#2d2d2d;
			text-overflow: ellipsis;
			overflow: hidden;
		}
		.payment-toggle .table.table-condensed tbody{
			text-align: center;
		}
		.expense-list .table.table-striped tr:nth-child(even){
			background-color: #bdccca;
			text-overflow: ellipsis;
			overflow: hidden;
		}
		.expense-list .table.table-striped tr:nth-child(odd){
			background-color: #deedeb;
			text-overflow: ellipsis;
			overflow: hidden;
		}
		#expenseheader{
			color:#2d2d2d;
		}
		#saveme{
			margin-top:0px;
			margin-bottom: 5px;
			font-weight: bold;
			//background-color: #e800ff;
		}
		.fc-event{
			cursor: pointer;
			margin-left: auto;
			margin-right: auto;
			border:none;
			font-weight: bold;
			text-overflow: ellipsis;
			overflow: hidden;
		}
		#refreshexpense{
			cursor: pointer;
			float:left;
			transform: translate(10px,0);
		}
		#deleteclient{
			background-color:red;
			border-radius:20px;
			float:right;
			transform: translate(0,1px);
			display:none;
			color:white;
			border: none;
		}
		#calendar {
			background-color: white;
			//margin: 10px auto;
			//padding: 0;
			//font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
			//font-size: 14px;
			//max-width: 900px;
		}
		.switch{
			position: relative;
			display: inline-block;
			width: 55px;
			height: 22px;
		}
		.switch input{display: none;}
		.slider{
			position: absolute;
			cursor: pointer;
			top:0;
			left:0;
			right:0;
			bottom:0;
			background-color: grey;
			transition: .4s;
			border-radius:20px; 
		}
		.slider:before{
			position: absolute;
			content: "";
			height: 14px;
			width:14px;
			left:4px;
			bottom:4px;
			background-color: white;
			transition: .4s;
			border-radius:50%;
		}
		input:checked + .slider{
			background-color: limegreen;
		}
		input:focus + .slider {
			box-shadow: 0 0 1px #2196F3;
		}
		input:checked + .slider:before {
		  -webkit-transform: translateX(26px);
		  -ms-transform: translateX(26px);
		  transform: translateX(31px);
		}
		#tabletMode{
			min-width: 765px;
		}
    </style>
</head>
<body>
	{{--@include('layouts.nav')--}}

	@include('layouts.flash')
	
	<div class='container'>

		<div class='row' id='tabletMode'>

			@include('layouts.sidebar')	

			@yield('content')

		</div>

	</div>
	@include('layouts.footer')
	<script src='js/setup.js?4.4'></script>
</body>
</html>