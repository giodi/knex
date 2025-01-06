@extends('base')
@section('main')
<div class="grid start">
    <article class="search">
        <header>
            <h2>Suche</h2>
        </header>
        <form action="/search" method="get">
            <div role="search">
                <input name="term" type="search" placeholder="Suche" minlength="3" />
                <input type="submit" value="Search" />
            </div>
            <details>
                <summary>Eweiterte Suchoptionen</summary>
                <label>
                    <input type="checkbox" name="hasDescendant" />
                    hat Nachfahren
                </label>
                <div class="grid">
                    <label>
                        Geburtsjahr
                        <input type="number" step="1" placeholder="YYYY" name="birthYear" />
                    </label>
                    <label>
                        Geburtsmonat
                        <input type="number" step="1" placeholder="mm" min="1" max="12" name="birthMonth" />
                    </label>
                    <label>
                        Geburtstag
                        <input type="number" step="1" placeholder="dd" min="1" max="31" name="birthDay" />
                    </label>
                </div>
                    <div class="grid">
                        <label>
                            Todesjahr
                            <input type="number" step="1" placeholder="YYYY" name="deathYear" />
                        </label>
                        <label>
                            Todesmonat
                            <input type="number" step="1" placeholder="mm" min="1" max="12" name="deathMonth" />
                        </label>
                        <label>
                            Todestag
                            <input type="number" step="1" placeholder="dd" min="1" max="31" name="deathDay" />
                        </label>
                    </div>

            </details>

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
        <p>Auf der folgenden Seite, kannst du nach Deskriptoren anhand ihrer Lebensn suchen.</p>
        <a href="/timeline" role="button">Zum Zeitstrahl</a>
    </article>
    <div>
        @endsection
