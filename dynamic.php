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

	<form id="main" action="dynamic.php" method="post" enctype="multipart/form-data">

		<!-- Data source selection -->
		<label id="mainText">Select a data set:</label> <select name="dataset">
			<option value="">Not Selected</option>
			<option value="dynamicdataset1">Dataset1:5 frames</option>
			<option value="dynamicdataset2">Dataset2:10 frames</option>
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
				
// 				echo $image_width;
// 				echo $image_height;
				
				// creating an array for storing the data
				$dataInfo = "";
				$content = "";
				$Xmax = "";
				$Ymax = "";
				
				// reading file, and store the data into arrays
				if(strcmp($which_dataset, "")!=0){
					$handle = fopen ( $which_dataset.".txt", "r" );
					if ($handle) {
						while ( ($line = fgets ( $handle )) !== false ) {
							
							$items = explode ( " ", $line );
							if(strcmp($items[0], "Start") == 0)
							{
								//echo $items[0];
								$dataInfo = $dataInfo .$items[1]. " " . $items[2]. " ". $items[3] . "/";
								$Xmax = $items[2];
								$Ymax = $items[3];
							}
							else
							{
								$coordinates = explode(" ", $line);
								$content = $content . intval(floatval($coordinates[0])*($image_width/$Xmax))." ".intval(floatval($coordinates[1])*($image_height/$Ymax))." ".$coordinates[2]."/";
							}
						}
					} else {
						// nothing here
					}
					fclose ( $handle );
					session_start();
					
					$_SESSION['datainfo'] = $dataInfo;
					$_SESSION['content'] = $content;
					//echo $content;
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
			
		}
	}
	?>
	
	<script type="text/javascript" src="heatmap.js"></script>
	<script type="text/javascript" src="jquery-1.11.1.js"></script>
	<script type="text/javascript">
	window.onload = function(){
 
	// heatmap configuration
	var heatmap = h337.create({"element":document.getElementById("heatmapArea"), "radius":25, "visible":true, "maxOpacity":0.1, "minOpacity":0});

	var datainfo = <?php echo json_encode($_SESSION['datainfo']) ?>;

	var content = <?php echo json_encode($_SESSION['content']) ?>;

	var infoArray = datainfo.split("/");
	infoArray.pop();

	var contentArray = content.split("/");
	contentArray.pop();

	var map = new Object();
	var StartPosition = 0;
	for(var i = 0; i < infoArray.length; i++)
	{
		var info = infoArray[i];
		var three = info.split(" ");
		var size = three[1];
		var corresponding_data = "";
		var length = StartPosition + parseInt(size);
		for(var j = StartPosition; j < length; j++)
		{
			corresponding_data = corresponding_data + contentArray[j] +" ";
		}
		StartPosition = length;
		map[info] = corresponding_data;
	}

	//console.log(map);

	var interval = 1000;
	for(var info in map)
	{
// 		var items = info.split(" ");
// 		var max = items[0];
// 		//var size = items[1];
// 		var dat = map[info];
// 		dat = dat.split(" ");
// 		dat.pop();

// 		//console.log(dat);

// 		var data = {max: max, data:[]};
// 		for(var i =0; i< dat.length; i = i+3)
// 		{
// 			data.data.push({x: dat[i], y: dat[i+1], count: dat[i+2]});
// 		}
		setTimeout(function(x){ return function()
			{
				var items = x.split(" ");
				var max = items[0];
				//var size = items[1];
				var dat = map[x];
				dat = dat.split(" ");
				dat.pop();
	
	
				var data = {max: max, data:[]};
				for(var i =0; i< dat.length; i = i+3)
				{
					data.data.push({x: dat[i], y: dat[i+1], count: dat[i+2]});
				}

				//console.log(data);
				
				heatmap.store.setDataSet(data);
				//alert("Here");
			};
		}(info), interval);
		//heatmap.store.setDataSet(data);
		interval = interval + 1000;
	}

// 		for (var i = 1; i <= 5; i++) {
// 		    setTimeout(function(x) { return function() { alert(x); }; }(i), 1000*i);
// 		    // 1 2 3 4 5
// 		}

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