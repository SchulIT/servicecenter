let template =
    '<td><input type="text" required="true" class="form-control input-sm" name="placard[devices][__id__][source]" /></td>' +
    '<td><input type="text" required="true" class="form-control input-sm" name="placard[devices][__id__][beamer]" /></td>' +
    '<td><input type="text" required="true" class="form-control input-sm" name="placard[devices][__id__][av]" /></td>' +
    '<td><a href="#" onclick="this.parentNode.parentNode.remove(); return false;" class="btn btn-danger btn-sm" role="button"><i class="fa fa-trash"></i></a></td>';

let regex = /placard\[devices\]\[(\d+)\]/i;

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a.add-placard').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();

            let collection = document.querySelector('tbody.devices');

            let indices = [];

            document.querySelectorAll('input[name^="placard[devices]"]').forEach(function(el) {
                let name = el.getAttribute('name');
                let matches = name.match(regex);

                if (matches !== null && matches.length > 1) {
                    indices.push(parseInt(matches[1]));
                }
            });

            indices.sort();
            let index = 0;

            if (indices.length > 0) {
                index = indices[indices.length - 1] + 1;
            }

            collection.setAttribute('data-index', index);
            let element = document.createElement('tr');
            element.innerHTML = template.replace(/__id__/g, collection.getAttribute('data-index'));

            collection.appendChild(element);
        });
    });
});