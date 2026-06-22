<?php
$c = file_get_contents('app/Http/Controllers/ErpExpenseController.php');
$c = str_replace("Auth::user()->can('Submit Expenses')", "(Auth::user()->can('Submit Expenses') || Auth::user()->can('Manage Purchases & Suppliers') || Auth::user()->can('Manage Employees'))", $c);
file_put_contents('app/Http/Controllers/ErpExpenseController.php', $c);
echo "Done";
