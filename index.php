<?php
$query = $_SERVER['QUERY_STRING'] ?? '';
$target = 'public/index.php' . ($query ? ('?' . $query) : '');
header('Location: ' . $target, true, 302);
exit;
