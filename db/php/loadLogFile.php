<?php
	require_once "utilis.php";

	$content = '<h2>Strona dla wgrywania pliku z logiem</h2>';
	$content .= '<h5><a href="../index.php">Wroc do strony glownej</a></h5>';
	$content .= '<form method="POST" enctype="multipart/form-data">';
	$content .= '<p><input type = "text" name="comment" value="komentarz"></p>';
	$content .= '<p><input type="file" name="logFile"></p>';
	$content .= '<p><input type="submit" value="Wyslij"></p>';
	$content .= '</form>';
	$content .= '<br>';

	if (isset($_FILES["logFile"])) {
		$conn = getConnect();
		$contents = file_get_contents($_FILES["logFile"]["tmp_name"]);
		$lines = parseLog($contents);
		foreach ($lines as $line) {
			$query = createInsertQuery($line, $_POST['comment']);
			$stid = prepareStatement($conn, $query);
			executeStatement($stid);
		}
		free($stid, $conn);
	}
	
	echo $content;
?>