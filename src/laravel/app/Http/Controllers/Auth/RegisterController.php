<?php

namespace App\Http\Controllers\Auth;

use App\model\G_Login;
use App\Mail\Email;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Dao;
use App;
use Utility;

class RegisterController extends Controller
{
   /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

   use RegistersUsers;

   /**
    * Where to redirect users after registration.
    *
    * @var string
    */
   protected $redirectTo = '/preregisterdone';

   /**
    * Create a new controller instance.
    *
    * @return void
    */
   public function __construct()
   {
      $this->middleware('guest');
   }

   /**
    * Get a validator for an incoming registration request.
    *
    * @param  array  $data
    * @return \Illuminate\Contracts\Validation\Validator
    */
   protected function validator(array $data)
   {
      return Validator::make($data, [
         'email' => 'required|string|email|max:255|unique:G_Login',
         'password' => 'required|string|regex:/^[!-~]+$/|min:8|max:12|confirmed',
      ]);
   }

   /**
    * Create a new user instance after a valid registration.
    *
    * @param  array  $data
    * @return \App\User
    */
   protected function create(array $data)
   {
      $dataFormat = new Utility\DataFormat;
      $token = $dataFormat->randGet(18, "allAlphabet", "back");

      
      $now = \Carbon\Carbon::now();
      $login = G_Login::create([
         'email' => $data['email'],
         'password' => bcrypt($data['password']),
         'accountStatus' => 0,
         'email_token' => $token,
         'createDay' => $now,
         'updateDay' => $now
      ]);

      $email = new Email($login);
      \Mail::to($login->email)->send($email);

      return $login;
   }

   public function preRegisterDone()
   {

      return view('auth.preRegisterDone');
   }

   public function preRegisterResult($token)
   {

      $g_loginDao = new Dao\G_LoginDao();

      $g_loginInfo = $g_loginDao->getG_LoginTockenCheck($token);

      if (!empty($g_loginInfo)) {

         return view('auth.registerDone')->with(['email' => $g_loginInfo->email, 'token' => $g_loginInfo->email_token]);
      } else {
      }
   }
}
