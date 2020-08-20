<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DataTables;

class UsersController extends Controller
{



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::paginate(10);
        $count = User::count();
        return view('Admin.users' , compact('users' , 'count'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::all()->where('name', '!=', 'superadmin');

        if ($user->hasAnyRole('superadmin')) {
            return redirect(route('users.index'))->with('error' , 'You can not Edit The Super Admin are you idiot return back baaaaaaaaaaaakaaaaaaaaa!');
        }
        if (Auth::user()->hasAnyRole('admin') && $user->hasAnyRoles(['superadmin' , 'admin'])) {
            abort(403);
        }

        return view('Admin.editUsers' , compact('user' , 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->roles()->sync($request->role);

        return redirect(route('users.index'))->with('msg' , 'The User Has updated Successfully !');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->hasAnyRole('superadmin')) {
            return redirect(route('users.index'))->with('error' , 'You can not Delete The Super Admin are you idiot return back baaaaaaaaaaaakaaaaaaaaa!');
        }

        if (Auth::user()->hasAnyRole('admin') && $user->hasAnyRoles(['superadmin' , 'admin'])) {
            abort(403);
        }


        if ($user) {
            $user->roles()->detach();
            User::destroy($user->id);
            return redirect(route('users.index'))->with('msg' , 'The User Has Deleted successfully !');
        }
        return redirect(route('users.index'))->with('error' , 'The User Can not be Deleted');
    }
}
