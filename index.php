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

	<link href="https://cdn.datatables.net/v/se/jq-3.7.0/dt-2.1.0/b-3.1.0/date-1.5.2/sb-1.7.1/datatables.min.css" rel="stylesheet">
	<script src="https://cdn.datatables.net/v/se/jq-3.7.0/dt-2.1.0/b-3.1.0/date-1.5.2/sb-1.7.1/datatables.min.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.js"></script>	

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
				<form class="ui form">
					<div class="inline fields">
						<div class="six wide field"><input type="text" class="keyword" placeholder="Choose a keyword..."></div>
						<div class="three wide field"><button class="ui left labeled icon primary fluid button" id="getData" data-method="run"><i class="download icon"></i><span class="label">Run</span></button></div>
						<div class="three wide field"><button class="ui left labeled icon primary fluid button" id="loadData" data-method="getData"><i class="database icon"></i>Load Data</button></div>
						<div class="four wide field">
							<div class="ui selection dropdown fluid filter">
								<input type="hidden" name="">
								<i class="dropdown icon"></i>
								<div class="default text">Filter Jobs</div>
								<div class="menu">
									<div class="actionable item" data-value="all"><i class="ui empty circular label"></i>All</div>
									<div class="actionable item" data-value="active"><i class="ui empty circular label"></i>Active</div>
									<?php
										foreach($statusLabels AS $label=>$icon){
											echo '<div class="actionable item" data-value="'.$label.'"><i class="ui '.$icon.' empty circular label"></i>'.ucwords(str_replace("_"," ",$label)).'</div>';
										}
									?>
								</div>
							</div>	
						</div>
					</div>
				</form>

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

			$('.ui.dropdown.filter').dropdown({
				collapseOnActionable: false,
				onActionable: function(value, text, $selected) {
					$.get( "showJobs.php?filter="+value, function( data ) {
						alert(value);
						$( "#dataList" ).html( data );
						$("#getData").removeClass('loading');
					});
				}
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

		window.onload = function() { getRunStatus(); };
		setInterval(getRunStatus, 30000);
		
		$.get( "showJobs.php?filter=default", function( data ) {
			$( "#dataList" ).html( data );
		});

		$("#getData,#loadData").click(function(){

			$( "#dataList" ).html('<div class="ui placeholder"><div class="image header"><div class="line"></div><div class="line"></div></div><div class="paragraph"><div class="line"></div><div class="line"></div><div class="line"></div><div class="line"></div><div class="line"></div></div></div>');

			var runMethod = $(this).data( "method" );
			var keyword = $("input.keyword").val();

			if(runMethod == 'run'){
				changeDataButton("RUNNING");
			}

			$.get( "getJobs.php?method="+runMethod+"&keyword="+keyword, function( data ) {

			}).done(function(){

				$.get( "showJobs.php", function( data2 ) {
					$( "#dataList" ).html( data2 );
				});

			});

		});


	</script>

</body>
</html>