<footer>
    @if (isset($page) && $page > 0)
        <a href="/list/p/{{ $page - 1 }}" role="button" tabindex="0">Vorherige Seite</a>
        <a href="/list/p/{{ $page + 1 }}" role="button" tabindex="0">Nächste Seite</a>
    @else
    <a href="/list/p/1" role="button" tabindex="0">NEXT</a>
    @endif
</footer>
