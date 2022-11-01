<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RegistrationController extends Controller
{
    public function registerView()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $roles = Role::whereIn('name', ['teacher', 'student', 'parent'])->get();
        $validated = $request->validate([
            'role' => [
                'required',
                Rule::in($roles->pluck('id')),
            ],
            'school' => [
                'required',
                'exists:schools,id',
            ],
        ]);

        $role = $roles->find($request->role);
        $request['school_id'] = $request->school;

        switch ($role->name) {
            case 'student':
                app('App\Services\Student\StudentService')->createStudent($request);
                break;
            case 'teacher':
                app('App\Services\Teacher\TeacherService')->createTeacher($request);
                break;
            case 'parent':
                app('App\Services\Parent\ParentService')->createParent($request);
                break;
            default:
                session()->flash('danger', 'Role could not be resolved');
                break;
        }

        return back();
    }
}
