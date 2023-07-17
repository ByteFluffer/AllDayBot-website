<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLog;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Session;


class AuthenticationController extends Controller
{
    public function login() 
    {
        return view('auth.login');
    }



    public function postLogin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($validated)) {
            $user = Auth::user()->name;
            ActivityLog::addToLog('Gebruiker $user heeft ingelogd');
            return redirect('/');
        } 
            
        ActivityLog::addToLog('Gefaalde login');
        return back()->with('error', 'Aaah grutjes, je kon niet ingelogd worden! Waarschijnlijk zijn je gegevens onjuist! :o ');
    }



    public function register()
    {
        return view('auth.register');
    }



    public function postRegistration(Request $request)
    {  
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
            
        $data = $request->all();
        $this->create($data);
        $user = Auth::user()->name;
        ActivityLog::addToLog("Gebruiker aangemaakt door $user");

        return back()->with('status', 'Gebruiker is aangemaakt');
    }



    public function changePassword()
    {
        return view('auth.passwordReset');
    }



    public function postChangePassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'password_second' => 'required'
        ]);

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->password)
        ]);

        $user = Auth::user()->name;
        ActivityLog::addToLog("Wachtwoord gewijzigd door $user");
        
        return back()->with('status', 'Wachtwoord is succesvol gewijzigd!');
    }



    public function create(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
    }


    
    public function logout()
    {
        $user = Auth::user()->name;
        ActivityLog::addToLog("Gebruiker $user heeft uitgelogd");

        Session::flush();
        Auth::logout();

        return redirect('/login');
    }
}
