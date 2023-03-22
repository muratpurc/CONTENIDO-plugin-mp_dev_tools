/**
 * JavaScript for CONTENIDO plugin "Mp Dev Tools".
 *
 * Some functionalities are borrowed from the CONTENIDO modules
 * `Article List Reloaded` and `Terminliste v3`.
 */
(function(Con, $) {

    // Tasks to do on document ready
    $(function() {

        // Get all fieldset table legends
        var $fieldsetTableLegends = $('.mp_dev_tools_fieldset_table legend.mp_dev_tools_show_more'),
            // Get the template configuration form
            $templateConfigurationForm = $('#tpl_form');

        // Initial collapse of all fieldset table contents
        $fieldsetTableLegends.each(function(pos, item) {
            $(item).next('div').hide().css('height', 'auto').slideUp();
            $(item).data('state', '0');
        });

        /**
         * Action to submit the template configuration form with the "send" field set to "0".
         *
         * @returns {boolean}
         */
        function actionSubmit() {
            $templateConfigurationForm.find('[name="send"]').val("0");
            $templateConfigurationForm.submit();
            return false;
        }

        /**
         * Toggles (expands & collapses) the fieldset table content,
         * but only if the appropriate css class name is set.
         *
         * @param {jQuery} $element
         */
        function actionToggleFieldSetTable($element) {
            if (!$element.hasClass('mp_dev_tools_show_more')) {
                return;
            }
            var $icon = $element.find('span').first(),
                $content = $element.next('div'),
                state = $element.data('state') === '0' ? '1' : '0';

            $element.data('state', state);
            $content.slideToggle(state === '1');
            $element.toggleClass('active', state === '1');

            if (state === '1') {
                $icon.attr('class', 'mp_dev_tools_more_button2')
                    .html('&equiv;');
            } else {
                $icon.attr('class', 'mp_dev_tools_more_button')
                    .html('&raquo;');
            }
        }

        /**
         * Takes over values of a form element (i.e. multiple select)
         * to another form element (i.e. hidden field).
         * Values of type array will be taken over as a string, where
         * the values are comma separated.
         *
         * @param {jQuery} $element
         */
        function actionTakeoverMultipleValues($element) {
            var $destination = $('#' + $element.data('field-id')),
                val = $element.val();

            if ($.type(val) === 'array') {
                val = val.toString();
            }
            $destination.val(val);
            console.log($element.data('field-id'), $element.val(), $destination.val());
        }

        // Click handler
        $('[data-mp_dev_tools-action]').live('click', function() {
            var $element = $(this)
                action = $element.data('mp_dev_tools-action');

            if (action === 'mp_dev_tools_submit') {
                return actionSubmit();
            } else if (action === 'mp_dev_tools_toggle_fieldset_table') {
                actionToggleFieldSetTable($element);
            }
        });

        // Change handler
        $('[data-mp_dev_tools-action-change]').live('change', function() {
            var $element = $(this)
                action = $element.data('mp_dev_tools-action-change');

            if (action === 'takeover_multiple_values') {
                actionTakeoverMultipleValues($element);
            }
        });

        // Initialize tabs for \CONTENIDO\Plugin\MpDevTools\Gui\Tabs
        $('.mp_dev_tools_tabs').tabs();
    });

})(Con, Con.$);
