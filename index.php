<?php
	require 'db.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>The Job List</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.css">
	<link rel="icon" href="/assets/img/rocket.svg">

</head>
<body>

	<div class="ui container" style="margin: 30px auto; width: 95% !important;">
		<div class="ui middle aligned two column grid" style="margin-bottom: 10px;">
			<div class="column">
				<h1 class="ui middle aligned header">
					<img src="/assets/img/rocket.svg" class="ui big image">
					The Job List
				</h1>
			</div>
			<div class="right aligned column"><i class="ui refresh grey icon" id="getData" style="cursor: pointer;"></i></div>
		</div>		
		<div id="dataList"></div>
		<div class="ui grid" style="padding-top: 15px;">
			<div class="center aligned column">
				<a class="ui link small" href="https://github.com/VishwaGauravIn/linkedin-jobs-api" target="_blank">View GitHub</a>
			</div>
		</div>

	</div>



	<script type="text/javascript">
		
		$.get( "showJobs.php", function( data ) {
			$( "#dataList" ).html( data );
		});

		$("#getData").click(function(){

			$(this).addClass('loading');

			$( "#dataList" ).html('<div class="ui placeholder"><div class="image header"><div class="line"></div><div class="line"></div></div><div class="paragraph"><div class="line"></div><div class="line"></div><div class="line"></div><div class="line"></div><div class="line"></div></div></div>');

			$.get( "getJobs.php", function( data ) {



			}).done(function(){

				$.get( "showJobs.php", function( data2 ) {
					$( "#dataList" ).html( data2 );
					$("#getData").removeClass('loading');
				});

			});

		});


	</script>

</body>
</html>