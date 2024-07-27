<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdminMiddleware;
use App\Models\User;
use App\Models\User as Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (Auth::user()->role == User::ADMIN_ROLE) {
            $members = User::whereRole(User::MEMBER_ROLE)->get();
            return view('members.index', compact('members'));
        }
        return abort(403);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        $admin = Auth::user();
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'role' => User::MEMBER_ROLE,
            'parent_id' => $admin->id,
        ]);

        return response()->json(['success' => true, 'user' => $user], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        return response()->json($member);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        $user = $member;

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
        ]);
        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ]);

        return response()->json(['success' => true, 'user' => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();

        return response()->json(['success' => true], 200);
    }
}
