<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateController extends Controller
{
    public function translate(Request $request, GoogleTranslate $client)
    {
        $tr = new GoogleTranslate('es');

        $text = $tr->translate('Hello World!');
        echo $text;
        exit();
        echo $tr->getLastDetectedSource(); // Output: en
    }
}
