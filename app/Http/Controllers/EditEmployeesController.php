<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EditEmployeesController extends Controller
{
    public function index() {
        return view('edit-employees');
    }

    public function store() {
        
    }

    public function edit(Employee $employee) {
    return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        // ...
    }
}
