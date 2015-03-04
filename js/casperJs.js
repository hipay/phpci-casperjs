var casperJsPlugin = ActiveBuild.UiPlugin.extend({
    id: 'build-casperJs-errors',
    css: 'col-lg-6 col-md-12 col-sm-12 col-xs-12',
    title: Lang.get('casperJs'),
    lastData: null,
    displayOnUpdate: false,
    box: true,
    rendered: false,

    register: function() {
        var self = this;
        var query = ActiveBuild.registerQuery('casperJs-data', -1, {key: 'casperJs-data'})

        $(window).on('casperJs-data', function(data) {
            self.onUpdate(data);
        });

        $(window).on('build-updated', function() {
            if (!self.rendered) {
                self.displayOnUpdate = true;
                query();
            }
        });
    },

    render: function() {

        return $('<div class="table-responsive"><table class="table" id="casperJs-data">' +
            '<thead>' +
            '<tr>' +
            '   <th>'+Lang.get('test')+'</th>' +
            '</tr>' +
            '</thead><tbody></tbody></table></div>');
    },

    onUpdate: function(e) {
        if (!e.queryData) {
            $('#build-casperJs-errors').hide();
            return;
        }

        this.rendered = true;
        this.lastData = e.queryData;

        var tests = this.lastData[0].meta_value;
        var tbody = $('#casperJs-data tbody');
        tbody.empty();

        if (tests.length == 0) {
            $('#build-casperJs-errors').hide();
            return;
        }

        for (var i in tests) {

            var row = $('<tr>' +
                '<td><strong>'+tests[i].suite+'' +
                '::'+tests[i].test+'</strong><br>' +
                ''+(tests[i].message || '')+'</td>' +
                '</tr>');

            if (!tests[i].pass) {
                row.addClass('danger');
            } else {
                row.addClass('success');
            }

            tbody.append(row);
        }

        $('#build-casperJs-errors').show();
    }
});

ActiveBuild.registerPlugin(new casperJsPlugin());
