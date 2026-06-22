<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DemoController extends Controller
{
    public function loginAsTeacher(): RedirectResponse
    {
        $user = $this->createDemoUser('teacher');
        Auth::login($user);
        return redirect('/admin');
    }

    public function loginAsStudent(): RedirectResponse
    {
        $user = $this->createDemoUser('student');
        
        $demoClass = SchoolClass::where('is_demo', true)->first();
        if ($demoClass) {
            $user->class_id = $demoClass->id;
            $user->save();
        }

        Auth::login($user);
        return redirect('/student/dashboard');
    }

    private function createDemoUser(string $role): User
    {
        $suffix = Str::random(8);
        $user = User::create([
            'name' => $role === 'teacher' ? 'Guru Demo' : 'Siswa Demo',
            'email' => ($role === 'teacher' ? 'guru-demo-' : 'siswa-demo-') . $suffix . '@demo.local',
            'password' => bcrypt(Str::random(16)),
            'is_demo' => true,
        ]);

        $user->assignRole($role);
        $user->markEmailAsVerified(); // demo users skip the verification flow
        return $user;
    }
}
