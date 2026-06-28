<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
$response = curl_exec($ch);
curl_close($ch);

// parse CSRF token from response if needed, or just run a quick Laravel test script.
