<?php

namespace App\Http\Controllers;

use App\Events\OurExampleEvent;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function storeAvatar(Request $request){

        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);

        $user = auth()->user();

        $filename = $user->id . '-' . uniqid() . '.jpg';

        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');
        Storage::put('public/avatars/' . $filename, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if($oldAvatar != 'fallback-avatar.jpg'){
            Storage::delete(str_replace('/storage/', 'public/', $oldAvatar));
        }

        return back()->with('success', 'sucesso!!!!!!!!!!!!!!!!!!!!');
    }
    public function showAvatarForm(){
        return view('avatar-form');
    }

    private function getSharedData(User $user){

        $currentlyFollowing = 0;

        if(auth()->check()){
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
        }

        View::share('sharedData', [
            'currentlyFollowing' => $currentlyFollowing,
            'username' => $user->username,
            'postCount' => $user->posts()->count(),
            'avatar' => $user->avatar,
            'followerCount' => $user->followers()->count(),
            'followingCount' => $user->followingTheseUsers()->count()
        ]);
    }

    public function profile(User $user){ //esta variável é um objeto usuário instanciado

        $this->getSharedData($user);

        return view('profile-posts', [
            'posts' => $user->posts()->get(),
        ]);
        //passa todos os posts, a relação foi definida na classe user
    }

    public function profileRaw(User $user){ //esta variável é um objeto usuário instanciado
        return response()->json(['theHTML' => view('profile-posts-only', ['posts' => $user->posts()->get()])->render(), 'doctitle' => $user->username . "'s profile"]);
    }

    public function profileFollowers(User $user){ //esta variável é um objeto usuário instanciado

        $this->getSharedData($user);

        return view('profile-followers', [
            'followers' => $user->followers()->latest()->get()
        ]);
        //passa todos os posts, a relação foi definida na classe user
    }

    public function profileFollowersRaw(User $user){ //esta variável é um objeto usuário instanciado
        return response()->json(['theHTML' => view('profile-followers-only', ['followers' => $user->followers()->latest()->get()])->render(), 'doctitle' => $user->username . "'s followers"]);
    }
    
    public function profileFollowing(User $user){ //esta variável é um objeto usuário instanciado
        
        $this->getSharedData($user);
        
        return view('profile-following', [
            'following' => $user->followingTheseUsers()->latest()->get()
        ]);
        //passa todos os posts, a relação foi definida na classe user
    }
    
    public function profileFollowingRaw(User $user){ //esta variável é um objeto usuário instanciado
        return response()->json(['theHTML' => view('profile-following-only', ['following' => $user->followingTheseUsers()->latest()->get()])->render(), 'doctitle' => 'Who ' . $user->username . "follows"]);
    }

    public function logout(){
        event(new OurExampleEvent(['username' => auth()->user()->username,  'action' => 'logout']));
        auth()->logout();
        return redirect('/')->with('success', 'saiu com sucesso. seu merda');
    }

    public function showCorrectHomepage(){
        if (auth()->check()) {
            return view('homepage-feed', ['posts' => auth()->user()->feedPosts()->latest()->paginate(4)]);
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
            event(new OurExampleEvent(['username' => auth()->user()->username,  'action' => 'login']));
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
