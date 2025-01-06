@extends('base')
@section('main')
<h1>Zeitstrahl</h1>

<article>
    <header>
        <form method="get" action="/timeline">
            <fieldset role="group">
                <label for="from">Von:</label>
                <input type="date" id="from"name="from" aria-label="Datum von" value="{{ $from }}">
                <label for="to">Bis:</label>
                <input type="date" id="to" name="to" aria-label="Darum bis" value="{{ $to }}">
                <input type="submit" value="Aktualisieren" />
            </fieldset>
        </form>
    </header>
    @if ($from > $to)
    <p class="badge">Das «Datum von» muss vor dem «Datum bis» liegen.</p>
    @endif
    <div id="chart" style="width: 100%; height: {{ ($len * 120) }}px"></div>
    <footer>
        @if ($len >= 50 && $page >= 1)
            <a href="/timeline/?page={{ $page - 1 }}&from={{ $from }}&to={{ $to }}" role="button" tabindex="1">Vorherige Seite</a>
            <a href="/timeline/?page={{ $page + 1 }}&from={{ $from }}&to={{ $to }}" role="button" tabindex="0">Nächste Seite</a>
        @elseif ($len >= 50 & $page <= 1)
            <a href="/timeline/?page={{ $page + 1 }}&from={{ $from }}&to={{ $to }}" role="button" tabindex="0">Nächste Seite</a>
        @endif
    </footer>
</article>

