<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function show(Organization $organization)
    {
        return view('organizations.show', compact('organization'));
    }
}
