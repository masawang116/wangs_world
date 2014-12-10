<?php
    $path = __DIR__;
    require_once $path . '\..\..\includes\connect_to_db.php';
    
    $raw_name = basename($_SERVER['PHP_SELF']);
    $title = get_web_page_name_from_raw_name($db, $raw_name);
    if(!$title) {
        $title = 'Wang\'s World';
    }
    date_default_timezone_set('America/Los_Angeles');
?>
<!DOCTYPE html>
<html>
	<head>
                <title><?php echo $title; ?></title>
	</head>
	<body>
		<header>
			<p>'This is the header.'</p>
		</header>