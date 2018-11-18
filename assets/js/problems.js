+function($) {
    'use strict';

    function onElementChanged($input) {
        var isChecked = $input.is(':checked');

        var target = $input.attr('data-select-target');
        var $target = $(target);

        if($target.length === 0) {
            console.log('Empty target list');
            return;
        }

        var value = $input.val();

        var currentValue = $target.val();
        var currentValueAsList = [ ];

        if(currentValue.length > 0) {
            currentValueAsList = currentValue.split(',');
        }

        var valueIndex = currentValueAsList.indexOf(value);

        if(isChecked === true && valueIndex === -1) {
            currentValueAsList.push(value);
        } else if(isChecked === false && valueIndex !== -1) {
            currentValueAsList.splice(valueIndex, 1);
        }

        $target.val(currentValueAsList.join(','));
    };

    $(document).ready(function() {
        $('input[type=checkbox][data-toggle=select]').change(function() {
            var $input = $(this);
            onElementChanged($input);
        });

        $('input[type=checkbox][data-toggle=select-all]').change(function() {
            var $input = $(this);
            var isChecked = $input.is(':checked');

            var target = $input.attr('data-select-target');
            var $target = $(target);

            if($target.length === 0) {
                console.log('Empty target list');
                return;
            }

            var $elements = $('input[type=checkbox][data-select-target="' + target + '"]');
            $elements.each(function() {
                var $element = $(this);
                $element.prop('checked', isChecked);
                onElementChanged($element);
            });
        });
    });
}(jQuery);