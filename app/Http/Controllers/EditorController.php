<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EditorController extends Controller
{
    private $utilitie;

    public function __construct()
    {
        $this->utilitie = new Utilities();
    }

    public function index()
    {
        return view('editor');
    }

    /*
     * Create new client (with heberg zip)
     *
     * Err code:
     *      - 101   Parameters error (missing or invalid)
     *      - 102:  Corrupted save
     */
    public function process_save(Request $request)
    {
        $data = $request->all();
        $valid = validator($data, [
            'save' => 'required|string',
        ]);

        if ($valid->fails())
        {
            return $this->utilitie->response_handle(null, 101, $valid->errors()->first());
        }
        return $data;
    }
}
