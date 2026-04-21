<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\MpEmployee;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    /** หน้า Login */
    public function index(): View
    {
        return view('Auth.login');
    }

    public function register(): View
    {
        $departments = \App\Models\MpDepartment::all();
        $positions = \App\Models\MpPosition::all();
        return view('Auth.registration', compact('departments', 'positions'));
    }

    /** กด Login */
    public function postlogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'กรุณากรอกอีเมล',
            'email.email'       => 'รูปแบบอีเมลไม่ถูกต้อง',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // ป้องกัน session fixation
            return redirect()->intended(route('dashboard'))
                ->with('success', 'เข้าสู่ระบบสำเร็จ');
        }

        return back()
            ->withErrors(['email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'])
            ->onlyInput('email');
    }

    /** กด Register */
    public function postregister(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:mp_users,email'],
            'gender' => ['required', 'in:M,F'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'dept_id'    => ['required', 'integer', 'exists:mp_departments,dept_id'],
            'position_id' => ['required', 'integer', 'exists:mp_positions,position_id'],
        ], [
            'first_name.required' => 'กรุณาระบุชื่อ',
            'first_name.max' => 'ชื่อยาวเกินไป (50 ตัวอักษร)',
            'last_name.required' => 'กรุณาระบุนามสกุล',
            'last_name.max' => 'นามสกุลยาวเกินไป (50 ตัวอักษร)',
            'email.required' => 'กรุณาระบุอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.max' => 'อีเมลยาวเกินไป (100 ตัวอักษร)',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',

        ]);

        $user = MpEmployee::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'gender' => $validated['gender'],
            'dept_id' => $validated['dept_id'],
            'position_id' => $validated['position_id'],
            'password_hash' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'สมัครสมาชิกและเข้าสู่ระบบสำเร็จ');
    }

    /** หน้า Dashboard (ถ้าไม่ล็อกอินจะเด้งไปหน้า Login) */
    public function dashboard(): View|RedirectResponse
    {
        if (Auth::check()) {
            return view('backoffice.dashboard');
        }
        return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
    }

    public function creation(array $data): RedirectResponse
    {
        $user = MpEmployee::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'สมัครสมาชิกและเข้าสู่ระบบสำเร็จ');
    }

    /** ออกจากระบบ */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'ออกจากระบบสำเร็จ');
    }
}
