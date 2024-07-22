<?php
require 'db.php';
$applyButton = "";
?>

<table class="ui compact small selectable table" id="jobsList">
	<thead>
		<tr>
			<th class="one wide">Date Posted</th>
			<th class="one wide">Date Posted</th>
			<th class="three wide">Title</th>
			<th class="two wide">Company</th>
			<th class="three wide">Work Type</th>
			<th class="two wide">Sector</th>
			<th class="two wide">Location</th>
			<th class="one wide"></th>
			<th class="one wide"></th>
			<th class="one wide"></th>
		</tr>
	</thead>
	<?php

	$statusLabels = array("new" => "blue","applied" => "orange","in_progress" => "green","rejected" => "red","not_interested" => "black");

	$sql = "SELECT * FROM jobs WHERE status != 'not_interested' ORDER BY date ASC";
	$result = $mysqli->query($sql);
	while($row = $result->fetch_object()){

		if($row->status != 'applied'){
			($row->easy_apply == 1) ? 
				$applyButton = '<a class="ui left labeled icon tiny primary button" href="'.$row->apply_url.'" target="_blank"><i class="ui linkedin icon"></i>Apply</a>' :
				$applyButton = '<a class="ui left labeled icon tiny teal button" href="'.$row->apply_url.'" target="_blank"><i class="ui external icon"></i>Apply</a>';
		}

		($row->updatedDate != '') ? $updatedDate = $row->updatedDate = date("Y-m-d H:i:s",$row->updatedDate) : $updatedDate = '<a class="ui tertiary button not-interested" data-id="'.$row->id.'">Nope</a>';

		echo '
		<tr class="'.$statusLabels[$row->status].' colored left '.$statusLabels[$row->status].' marked">
			<td>'.date("Y-m-d H:i:s",$row->date).'</td>
			<td>
				<div class="ui middle aligned list">
				  <div class="item">
				    <i class="ui '.$row->source.' large blue icon"></i>
				    <div class="content">
				      <div>'.$row->agoTime.'</div>
				    </div>
				  </div>
				</div>					
			</td>
			<td><b><a href="'.$row->jobUrl.'" target="_blank">'.$row->position.'</a></b></td>
			<td>'.stripslashes($row->company).'</td>
			<td>'.stripslashes($row->work_type).'</td>
			<td>'.stripslashes($row->sector).'</td>
			<td>'.ucwords(stripslashes($row->location)).'</td>
			<td class="">'.$applyButton.'</td>
			<td>
				<div class="ui dropdown" data-id="'.$row->id.'">
				  <input type="hidden" name="filters">
				  <span class="text"><div class="ui '.$statusLabels[$row->status].' empty circular label"></div> '.ucwords(str_replace("_"," ",$row->status)).'</span>
				  <div class="menu">
				    <div class="scrolling menu">';
						foreach($statusLabels AS $label=>$icon){							
							($row->status == $label) ? $activeItem = 'active selected' : $activeItem = '';							
							echo '<div class="actionable item '.$activeItem.'" data-value="'.$label.'"><div class="ui '.$icon.' empty circular label"></div>'.ucwords(str_replace("_"," ",$label)).'</div>';
						}
						echo '
					</div>
				  </div>
				</div>
			</td>
			<td>'.$updatedDate.'</td>
		</tr>';
	}

	?>
</table>

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
	$('.ui.dropdown').dropdown({

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