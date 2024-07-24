<?php
	require 'db.php';
	$applyButton = "";

	switch($_GET['filter']){
		default:
			$sql = "SELECT * FROM jobs WHERE status != 'not_interested' ORDER BY date ASC";
			break;
		case "all":
			$sql = "SELECT * FROM jobs ORDER BY date ASC";
			break;
		case "new":
			$sql = "SELECT * FROM jobs WHERE status = 'new' ORDER BY date ASC";
			break;
		case "potential":
			$sql = "SELECT * FROM jobs WHERE status = 'potential' ORDER BY date ASC";
			break;
		case "applied":
			$sql = "SELECT * FROM jobs WHERE status = 'applied' ORDER BY date ASC";
			break;
		case "read":
			$sql = "SELECT * FROM jobs WHERE status = 'read' ORDER BY date ASC";
			break;
		case "in_progress":
			$sql = "SELECT * FROM jobs WHERE status = 'in_progress' ORDER BY date ASC";
			break;
		case "rejected":
			$sql = "SELECT * FROM jobs WHERE status = 'rejected' ORDER BY date ASC";
			break;
		case "not_interested":
			$sql = "SELECT * FROM jobs WHERE status = 'not_interested' ORDER BY date ASC";
			break;
	}
	
	$result = $mysqli->query($sql);
	$numRows = $result->num_rows;
	if($numRows > 0){

		echo '
		<table class="ui compact small selectable table" id="jobsList">
			<thead>
				<tr>
					<th class="two wide">Date Posted</th>
					<th class="three wide">Title</th>
					<th class="two wide">Company</th>
					<th class="two wide">Work Type</th>
					<th class="two wide">Sector</th>
					<th class="two wide">Location</th>
					<th class="one wide">Options</th>
					<th class="one wide">Status</th>			
					<th class="two wide">Last Updated</th>
				</tr>
			</thead>';
			while($row = $result->fetch_object()){

				($row->updatedDate != '') ? $updatedDate = $row->updatedDate = date("Y-m-d H:i:s",$row->updatedDate) : $updatedDate = '<a class="ui tertiary button not-interested" data-id="'.$row->id.'">Nope</a>';

				echo '
				<tr class="'.$statusLabels[$row->status].' colored left '.$statusLabels[$row->status].' marked">
					<td>

						<div class="ui middle aligned list">
						  <div class="item">
						    <img src="assets/img/'.strtolower($row->source).'.svg" class="ui image" style="height: 20px;" title="'.$row->source.'">
						    <div class="content">
						    '.date("Y-m-d H:i:s",$row->date).'
						    </div>
						  </div>
						</div>					
					</td>
					<td><b><a href="'.$row->jobUrl.'" target="_blank">'.$row->position.'</a></b></td>
					<td>'.stripslashes($row->company).'</td>
					<td>'.stripslashes($row->work_type).'</td>
					<td>'.stripslashes($row->sector).'</td>
					<td>'.ucwords(stripslashes($row->location)).'</td>			
					<td>
						<div class="ui selection dropdown updateStatus" data-id="'.$row->id.'">
							<input type="hidden" name="">
							<i class="dropdown icon"></i>
							<div class="default text">Update Status</div>
							<div class="menu">';
								foreach($statusLabels AS $label=>$icon){
									($row->status == $label) ? $activeItem = 'active selected' : $activeItem = '';							
									echo '<div class="actionable item '.$activeItem.'" data-value="'.$label.'"><i class="ui '.$icon.' empty circular label"></i>'.ucwords(str_replace("_"," ",$label)).'</div>';
								}
								echo '
							</div>
						</div>
					</td>
					<td class="">'.ucwords(str_replace("_"," ",$row->status)).'</td>
					<td>'.$updatedDate.'</td>
				</tr>';
			}
		echo '</table>';

	}else{
		echo '
		<div class="ui message">
		<div class="content">
			<div class="header">No results found!</div>
			Maybe try a different filter?
		</div>
		</div>';
	}

	?>

<script type="text/javascript">

	new DataTable('#jobsList',{
		order: {
			idx: 0,
			dir: 'desc'
		},
		pageLength: 250
	});

	$(".not-interested").click(function(){
		var jobId = $(this).data( "id" );
		$.get( "updateJob.php?status=not_interested&id="+jobId, function( data ) {

			$.get( "showJobs.php", function( data2 ) {
				$( "#dataList" ).html( data2 );
				$("#getData").removeClass('loading');
			});

		});
	});

	$("i").popup();
	$('.ui.dropdown.updateStatus').dropdown({

		collapseOnActionable: false,
		onActionable: function(value, text, $selected) {
			
			var jobId = $(this).data( "id" );		

			$.get( "updateJob.php?status="+value+"&id="+jobId, function( data ) {

				$.get( "showJobs.php", function( data2 ) {
					$( "#dataList" ).html( data2 );
					$("#getData").removeClass('loading');
				});

			});
		}
	});

</script>