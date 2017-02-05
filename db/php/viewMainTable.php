<?php
	require_once "utilis.php";
	$content = '<h2>Tutaj masz swoje logi :D</h2>';
	$content .= '<h5><a href="../index.php">Wroc do strony glownej</a></h5>';
	$content .= '<form method="POST">';
	$content .= 'Urzadzenie:<br> <input type="text" name="driver"><br>';
	$content .= 'Wiadomosc:<br> <input type="text" name="message"><br>';
	$content .= 'Komentarz:<br> <input type="text" name="comment"><br>';
	$content .= 'Czas:<br> <input type="text" name="time"></p>';
	$content .= '<p><input type="submit" value="Filtruj"></p>';
	$content .= '</form>';
	$content .= '<br>';

	$query = '
			SELECT 
				d.name AS nazwaUrzadzenia,
				l.message AS wiadomosc,
				l.logTime AS czas,
				c.commentContent as komentarz
			FROM
				drivers d JOIN logs l
					ON d.driverId = l.driverId
				JOIN comments c
					ON c.commentId = l.commentId
				WHERE d.driverId = l.driverId
			';

	if (isset($_POST["driver"]) && !empty($_POST["driver"])) {
		$query .= 'AND d.name LIKE \'%'.$_POST["driver"].'%\'';
	}
	if (isset($_POST["message"]) && !empty($_POST["message"])) {
		$query .= 'AND l.message LIKE \'%'.$_POST["message"].'%\'';
	}
	if (isset($_POST["comment"]) && !empty($_POST["comment"])) {
		$query .= 'AND c.commentContent LIKE \'%'.$_POST["comment"].'%\'';
	}
	if (isset($_POST["time"]) && !empty($_POST["time"])) {
		$query .= 'AND l.logTime LIKE \'%'.$_POST["time"].'%\' ';
	}
	$query .= 'ORDER BY l.logId DESC';

	$conn = getConnect();
	$stid = prepareStatement($conn, $query);
	executeStatement($stid);
	$rowNames = ['Urzadzenia', 'Wiadomosc', 'Czas', 'Komentarz'];
	$content .= printStatement($stid, $rowNames);
	free($stid, $conn);


	echo $content;

?>