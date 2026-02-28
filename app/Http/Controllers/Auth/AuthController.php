<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CashCount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Repositories\LogRepository;

class AuthController extends Controller
{
    protected $logRepo;

    public function __construct(LogRepository $logRepo)
    {
        $this->logRepo = $logRepo;
    }

    /**
     * Show login page
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Register a new user and assign a role.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|string|in:admin,customer,supplier',
        ]);

        // Create the user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign the role using Spatie
        $user->assignRole($request->role);

        // Auto-login
        Auth::login($user);

        // Log login activity
        $this->logRepo->logLogin($request);

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user,
            'roles'   => $user->getRoleNames()
        ], 201);
    }

    /**
     * Login existing user and store login log
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->with('error', 'Invalid email or password.');
        }

        $request->session()->regenerate();

        // Log the login activity
        $this->logRepo->logLogin($request);

        return redirect()->intended('admin/dashboard')->with('success', 'Login successful!');
    }

    /**
     * Logout current user and update logout log
     */
    public function logout(Request $request)
    {

        $user = auth()->user();
       if(!in_array($user->roles->first()->id , [1,5])){
            $todayCashCount = CashCount::where('cashier_id',$user->id)->where('date', now()->format('Y-m-d'))->count();
        }
       
        if ($user->roles->first()->name == 'cashier' && $todayCashCount == 0) {
            return redirect()->route("admin.wallets.cash-count")->with("error", "Before logout , please complete today's cash count");
        }

        // Log logout activity
        $this->logRepo->logLogout();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}