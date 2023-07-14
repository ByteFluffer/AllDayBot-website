<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Session;



class authentication extends Controller
{



    public function login() 
    {

        return view("auth.login");

    }



    public function post_login(Request $request)
    {

        $request->validate([
            "email" => "required",
            "password" => "required"
        ]);

        $credentials = $request->only("email", "password");
        
        if (Auth::attempt($credentials)) {
            return redirect()->intended("/")->withSucces("Je bent ingelogd");
        } else {
            return redirect("/login")->withSucces("Aah er ging iets mis");
        }

    }



    public function register()
    {

        if (Auth::check()) {
            return view("auth.register");
        }

    }



    public function post_registration(Request $request)
    {  

        if (Auth::check()) {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);
               
            $data = $request->all();
            $check = $this->create($data);
            return redirect("/")->withSuccess('Great! You have Successfully loggedin');
        }

    }



    public function change_password()
    {
        return view("auth.password_reset");
    }



    public function post_change_password(Request $request)
    {

        $request->validate([
            "password" => "required",
            "password_second" => "required"
        ]);

        User::whereId(auth()->user()->id)->update([
            "password" => Hash::make($request->password)
        ]);

        return back()->with("status", "Wachtwoord is succesvol gewijzigd!");

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

        Session::flush();
        Auth::logout();

        return redirect("/login");
        
    }
}
