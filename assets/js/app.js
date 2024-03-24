import Choices from "choices.js";

require('../css/app.scss');

import { Modal, Tooltip, Popover } from "bootstrap";
require('chart.js');

require('../../vendor/schulit/common-bundle/Resources/assets/js/polyfill');
require('../../vendor/schulit/common-bundle/Resources/assets/js/menu');
require('../../vendor/schulit/common-bundle/Resources/assets/js/dropdown-polyfill');
require('./add-problem');

let Masonry = require('masonry-layout');

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('select[data-trigger=navigate-to-value]').forEach(function(el) {
        el.addEventListener('change', function (event) {
            let value = this.value;

            if(value.length !== 0) {
                window.location.href = value;
            }
        })
    });

    let initializeChoice = function(el) {
        let removeItemButton = false;

        if(el.getAttribute('multiple') !== null) {
            removeItemButton = true;
        }

        let config = {
            itemSelectText: '',
            shouldSort: false,
            shouldSortItems: false,
            removeItemButton: removeItemButton,
            placeholder: true
        };

        el.choices = new Choices(el, config);
    };

    document.querySelectorAll('select[data-trigger=ajax]').forEach(function(el) {
        el.addEventListener('change', function (event) {
            if (el.getAttribute('data-url') === null) {
                console.error('You must specify data-url');
                return;
            }

            if (el.getAttribute('data-target') === null) {
                console.error('You must specify data-target');
                return;
            }

            let url = el.getAttribute('data-url');
            let targetSelector = el.getAttribute('data-target');
            let target = document.querySelector(targetSelector);

            if (target === null) {
                console.error('Specified target element does not exist');
                return;
            }

            let paramName = el.getAttribute('data-paramname') || 'value';

            url += '?' + paramName + '=' + el.value;

            if(el.value === undefined || el.value === null || el.value === '') {
                target.choices.destroy();
                target.setAttribute('disabled', 'disabled');
                initializeChoice(target);

                return;
            }

            let request = new XMLHttpRequest();
            request.open('GET', url, true);
            request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');

            request.onload = function () {
                if (request.status >= 200 && request.status < 400) {
                    let items = JSON.parse(request.responseText);
                    target.choices.destroy();
                    target.removeAttribute('disabled');

                    target.innerHTML = '';

                    for (let i = 0; i < items.length; i++) {
                        let option = document.createElement('option');
                        option.setAttribute('value', items[i].value);
                        option.innerText = items[i].label;

                        target.appendChild(option);
                    }

                    initializeChoice(target, items);
                }
            };

            target.setAttribute('disabled', 'disabled');
            request.send();
        });
    });

    document.querySelectorAll('select[data-choice=true]').forEach(function(el) {
        initializeChoice(el);
    });

    document.querySelectorAll('a[data-submit]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            let targetSelector = el.getAttribute('data-submit');

            console.log(targetSelector);

            if(targetSelector === null || targetSelector === '') {
                return;
            }

            if(targetSelector === 'form') {
                let form = el.closest('form');

                if(form !== null) {
                    form.submit();
                }

                return;
            }

            let target = document.querySelector(targetSelector);

            console.log(target);

            if(target !== null) {
                target.submit();
            }
        });
    });

    document.querySelectorAll('[title]').forEach(function(el) {
        new Tooltip(el, {
            placement: 'bottom'
        });
    });

});
