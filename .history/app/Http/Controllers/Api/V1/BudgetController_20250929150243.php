<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    // Logika untuk CRUD Budget akan ditambahkan di sini
    public function index() { /* ... */ }
    public function store(Request $request) { /* ... */ }
    public function show(Budget $budget) { /* ... */ }
    public function update(Request $request, Budget $budget) { /* ... */ }
    public function destroy(Budget $budget) { /* ... */ }
}
