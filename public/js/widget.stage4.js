// <?php header('Content-Type: application/javascript'); ?>
stage3ColorWidget = function () {
    const widget = this;
    this.code = null;

    this.addSearchDropdownElement = () => {
        $('.js-linked-has-value').each(function () {
            const value = $(this).find('div[class^="control"] input').attr('value');

            const $tipsContainer = $(this).find('.js-tip-items');

            $('<div>', {
                class: 'tips-item js-tips-item js-cf-actions-item',
                html: '<span class="tips-icon icon icon-inline icon-search"></span>🔍 Нагуглить',
                click: function () {
                    if (value) {
                        window.open('http://letmegooglethat.com/?q=' + encodeURIComponent(value));
                        window.open('http://letmegooglethat.com/?q=' + encodeURIComponent(value));
                    }
                }
            }).appendTo($tipsContainer);
        });
    };

    // can be excess
    this.bind_actions = () => $(document).on('load', widget.addSearchDropdownElement);
    this.render = () => widget.addSearchDropdownElement();

    this.init = () => { };

    this.bootstrap = code => {
        widget.code = code;
        widget.init();
        widget.render();
        widget.bind_actions();
        $(document).on('widgets:load', widget.render);
    };
};

yadroWidget.widgets['stage4-add_search'] = new stage3ColorWidget();
yadroWidget.widgets['stage4-add_search'].bootstrap('stage4-add_search');
test_yadro_start('stage4-add_search');