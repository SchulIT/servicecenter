import Choices from "choices.js";

require('../css/app.scss');

let bsn = require('bootstrap.native');
require('chart.js');
require('emojione');

require('../../vendor/schulit/common-bundle/Resources/assets/js/polyfill');
require('../../vendor/schulit/common-bundle/Resources/assets/js/menu');

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('select[data-trigger=navigate-to-value]').forEach(function(el) {
        el.addEventListener('change', function (event) {
            let value = this.value;

            if(value.length !== 0) {
                window.location.href = value;
            }
        })
    });

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

            let request = new XMLHttpRequest();
            request.open('GET', url, true);
            request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');

            request.onload = function () {
                if (request.status >= 200 && request.status < 400) {
                    let response = JSON.parse(request.responseText);
                    target.innerHTML = '';

                    for (let i = 0; i < response.length; i++) {
                        let option = document.createElement('option');
                        option.setAttribute('value', response[i].id);
                        option.innerText = response[i].name;

                        target.appendChild(option);
                    }

                    target.removeAttribute('disabled');
                }
            };

            target.setAttribute('disabled', 'disabled');
            request.send();
        });
    });

    document.querySelectorAll('select[data-choice=true]').forEach(function(el) {
        new Choices(el, {
            itemSelectText: ''
        });
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
        new bsn.Tooltip(el, {
            placement: 'bottom'
        });
    });

});
