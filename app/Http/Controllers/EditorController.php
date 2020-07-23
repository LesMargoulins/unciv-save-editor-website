<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use mysql_xdevapi\Exception;

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
     * Handle save
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
            return Redirect::back()->withErrors(['Save is required.']);
        }
        $raw = base64_decode($data['save'], true);
        if (!$raw)
        {
            return Redirect::back()->withErrors(['Corrupted or incomplete save.']);
        }
        try
        {
            $raw = gzdecode($raw);
        }
        catch (\Exception $exception)
        {
            return Redirect::back()->withErrors(['Corrupted or incomplete save.']);
        }
        $raw = $this->utilitie->fix_json_key($raw);
        $raw = $this->utilitie->fix_json_string($raw);
        $save = json_decode($raw, true);
        if ($save)
        {
            return 'Still wip my friend, come again in few weeks';
            $request->session()->push('game', $save);
        }
        return Redirect::back()->withErrors(['Corrupted or incomplete save.']);
    }
}
