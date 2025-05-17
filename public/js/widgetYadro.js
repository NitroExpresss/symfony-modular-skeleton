// <?php header('Content-Type: application/javascript'); ?>
stage3ColorWidget = function() {
    const widget = this;
    this.code = null;
    
    this.applyStage3TextColors = () => {
        const $thirdTitle = $('.pipeline_cell:not(.h-hidden) .pipeline_status__head_title').eq(2);
            const style = $thirdTitle.siblings('.pipeline_status__head_line').attr('style');
            $thirdTitle.find('.block-selectable').attr('style', style).css('background', '');
    };

    // can be excess
    this.bind_actions = () => $(document).on('load', widget.applyStage3TextColors);
    this.render = () => widget.applyStage3TextColors();

    this.init = () => {};
    
    this.bootstrap = code => {
        widget.code = code;
        widget.init();
        widget.render();
        widget.bind_actions();
        $(document).on('widgets:load', widget.render);
    };
};

yadroWidget.widgets['stage3-text-color'] = new stage3ColorWidget();
yadroWidget.widgets['stage3-text-color'].bootstrap('stage3-text-color');
test_yadro_start('stage3-text-color');