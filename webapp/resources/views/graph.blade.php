@extends('base')
@section('main')
<div id="chart" style="width: 100%; height: 800px;"></div>
@vite('resources/js/graph.js')
<script type="module">
const option = {
  title: {
    text: 'Basic Graph'
  },
  series: [
    {
      type: 'graph',
      layout: 'force',
      symbolSize: 50,
      roam: true,
      label: {
        show: true
      },
      edgeSymbol: ['circle', 'arrow'],
      edgeSymbolSize: [4, 10],
      edgeLabel: {
        fontSize: 20
      },
      data: [
        @foreach ($node as $n)
        {
          name: '{{ $n }}',
          //x: {{ rand(0, 100) }},
          //y: {{ rand(0, 100) }}
        },
        @endforeach
      ],
      // links: [],
      links: [
        {
          source: 0,
          target: 1,
          symbolSize: [5, 20],
          label: {
            show: true
          },
          lineStyle: {
            width: 5,
            curveness: 0.2
          }
        },
        {
          source: 'Node 2',
          target: 'Node 1',
          label: {
            show: true
          },
          lineStyle: {
            curveness: 0.2
          }
        },
        {
          source: 'Node 1',
          target: 'Node 3'
        },
        {
          source: 'Node 2',
          target: 'Node 3'
        },
        {
          source: 'Node 2',
          target: 'Node 4'
        },
        {
          source: 'Node 1',
          target: 'Node 4'
        }
      ],
      lineStyle: {
        opacity: 0.9,
        width: 2,
        curveness: 0
      }
    }
  ]
};
option && graphChart.setOption(option);
</script>
@endsection
