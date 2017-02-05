<?php
	require_once "utilis.php";
	$content = "<h2>Strona testujaca</h2>";
	$content .= '<h5><a href="../index.php">Wroc do strony glownej</a></h5>';
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
				ORDER BY l.logId DESC
			';


	$conn = getConnect();
	$stid = prepareStatement($conn, $query);
	executeStatement($stid);
	$rowNames = ['Urzadzenie', 'Wiadomosc', 'Czas', 'Komentarz'];
	$content .= printStatement($stid, $rowNames);
	echo $content;

	free($stid, $conn);
	
?>