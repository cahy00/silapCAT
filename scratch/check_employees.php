<?php

$employees = \App\Models\Employee::all();
echo "Total: " . $employees->count() . PHP_EOL;

$roles = [];
foreach ($employees as $emp) {
    foreach (($emp->status ?? []) as $s) {
        $roles[$s] = ($roles[$s] ?? 0) + 1;
    }
}

echo json_encode($roles, JSON_PRETTY_PRINT);
