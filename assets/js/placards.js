+(function ($) {
    'use strict';

    var template = '<tr>' +
        '<td><input type="text" required="true" class="form-control input-sm" name="placard[devices][__id__][source]" /></td>' +
        '<td><input type="text" required="true" class="form-control input-sm" name="placard[devices][__id__][beamer]" /></td>' +
        '<td><input type="text" required="true" class="form-control input-sm" name="placard[devices][__id__][av]" /></td>' +
        '<td><a href="#" onclick="$(this).parent().parent().remove(); return false;" class="btn btn-danger btn-sm" role="button"><i class="fa fa-trash"></i></a></td>' +
        '</tr>';

    var regex = /placard\[devices\]\[(\d+)\]/i;

    $(document).ready(function() {
        $('a.add-placard').click(function() {
            var $collection = $('tbody.devices');

            var indices = [ ];

            var $currentElements = $('input[name^="placard[devices]"]');
            $currentElements.each(function(index, element) {
                var $element = $(this);
                var name = $element.attr('name');

                var matches = name.match(regex);

                if(matches !== null && matches.length > 1) {
                    indices.push(parseInt(matches[1]));
                }
            });

            indices.sort();
            var index = 0;

            if(indices.length > 0) {
                index = indices[indices.length - 1] + 1;
            }

            $collection.data('index', index);
            var html = template.replace(/__id__/g, $collection.data('index'));

            $collection.append($(html));
        });
    });
})(jQuery);