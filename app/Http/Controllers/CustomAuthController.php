<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Session;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{
    public function login()
    {
        return view ('auth.Login');
    }

    public function registration()
    {
        return view ('auth.registration');
    }
    public function registerUser(Request $request)
    {
        $request->validate(
            [
                'first_name'=>'required',
                'last_name'=>'required',
                'email'=>'required|email|unique:users',
                'password'=>'required|min:5|max:15'
            ]
            );
            $user=new User();
            $user->first_name= $request ->first_name;
            $user->last_name= $request ->last_name;
            $user->email= $request ->email;
            // $user->birthday= $request ->birthday;
            $user->phone= $request ->phone;
            $user->password= Hash::make($request ->password);
            $user->subject= $request ->subject;
            $res=$user ->save();
            if($res)
            {
                return back()->with('success','You have registered successfully');
            }
            else
            {
                return back()->with('fail','Something wrong');
            }
    }

    public function loginUser(Request $request)
    {
       $request->validate(
            [
                'email'=>'required|email',
                'password'=>'required|min:5|max:15'
            ]); 
      $user = User::where('email','=',$request->email)->first();
      if($user)
      {
        if(Hash::check($request->password,$user->password))
        {
            $request->Session()->put('loginId',$user->id);
            return redirect('dashboard');
        }
        else
        {
            return back()->with('fail','Password Does Not Match');
        }
      }
      else
      {
        return back()->with('fail','This Email is not registered');
      }
    }

    public function dashboard()
    {
        $data = array();
        if(Session::has('loginId'))
        {
          $data = User::where('id','=',Session::get('loginId'))->first();  
        }
        return view('dashboard',compact('data')); 
        // return view ('dashboard');
    }

    public function logout(Request $request )
     {
        if(Session::has('loginId'))
        {
            Session::pull('loginId');
            return redirect('login');
        }
    }
}

