<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	putenv('ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe');
	putenv('LD_LIBRARY_PATH=/usr/lib/oracle/12.1/client64/lib');

	function getConnect() {
		return oci_connect('alex', 'Uwpas1704', 'XE');
	}

	function prepareStatement($conn, $query) {
		$stid = oci_parse($conn, $query);
		if (!$stid) {
		    $e = oci_error($conn);
		    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
		// echo "$query <br>";
		return $stid;
	}

	function executeStatement($stid) {
		$r = oci_execute($stid);
		if (!$r) {
		    $e = oci_error($stid);
		    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}
	}

	function printStatement($stid, $rowNames) {
		$content = "<table border='1'>\n";
		$content .= "<tr>\n";
		foreach ($rowNames as $rowName) {
			$content .= "    <th>" . $rowName . "</th>\n";
		}
		$content .= "</tr>\n";

		while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
		    $content .= "<tr>\n";
		    foreach ($row as $item) {
		        $content .= "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
		    }
		    $content .= "</tr>\n";
		}
		$content .= "</table>\n";
		return $content;
	}

	function free($stid, $conn) {
		oci_free_statement($stid);
		oci_close($conn);
	}

	function parseLog($lines) {
		$lines = str_replace('\'', '', $lines);
		// echo $lines;
		$pattern = "/(\[(.*)\] ([^\:\n]+)\: (.*))[\n]+/";
    	preg_match_all($pattern, $lines."\n", $matches);
    	
    	$len = count($matches[1]);
    	$result = array();
    	for($i = 0; $i < $len; $i++) {
        	$result []= array('time' => $matches[2][$i], 'driver' => $matches[3][$i], 'msg' => $matches[4][$i]);
    	}
    	return $result;
	}

	function checkDriver($driver, $conn) {
		$stid = prepareStatement($conn, "SELECT driverId FROM drivers WHERE name = '$driver'");
		executeStatement($stid);
		$ar = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
		if (!$ar) {
			return 0;
		}
		$ret = $ar['DRIVERID'];
		return $ret;
	}

	function checkComment($comment, $conn) {
		$stid = prepareStatement($conn, "SELECT commentId FROM comments WHERE commentContent = '$comment'");
		executeStatement($stid);
		$ar = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
		if (!$ar) {
			return 0;
		}
		$ret = $ar['COMMENTID'];
		return $ret;
	}

	function createInsertQuery($log, $comment) {
		$conn = getConnect();
		// tu dodaje driver jesli trzeba, zapisuje id
		$driver = $log["driver"];
		$driverId = checkDriver($driver, $conn);
		if (!$driverId) {
			$driverQuery = "declare
							    l_res number;
							begin
							    INSERT INTO drivers (name) VALUES ('$driver') returning driverId into l_res;
							    :l_res1 := l_res;
							end;
							";
			$stid = prepareStatement($conn, $driverQuery);
			oci_bind_by_name($stid, ":l_res1", $driverId);
			executeStatement($stid);
		}
		
		// tu dodaje komentarz zawsze, zapisuje id
		$commentId = checkComment($comment, $conn);
		if (!$commentId) {
			$commentQuery = "declare
								    l_res number;
								begin
								    INSERT INTO comments (commentContent) VALUES ('$comment') returning commentId into l_res;
								    :l_res2 := l_res;
								end;
								";
			$stid = prepareStatement($conn, $commentQuery);
			oci_bind_by_name($stid, ":l_res2", $commentId);
			executeStatement($stid);
		}
		
		// free($stid, $conn);
		oci_close($conn);

		// tu dodaje log po-prostu
		$message = $log["msg"];
		$time = $log["time"];
		$query = "INSERT INTO logs (driverId, message, commentId, logTime) VALUES ($driverId, '$message', $commentId, '$time')";
		return $query;
	}

?>