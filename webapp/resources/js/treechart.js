import * as echarts from 'echarts';

const tree = document.getElementById('tree-chart');
const treeChart = echarts.init(tree);
window.treeChart = treeChart;
