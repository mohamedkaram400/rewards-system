<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return $users;
    }

    public function store(Request $request)
    {
        $users = User::create($request->all());
    }

    public function getUserDetails($id)
    {
        $user = User::findOrFail($id);
        return $user;
    }
}
