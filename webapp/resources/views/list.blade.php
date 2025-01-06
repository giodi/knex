@extends('base')
@section('main')
<article>
    <header>
        @if (isset($term))
        <h1>Suchresultat für «{{ $term }}»</h1>
        @else
        <h1>Deskriptoren</h1>
        @endif
    </header>
    @if ($persons)
    <ul class="list">

        @foreach ($persons as $person)
        <li>
            <a href="/person/{{ $person['ark']['value'] }}">
                <span>
                    {!! $person['name']['value'] !!}
                </span>
                <span>
                    {{ $person['birth_date']['value'] }} &mdash; {{ $person['death_date']['value'] }}
                </span>
            </a>
        </li>
        @endforeach
    </ul>
    @else
        <p>Keine Einträge gefunden</p>
    @endif
    @if (!isset($term))
        @include('list-footer')
    @endif
</article>


@endsection
