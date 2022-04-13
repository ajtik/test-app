/* global document */
import naja from 'naja';
import netteForms from 'nette-forms';
import 'bootstrap';

naja.formsHandler.netteForms = netteForms;
document.addEventListener('DOMContentLoaded', () => naja.initialize());