@if ($len > 0)
@vite('resources/js/timeline.js')
<script type="module">

    const symBirth = 'path://M86.4 5.5L61.8 47.6C58 54.1 56 61.6 56 69.2L56 72c0 22.1 17.9 40 40 40s40-17.9 40-40l0-2.8c0-7.6-2-15-5.8-21.6L105.6 5.5C103.6 2.1 100 0 96 0s-7.6 2.1-9.6 5.5zm128 0L189.8 47.6c-3.8 6.5-5.8 14-5.8 21.6l0 2.8c0 22.1 17.9 40 40 40s40-17.9 40-40l0-2.8c0-7.6-2-15-5.8-21.6L233.6 5.5C231.6 2.1 228 0 224 0s-7.6 2.1-9.6 5.5zM317.8 47.6c-3.8 6.5-5.8 14-5.8 21.6l0 2.8c0 22.1 17.9 40 40 40s40-17.9 40-40l0-2.8c0-7.6-2-15-5.8-21.6L361.6 5.5C359.6 2.1 356 0 352 0s-7.6 2.1-9.6 5.5L317.8 47.6zM128 176c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 48c-35.3 0-64 28.7-64 64l0 71c8.3 5.2 18.1 9 28.8 9c13.5 0 27.2-6.1 38.4-13.4c5.4-3.5 9.9-7.1 13-9.7c1.5-1.3 2.7-2.4 3.5-3.1c.4-.4 .7-.6 .8-.8l.1-.1s0 0 0 0s0 0 0 0s0 0 0 0s0 0 0 0c3.1-3.2 7.4-4.9 11.9-4.8s8.6 2.1 11.6 5.4c0 0 0 0 0 0s0 0 0 0l.1 .1c.1 .1 .4 .4 .7 .7c.7 .7 1.7 1.7 3.1 3c2.8 2.6 6.8 6.1 11.8 9.5c10.2 7.1 23 13.1 36.3 13.1s26.1-6 36.3-13.1c5-3.5 9-6.9 11.8-9.5c1.4-1.3 2.4-2.3 3.1-3c.3-.3 .6-.6 .7-.7l.1-.1c3-3.5 7.4-5.4 12-5.4s9 2 12 5.4l.1 .1c.1 .1 .4 .4 .7 .7c.7 .7 1.7 1.7 3.1 3c2.8 2.6 6.8 6.1 11.8 9.5c10.2 7.1 23 13.1 36.3 13.1s26.1-6 36.3-13.1c5-3.5 9-6.9 11.8-9.5c1.4-1.3 2.4-2.3 3.1-3c.3-.3 .6-.6 .7-.7l.1-.1c2.9-3.4 7.1-5.3 11.6-5.4s8.7 1.6 11.9 4.8c0 0 0 0 0 0s0 0 0 0s0 0 0 0l.1 .1c.2 .2 .4 .4 .8 .8c.8 .7 1.9 1.8 3.5 3.1c3.1 2.6 7.5 6.2 13 9.7c11.2 7.3 24.9 13.4 38.4 13.4c10.7 0 20.5-3.9 28.8-9l0-71c0-35.3-28.7-64-64-64l0-48c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 48-64 0 0-48c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 48-64 0 0-48zM448 394.6c-8.5 3.3-18.2 5.4-28.8 5.4c-22.5 0-42.4-9.9-55.8-18.6c-4.1-2.7-7.8-5.4-10.9-7.8c-2.8 2.4-6.1 5-9.8 7.5C329.8 390 310.6 400 288 400s-41.8-10-54.6-18.9c-3.5-2.4-6.7-4.9-9.4-7.2c-2.7 2.3-5.9 4.7-9.4 7.2C201.8 390 182.6 400 160 400s-41.8-10-54.6-18.9c-3.7-2.6-7-5.2-9.8-7.5c-3.1 2.4-6.8 5.1-10.9 7.8C71.2 390.1 51.3 400 28.8 400c-10.6 0-20.3-2.2-28.8-5.4L0 480c0 17.7 14.3 32 32 32l384 0c17.7 0 32-14.3 32-32l0-85.4z'
    const symDeath = 'path://M32.0106 4.0131L16.0106 4L11 16.9916L15.9779 43.9828L31.9779 44L37 17.0129L32.0106 4.0131ZM23.0002 26V17.9975L19.9999 18L19.9982 16L23.0002 15.9975V13H25.0002V15.9959L27.9982 15.9935L27.9999 17.9935L25.0002 17.9959V26L23.0002 26Z'

    const option = {
        backgroundColor: '#181c25',
        grid: {
            show: false,
            top: 50,
            right: 50,
            left: 50,
            bottom: 50
        },
        emphasis: {
            disabled: true
        },
        tooltip: {
            show: false
        },
        xAxis: {
            type: 'time',
            axisLabel: {
                fontSize: 24
            },
            splitLine: {
                show: true,
                lineStyle: {
                    color: '#aaa',
                    type: [5, 20]
                }
            },
        },
        yAxis: {
            show: false,
            type: 'category',
            axisLine: {
                show: false
            },
            axisTick: {
                show: false
            },
            axisLabel: {
                //color: '#fff',
                fontSize: 16
            }

        },
        series: [
            @foreach ($persons as $person)
            {
                type: 'line',
                animation: false,
                symbolSize: 32,
                symbolKeepAspect: true,
                lineStyle: {
                    color: '#fff',
                    width: 3
                },
                itemStyle: {
                    color: '#fff'
                },
                label: {
                    show: true,
                    position: 'bottom',
                    fontSize: 21,
                    distance: 25,
                    formatter: function(d) {
                        return d.value[0];
                      }
                },
                data: [
                    {
                        value: ['{{ $person['birth_date']['value'] }}', '{{ $person['ark']['value'] }}'],
                        label: 'Geburt',
                        symbol: symBirth,
                        symbolOffset: ['50%', '-100%']
                    },
                    {
                        value: ['{{ $person['death_date']['value'] }}', '{{ $person['ark']['value'] }}'],
                        symbol: symDeath,
                        symbolOffset: ['-50%', '-100%']
                    }
                ],
                markLine: {
                    symbol: 'none',
                    lineStyle: 'none',
                    data: [
                        [{
                            name: '{{ $person['ark']['value'] }}',
                            label: {
                                position: 'middle',
                                distance: 10,
                                fontSize: 21,
                                formatter: '{{ $person['name']['value'] }}'
                            },
                            coord: ['{{ $person['birth_date']['value'] }}', '{{ $person['ark']['value'] }}'],
                        },
                        {
                            coord: ['{{ $person['death_date']['value'] }}', '{{ $person['ark']['value'] }}'],
                        }]
                    ]
                }
            },
            @endforeach
        ]
    };

    option && timelienChart.setOption(option);

    timelienChart.on('click', (e) => {
      window.open(`/person/${e.name}`, '_self');
    });

</script>
@endif

@endsection
