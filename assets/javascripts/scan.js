/**
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @portedToBootstrap5 Cristian Garcia Copete <cristian@demondog.es>
 */

$(document).ready(function () {
    scan.init();
});

/**
 * @type {object}
 */
var scan = {
    object: null,
    checked: false,
    init: function () {
        $('body').on('click', 'button#select-all', $.proxy(function () {
            this.toggleChecked();
        }, this));
        $('body').on('click', 'button#delete-selected', $.proxy(function () {
            if (window.confirm(ddt.t('Are you sure you want to delete these items?'))) {
                this.deleteSelected();
            }
        }, this));
        $('body').on('click', 'a.delete-item', $.proxy(function (event) {
            if (window.confirm(ddt.t('Are you sure you want to delete this item?'))) {
                this.deleteItem($(event.currentTarget));
            }
            return false;
        }, this));
    },
    toggleChecked: function () {
        this.checked = !this.checked;
        $('#delete-source').find('input.language-source-cb').prop("checked", this.checked);
    },
    deleteSelected: function () {
        var $ids = [];
        this.object = $('#delete-source').find('input.language-source-cb:checked');
        this.object.each(function () {
            $ids.push($(this).val());
        });
        this.delete($ids);
    },
    deleteItem: function ($object) {
        this.object = $object;
        var $ids = [];
        $ids.push(this.object.data('id'));
        this.delete($ids);
    },
    delete: function ($ids) {
        if ($ids.length) {
            $.post('/translateManager/language/delete-source', {ids: $ids}, $.proxy(function () {
                this.remove();
            }, this));
        }
    },
    remove: function () {
        this.object.closest('tr').remove();
        var text = $('#w2-danger').text();
        var pattern = /\d+/g;
        var number = pattern.exec(text);
        $('#w2-danger').text(text.replace(number, $('#delete-source').find('tbody').find('tr').length));
    }
};

