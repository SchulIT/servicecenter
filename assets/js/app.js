require('../css/app.scss');

var $ = require('jquery');
require('bootstrap');
require('select2');
require('chart.js');
require('emojione');

// Make jQuery available
window.$ = $;

+function($) {
    'use strict';

    $(document).ready(function() {
        $('[data-toggle=tooltip]').tooltip();

        $('select[data-trigger=navigate-to-value]').change(function(e) {
            var $select = $(this);
            var value = $select.val();

            if(value.length !== 0) {
                window.location.href = value;
            }
        });

        $('select[data-select2-enabled=true]').select2({
            theme: "bootstrap4"
        });
    });
}(jQuery);

import './editor';