import './bootstrap';
import SlimSelect from 'slim-select';
import 'slim-select/dist/slimselect.css';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import Chart from 'chart.js/auto';
import html2pdf from 'html2pdf.js';

window.SlimSelect = SlimSelect;
window.flatpickr = flatpickr;
window.dispatchEvent(new Event('flatpickr-ready'));
window.Chart = Chart;
window.html2pdf = html2pdf;
