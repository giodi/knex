<?php

use App\Sparql;
use App\Helpers;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Termwind\Components\Span;


Route::get('/', function () {

    return view('index');

});

Route::get('list', function () {

   $persons = Sparql::list();
   return view('list', ['persons' => $persons]);

});

Route::get('list/p/{page}', function (int $page) {

    $persons = Sparql::list($page);
    return view('list', ['persons' => $persons, 'page' => $page]);
});

Route::get('graph', function () {
    $ark = 'vx64bsvcfc5';
    $parents = Sparql::parents($ark);
    $children = Sparql::children_extended($ark);
    $node = [];
    foreach ($children as $child) {
        $node[] = $child['ark_parent']['value'];
    }

    return view('graph', ['node' => array_unique($node)]);
});


Route::get('/person/ark:36599/{id}', function (string $id) {

    $ark = "ark:36599/{$id}";
    $basic = Sparql::person($ark);
    $children = Sparql::children($id);
    $parents = Sparql::parents($id);
    $spouses = Sparql::spouses($id);
    $partner = Sparql::lifePartner($id);
    $siblings = Sparql::siblings($id);
    $links = Sparql::children_extended($id);
    $links = $links ? Helpers::buildTree($links, "ark:36599/{$id}") : null;

    return view('person', ['ark' => $ark, 'basic' => $basic[0], 'children' => $children, 'parents' => $parents, 'spouses' => $spouses, 'partner' => $partner, 'siblings' => $siblings, 'links' => $links]);
});

Route::get('/search', function (Request $request) {

    $term = $request->query('term') ?? '';

    $hasDescendant = $request->query('hasDescendant') ?? false;
    $birthYear = $request->query('birthYear') ?? false;
    $birthMonth = $request->query('birthMonth') ?? false;
    $birthDay = $request->query('birthDay') ?? false;
    $deathYear = $request->query('deathYear') ?? false;
    $deathMonth = $request->query('deathMonth') ?? false;
    $deathDay = $request->query('deathDay') ?? false;

    $options = [];
    $options['hasDescendant'] = $hasDescendant;
    $options['birthYear'] = $birthYear;
    $options['birthMonth'] = $birthMonth;
    $options['birthDay'] = $birthDay;
    $options['deathYear'] = $deathYear;
    $options['deathMonth'] = $deathMonth;
    $options['deathDay'] = $deathDay;

    $page = ($request->query('page') !== null && is_int(intval($request->query('page')))) || $request->query('page') !== null && intval($request->query('page')) === 0 ? $request->query('page') : 0;

    $persons = Sparql::search($term, $options, $page);

    if($persons && $term){
        foreach ($persons as $key => $person) {
            $persons[$key]['name']['value'] = preg_replace_callback(
                "/{$term}/i",
                fn($m) => "<mark>{$m[0]}</mark>",
                $person['name']['value']);
        }
    }



    return view('list', ['persons' => $persons, 'term' => $term]);

});

Route::get('/timeline', function (Request $request) {

    // Get random dates for $from / $to if they are not defined or invalid.
    $from = $request->query('from') && Helpers::dateValid($request->query('from')) ? $request->query('from') : date('Y-m-d', rand(-5364664448, -601347600));
    $to = $request->query('to') && Helpers::dateValid($request->query('to')) ? $request->query('to') : date('Y-m-d', strtotime($from. ' +99 years'));

    $page = ($request->query('page') !== null && is_int(intval($request->query('page')))) || $request->query('page') !== null && intval($request->query('page')) === 0 ? $request->query('page') : 0;

    $persons = Sparql::filterByBirthdate($from, $to, $page);
    $personsLen = $persons ? count($persons) : 0;

    return view('timeline', ['persons' => $persons, 'from' => $from, 'to' => $to, 'len' => $personsLen, 'page' => $page]);


});

