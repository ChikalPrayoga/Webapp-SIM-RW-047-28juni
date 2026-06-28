<?php
$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
foreach($tables as $t) echo $t->name . "\n";
