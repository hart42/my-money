<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected $clientService;

    public function __construct(ClientService $clientService) {
        $this->clientService = $clientService;
    }

    public function createNewClient(Request $request) {
        dd('entrewi!');
        $rules = [
            'full_name' => 'required|string|max:280',
            'cpf' => 'required|int|max:11',
            'email' => 'required|email',
            'password' => 'required|string',
            'nick_name' => 'string|max:120',
        ];

        $this->validate($request, $rules);

        dd('test');
    }
}
