import './bootstrap';
import flatpickr from "flatpickr";
import { Japanese } from "flatpickr/dist/l10n/ja.js"
import "flatpickr/dist/flatpickr.min.css";

flatpickr.localize(Japanese);
window.flatpickr = flatpickr;
