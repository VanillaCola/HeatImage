<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<title>Heat Image</title>
</head>
<body>
	<p id="Title">Heat Image</p>
	
	<div class="menu">
	<ul>
	<li><a href="index.php">Static</a></li>
	<li><a href="dynamic.php">Dynamic</a></li>
	<li><a href="contact.html">Contact</a></li>
	<li><a href="about.html">About</a></li>
	</ul>
	<br style="clear:left"/>
	</div>

	<form id="main" action="index.php" method="post" enctype="multipart/form-data">

		<!-- Data source selection -->
		<label id="mainText">Select a data set:</label> <select name="dataset">
			<option value="">Not Selected</option>
			<option value="dataset1">Dataset1(1000)</option>
			<option value="dataset2">Dataset2(500)</option>
			<option value="dataset3">Dataset3(100)</option>
		</select><br><br> 
	
	<?php
	// Once User trying to upload the file
	if (isset ( $_FILES ["file"] )) {
		// If nothing selected, then notify the user, else get the data set the user select,
		// and display the select image on the web
		if ($_FILES ["file"] ["error"] > 0) {
			echo "You must select an image....";
		} else {
			
			if (isset($_POST ["dataset"])){
				// Get the data set name
				$which_dataset = $_POST ["dataset"];
				// echo $which_dataset."<br>";
				
				// Get the image size (height and width)
				$image_info = getimagesize ( $_FILES ["file"] ["tmp_name"] );
				$image_width = $image_info [0];
				$image_height = $image_info [1];
				
				// creating an array for storing the data
				$max;
				$Xs = array ();
				$Ys = array ();
				$counts = array();
				
				// reading file, and store the data into arrays
				if(strcmp($which_dataset, "")!=0){
					$handle = fopen ( $which_dataset.".txt", "r" );
					if ($handle) {
						$startLine = 1;
						while ( ($line = fgets ( $handle )) !== false ) {
							if($startLine == 1)
							{
								$items = explode ( " ", $line );
								$max = $items[0];
								$Xmax = $items[1];
								$Ymax = $items[2];
								//echo $max."<br>";
								$startLine = 0;
							}
							else
							{
								$items = explode ( " ", $line );
								array_push ($Xs, intval(floatval($items[0])*($image_width/$Xmax)));
								
								//echo $items[0]."<br>";
								array_push ($Ys, intval(floatval($items[1])*($image_height/$Ymax)));
								//echo $items[1]."<br>";
								array_push( $counts, intval( $items [2]));
								//echo $items[2]."<br>";
							}
						}
					} else {
						// nothing here
					}
					fclose ( $handle );
					session_start();
					
					$_SESSION['Xs'] = $Xs;
					$_SESSION['Ys'] = $Ys;
					$_SESSION['max'] = $max;
					$_SESSION['Xmax'] = $Xmax;
					$_SESSION['Ymax'] = $Ymax;
					$_SESSION['counts'] = $counts;
				}
				
				// displaying image on webpage
				$imageSize = getimagesize ( $_FILES ['file'] ['tmp_name'] );
				$sImage = "data:" . $imageSize ["mime"] . ";base64," . base64_encode ( file_get_contents ( $_FILES ['file'] ['tmp_name'] ) );
				
				echo '<div id="heatmapArea" style = "background-image: url('.$sImage.'); height: '.$image_height.'; width: '.$image_width.'; "></div>';
			
			}
			else 
			{
				session_unset();
			}
			
			// echo "Upload: " . $_FILES["file"]["name"] . "<br>";
			// echo "Type: " . $_FILES["file"]["type"] . "<br>";
			// echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
			//echo "Stored in: " . $_FILES["file"]["tmp_name"]."<br>";
			
		}
	}
	?>
	
	<script type="text/javascript" src="heatmap.js"></script>
	<script type="text/javascript" src="jquery-1.11.1.js"></script>
	<script type="text/javascript">
	window.onload = function(){
 
	// heatmap configuration
	var heatmap = h337.create({"element":document.getElementById("heatmapArea"), "radius":25, "visible":true, "maxOpacity":0.1, "minOpacity":0});

// 	var myElement = document.getElementById("heatmapArea"); 
// 	var position = getPosition(myElement);

	//Get the stored Xs data from session
	var Xs = <?php echo json_encode($_SESSION['Xs']) ?>;

	//Get the stored Ys data from session
	var Ys = <?php echo json_encode($_SESSION['Ys']) ?>;

	//Get the max
	var max = <?php echo $_SESSION['max'] ?>;
	
	//Get the counts
	var counts = <?php echo json_encode($_SESSION['counts']) ?>;

	var data = {max: max, data:[]};
	for (var i = 0; i < Xs.length; i++) {
		var newX = Xs[i];
		var newY = Ys[i];
		var count = counts[i];
		data.data.push({x: newX, y: newY, count:count});
	}
	
// 	let's get some data

	heatmap.store.setDataSet(data);
	//heatmap.store.generateRandomDataSet(1000);
	//var dataset = heatmap.store.exportDataSet();
};

</script>
		<br>
		<!-- Upload an image -->
		<label for="file" id="mainText">Choose a Picture:</label> <input
			type="file" multiple accept='image/*' name="file" id="file"><br><br>

		<P class="submit">
			<input type="submit" name="Submit" value="Upload">
		</P>
	</form>

</body>
</html>