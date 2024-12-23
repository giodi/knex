@extends('base')
@section('main')
<article>
    <header>
        <h1>{{ $basic['name']['value'] }}</h1>
    </header>
    <div class="grid">
        @if (isset($basic['desc']))
        <div>
            <p style="hyphens: auto;hyphenate-limit-chars: auto 3; hyphenate-limit-lines: 4;">
                {{ $basic['desc']['value'] }}
            </p>
            <div>
            <h2>Weblinks</h2>
            <div id="metagrid-widget">
                <span aria-busy="true">Links werden gesucht</span>
            </div>
        </div>
        </div>
        @endif
        <div>
            <dl>
                <dt>Geschlecht (biologisch)</dt>
                <dd>{{ $basic['sex']['value'] }}</dd>
                @if (isset($basic['birth_date']))
                <dt>Geburtsdatum</dt>
                <dd>{{ $basic['birth_date']['value'] }}</dd>
                @endif
                @if (isset($basic['baptism_date']))
                <dt>Taufdatum</dt>
                <dd>{{ $basic['baptism_date']['value'] }}</dd>
                @endif
                @if (isset($basic['death_date']))
                <dt>Todesdatum</dt>
                <dd>{{ $basic['death_date']['value'] }}</dd>
                @endif
                @if (isset($basic['burial_date']))
                <dt>Begräbnisdatum</dt>
                <dd>{{ $basic['burial_date']['value'] }}</dd>
                @endif
                @if ($parents)
                <dt>Eltern</dt>
                @foreach ($parents as $child)
                <dd><a href="/person/{{ $child['ark']['value'] }}">{{ $child['name']['value'] }}</a></dd>
                @endforeach
                @endif
                @if ($siblings)
                <dt>Geschwister</dt>
                @foreach ($siblings as $sibling)
                <dd><a href="/person/{{ $sibling['ark']['value'] }}">{{ $sibling['name']['value'] }}</a></dd>
                @endforeach
                @endif
                @if ($spouses)
                <dt>Ehepartner</dt>
                @foreach ($spouses as $spouse)
                <dd><a href="/person/{{ $spouse['ark']['value'] }}">{{ $spouse['name']['value'] }}</a></dd>
                @endforeach
                @endif
                @if ($children)
                <dt>Kinder</dt>
                @foreach ($children as $child)
                <dd><a href="/person/{{ $child['ark']['value'] }}">{{ $child['name']['value'] }}</a></dd>
                @endforeach
                @endif
            </dl>

            <ul>
                <li><a title="Verzeichnungseinheiten zu «{{ $basic['name']['value'] }}» im Archivkatalog der Burgerbibliothek Bern."
                        href="https://katalog.burgerbib.ch/resultatliste.aspx?deskriptorId={{ $basic['scope_id']['value'] }}">
                        Verzeichnungseinheiten
                    </a></li>
                <li><a href="/timeline?from={{ $basic['birth_date']['value'] }}">
                        Zeitstrahl
                    </a></li>
            </ul>



        </div>

    </div>
</article>

@if ($links)
<article>
    <header>Nachkommenschaft</header>
    <div id="tree-chart" style="height: 1000px"></div>
</article>

@vite('resources/js/treechart.js')
<script type="module">
const option = {
  series: {
    type: 'sunburst',
    nodeClick: false,
    data: [{
        name: '{{ $basic['name']['value'] }}',
        ark: '{{ $ark }}',
        children: {!! json_encode($links) !!}
    }],
    emphasis: {
        focus: 'ancestor',
    },
    radius: [0, '90%'],
    label: {
      rotate: 'tangential'
    }
  }
};

option && treeChart.setOption(option);

treeChart.on('click', (e) => {
  window.open(`/person/${e.data.ark}`, '_self');
});
</script>
@endif

<template id="metagrid-template">
    <div>
        <ul></ul>
        <p>
            <small>Links via <a href="https://www.metagrid.ch/">Metagrid</a> – die Vernetzungsinitiative der
                SAGW</small>
        </p>
    </div>
</template>
<script>
    async function metagridWidget() {

        let response;
        const metagridWidget = document.getElementById('metagrid-widget');
        const metagridTemplate = document.getElementById('metagrid-template');
        const clone = metagridTemplate.content.cloneNode(true);

        try {
            response = await fetch('https://api.metagrid.ch/widget/burgerbibliothek/person/{{ $basic['scope_id']['value'] }}.json');
        } catch (error) {
            console.log('Fehler:', error);
        }

        if (await response?.ok) {

            let r = await response.json();
            const list = clone.querySelector('ul');

            for (const [key, value] of Object.entries(r[0])) {
                let li = document.createElement('li');
                li.innerHTML = `<a href="${value.url}" data-tooltip="${value.short_description}">${key}</a>`
                list.appendChild(li)
            }

            metagridWidget.replaceWith(clone);

        } else {
            let noLinks = document.createElement('p');
            noLinks.innerText = 'Keine Links vorhanden.';
            noLinks.classList.add('badge');

            metagridWidget.replaceWith(noLinks);
        }

    }

    metagridWidget();
</script>
@endsection
