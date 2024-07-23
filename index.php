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

	<link rel="stylesheet" href="//cdn.datatables.net/2.1.0/css/dataTables.dataTables.min.css">
	<script src="https://cdn.datatables.net/2.1.0/js/dataTables.min.js"></script>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.css">
	<link rel="icon" href="/assets/img/rocket.svg">

</head>
<body>

	<div class="ui container" style="margin: 30px auto; width: 95% !important;">
		<div class="ui middle aligned three column grid" style="margin-bottom: 10px;">
			<div class="column">
				<h1 class="ui middle aligned header">
					<img src="/assets/img/rocket.svg" class="ui big image">
					The Job List
				</h1>
			</div>
			<div class="center aligned column">
				<div class="ui success message jobAdded" style="display: none;">Job added successfully!</div>
			</div>
			<div class="right aligned column">
				<button class="ui left labeled icon primary button" id="getData" data-method="run"><i class="download icon"></i><span class="label">Run</span></button>
				<button class="ui left labeled icon primary button" id="loadData" data-method="getData" style="display: none;"><i class="database icon"></i>Load Data</button>
			</div>
		</div>		
		<div id="dataList"></div>
		<div class="ui grid" style="padding-top: 15px;">
			<div class="center aligned column">
				<a class="ui link small" href="https://github.com/VishwaGauravIn/linkedin-jobs-api" target="_blank">View GitHub</a>
			</div>
		</div>

	</div>

	<div class="ui modal">
		<div class="dividing header">Add new job</div>
		<div class="content">
			<form class="ui form" id="addJob">
				<div class="field"><input type="text" class="job_title" placeholder="Job Title" required></div>
				<div class="field"><input type="text" class="company" placeholder="Company" required></div>
				<div class="field"><input type="text" class="location" placeholder="Location" required></div>
				<div class="field"><input type="text" class="job_url" placeholder="Job URL" required></div>
				<div class="field"><input type="text" class="apply_link" placeholder="Application Link" required></div>
				<div class="field"><input type="text" class="source" placeholder="Source" required></div>
				<div class="two fields">
					<div class="two wide field"><button class="ui primary button" type="submit">Add Job</button></div>
					<div class="two wide field"><a class="ui button deny">Cancel</a></div>
				</div>
					
			</form>
		</div>
	</div>

	<i class="ui circular inverted black plus big link icon addJob" style="position: fixed; bottom: 30px; right: 30px;"></i>

	<script type="text/javascript">

		$("i.addJob").click(function(){
			$(".ui.modal").modal("show");
			$(this).fadeOut();
		});

		$(".deny").click(function(){
			$(".ui.modal").modal("hide");
			$("i.addJob").fadeIn();
		});

		$("#addJob").submit(function (event) {
		    var formData = {
		      job_title: $(".job_title").val(),
		      company: $(".company").val(),
		      location: $(".location").val(),
		      apply_link: $(".apply_link").val(),
		      job_url: $(".job_url").val(),
		      source: $(".source").val()
		    };

		    $.ajax({
		      type: "POST",
		      url: "addJob.php",
		      data: formData,
		      encode: true,
		    }).done(function (data) {
				
				// 1. Close modal
				$(".ui.modal").modal("hide");
				$("i.addJob").fadeIn();
				
				// 2. Refresh Data Table
				$.get( "showJobs.php", function( dataJobs ) {
					$( "#dataList" ).html( dataJobs );
				});

				// 3. Show Success Message
				$(".ui.message.jobAdded").fadeIn().html('Job added successfully').delay(5000);
				$(".ui.message.jobAdded").fadeOut();

		    });

		    event.preventDefault();
		  });

	</script>



	<script type="text/javascript">
		
		function changeDataButton(data){
			if(data == 'RUNNING'){
				$("#getData").addClass('disabled');
				$("#getData i").removeClass('download');
				$("#getData i").addClass('circle notch loading');
				$("#getData .label").html('Running');
				$("#loadData").hide();
			}

			if(data == 'SUCCEEDED'){
				$("#getData").removeClass('disabled');
				$("#getData i").addClass('download');
				$("#getData i").removeClass('circle notch loading');
				$("#getData .label").html('Run');					
				$("#loadData").show();
			}			
		}

		function getRunStatus() {
			$.get( "getJobs.php?method=getRun", function( data ) {

				console.log(data);
				changeDataButton(data);				

			});
		}

		window.onload = function() {
			getRunStatus();
		};

		setInterval(getRunStatus, 30000);
		
		$.get( "showJobs.php", function( data ) {
			$( "#dataList" ).html( data );
		});

		$("#getData,#loadData").click(function(){

			$( "#dataList" ).html('<div class="ui placeholder"><div class="image header"><div class="line"></div><div class="line"></div></div><div class="paragraph"><div class="line"></div><div class="line"></div><div class="line"></div><div class="line"></div><div class="line"></div></div></div>');

			var runMethod = $(this).data( "method" );

			if(runMethod == 'run'){
				changeDataButton("RUNNING");
			}

			$.get( "getJobs.php?method="+runMethod, function( data ) {

			}).done(function(){

				$.get( "showJobs.php", function( data2 ) {
					$( "#dataList" ).html( data2 );
				});

			});

		});


	</script>

</body>
</html>