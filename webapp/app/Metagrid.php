<?php

namespace App;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class Metagrid{

    /**
     * Get data from metagrid
     * @param int $id Scope-ID
     */
    public static function widget(int $id)
    {
        $request = Http::get("https://api.metagrid.ch/widget/burgerbibliothek/person/{$id}");

        if($request->successful()){
            return $request->json();
        }

        return false;

    }
}
