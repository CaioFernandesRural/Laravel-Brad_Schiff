<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function profile(User $user){ //esta variável é um objeto usuário instanciado
        return view('profile-posts', [
            'username' => $user->username,
            'posts' => $user->posts()->get(),
            'postCount' => $user->posts()->count()
        ]);
        //passa todos os posts, a relação foi definida na classe user
    }

    public function logout(){
        auth()->logout();
        return redirect('/')->with('success', 'saiu com sucesso. seu merda');
    }

    public function showCorrectHomepage(){
        if (auth()->check()) {
            return view('homepage-feed');
        } else {
            return view('home');
        }
    }

    public function login(Request $request){
        $incomingFields = $request->validate([
            'loginusername' => ['required'],
            'loginpassword' => ['required']
        ]);

        if (auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'Login bem sucedido');
        } else {
            return redirect('/')->with('failure', 'login inválido.');
        }
        
    }
    public function register(Request $request) {
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('success', 'Obrigado pelo cadastro');
    }
}
