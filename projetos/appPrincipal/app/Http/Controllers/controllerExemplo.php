<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class controllerExemplo extends Controller
{
    public function homepage() {
        //"carregando dados do BD"
        $nome = 'Caio';
        $animais = ['titus', 'leia', 'nikita'];

        return view('home', [
            'todosAnimais' => $animais,
            'name' => $nome,
            'cachorro' => 'titus'
        ]);//similar ao load template view de APS
    }

    public function sobre(){
        return view('single-post');
    }
}
