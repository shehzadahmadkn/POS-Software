<?php
$files = glob('app/Http/Controllers/*.php');
foreach($files as $f) {
    $c = file_get_contents($f);
    if(substr_count($c, 'use App\Models\Account;') > 1) {
        $c = preg_replace('/use App\\\\Models\\\\Account;\r?\n/', '', $c, 1);
        file_put_contents($f, $c);
        echo 'Fixed ' . $f . PHP_EOL;
    }
}
