<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ControllerPablo;
use App\Http\Controllers\TesteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//teste
Route::get('/teste', [TesteController::class, 'mostrar']);
Route::get('/teste2', [ControllerPablo::class, 'pablo']);

//User
Route::get('/', [UserController::class, "showCorrectHomepage"])->name('login'); //nomeia como a rota de login
Route::post('/register', [UserController::class, "register"])->middleware('guest');
Route::post('/login', [UserController::class, "login"])->middleware('guest');
Route::post('/logout', [UserController::class, "logout"])->middleware('mustBeLoggedIn');

//Blog
Route::get('/create-post', [PostController::class, 'showCreateForm'])->middleware('mustBeLoggedIn');// se não estiver autenticado manda pra rota de login
Route::get('/post/{post}', [PostController::class, 'viewSinglePost'])->middleware('mustBeLoggedIn');
Route::post('/create-post', [PostController::class, 'storeNewPost']);

//Perfil
Route::get('/profile/{user:username}', [UserController::class, 'profile']);