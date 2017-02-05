<?php
	require_once "utilis.php";

	$content = '<h2>Strona dla wgrywania tekstu z logiem</h2>';
	$content .= '<h5><a href="../index.php">Wroc do strony glownej</a></h5>';
	$content .= '<form method="POST">';
	$content .= '<p><input type = "text" name="comment" value="komentarz"></p>';
	$content .= '<p><textarea name="logText" rows="20" cols="100"></textarea></p>';
	$content .= '<p><input type="submit" value="Wyslij"></p>';
	$content .= '</form>';
	$content .= '<br>';

	if (isset($_POST['logText'])) {
		$conn = getConnect();
		$lines = parseLog($_POST['logText']);
		foreach ($lines as $line) {
			$query = createInsertQuery($line, $_POST['comment']);
			$stid = prepareStatement($conn, $query);
			executeStatement($stid);
		}
		free($stid, $conn);
	}

	echo $content;
?>