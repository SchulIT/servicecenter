import Choices from "choices.js";

require('../css/app.scss');

require('bootstrap.native');
require('chart.js');
require('emojione');

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

});
