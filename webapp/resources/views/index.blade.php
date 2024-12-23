@extends('base')
@section('main')
<div class="grid">
    <article>
        <header>
            <h2>Suche</h2>
        </header>
        <form action="/search" method="get">
            <div role="search">
                <input name="term" type="search" placeholder="Suche" minlength="3" />
                <input type="submit" value="Search" />
            </div>
            <fieldset>
                <legend>Eweiterte Suchoptionen:</legend>
                <label>
                    <input type="checkbox" name="hasDescendant" />
                    hat Nachfahren
                </label>
            </fieldset>

        </form>
    </article>
    <article>
        <header>
            <h2>Liste</h2>
        </header>
        <p>Unter der folgenden Seite findest du alle Deskriptoren, alphabetisch sortiert.</p>
        <a href="/list" role="button">Zur Liste</a>
    </article>
    <article>
        <header>
            <h2>Zeitstrahl</h2>
        </header>
        <p>Auf der folgenden Seite, kannst du nach Deskriptoren anhand ihrer Lebensdaten suchen.</p>
        <a href="/timeline" role="button">Zum Zeitstrahl</a>
    </article>
    <div>
        @endsection
