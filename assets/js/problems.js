function onElementChanged(input) {
    let isChecked = input.checked;

    let targetSelector = input.getAttribute('data-select-target');
    let target = document.querySelector(targetSelector);

    if(target === null) {
        console.log('Empty target list');
        return;
    }

    let value = input.value;

    let currentValue = target.value;
    let currentValueAsList = [ ];

    if(currentValue.length > 0) {
        currentValueAsList = currentValue.split(',');
    }

    let valueIndex = currentValueAsList.indexOf(value);

    if(isChecked === true && valueIndex === -1) {
        currentValueAsList.push(value);
    } else if(isChecked === false && valueIndex !== -1) {
        currentValueAsList.splice(valueIndex, 1);
    }

    target.value = currentValueAsList.join(',');
}

document.addEventListener('DOMContentLoaded', function(event) {
    document.querySelectorAll('input[type=checkbox][data-toggle=select]').forEach(function(el) {
        el.addEventListener('change', function(event) {
            onElementChanged(this);
        });
    });

    document.querySelectorAll('input[type=checkbox][data-toggle=select-all]').forEach(function(el) {
        el.addEventListener('change', function(event) {
            let input = this;
            let isChecked = input.checked;

            let targetSelector = input.getAttribute('data-select-target');
            let target = document.querySelector(targetSelector);

            if(target === null) {
                console.log('Empty target list');
                return;
            }

            document.querySelectorAll('input[type=checkbox][data-select-target="' + targetSelector + '"]').forEach(function(el) {
                el.checked = isChecked;
                onElementChanged(el);
            });
        });
    });
});