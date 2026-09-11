import { Tab } from "bootstrap";

let ids = {
    devices: 'problem_dto_general_group_devices',
    type: 'problem_dto_general_group_problemType'
};
let targetId = 'existing_problems';

function getQuery() {
    let query = '?';

    Object.keys(ids).forEach(function(name) {
        let element = document.getElementById(ids[name]);

        if(element.multiple === true) {
            for(let i = 0; i < element.selectedOptions.length; i++) {
                query += name + '[]=' + element.selectedOptions[i].value + '&';
            }
        } else if(element.value !== "") {
            query += name + '=' + element.value + '&';
        }
    });

    return query;
}

function registerTabs() {
    let target = document.getElementById(targetId);

    if(target === null) {
        return;
    }

    let tabsContainer = target.querySelector('#existing-problems-tab');

    if(tabsContainer !== null) {
        let tabs = tabsContainer.querySelectorAll('a.nav-link');

        for(let tab of tabs) {
            let trigger = new Tab(tab);
            tab.addEventListener('click', function (event) {
                event.preventDefault();
                trigger.show();
            })
        }
    }
}

Object.values(ids).forEach(function(id) {
    document.getElementById(id)?.addEventListener('change', function() {
        let target = document.getElementById(targetId);
        let request = new XMLHttpRequest();
        request.open('GET', this.getAttribute('data-existing-url') + getQuery(), true);

        request.onload = function () {
            if (request.status >= 200 && request.status < 400) {
                target.innerHTML = request.responseText;
                registerTabs();
            }
        };

        request.send();
    });
});

const roomSelectId = "problem_dto_general_group_room";
let $roomSelect = document.getElementById(roomSelectId);

if($roomSelect !== null) {
    let urlParams = new URLSearchParams(window.location.search);
    let room = urlParams.get('room');
    let value = null;

    if(room !== null) {
        for (let $option of $roomSelect.options) {
            if ($option.innerText.startsWith(room)) {
                value = $option.value;
            }
        }

        if(value !== null) {
            document.addEventListener('DOMContentLoaded', function() {
                $roomSelect.choices.setChoiceByValue(value);
                $roomSelect.dispatchEvent(new Event('change'));
            });
        }
    }
}

