(function($){
    window.vc = window.vc || {};
    vc.atts = vc.atts || {};
    vc.atts.checkbox_search_posts = {
        parse: function(param) {
            var checked = [];
            this.content().find('input[type=checkbox]:checked').each(function(){
                checked.push($(this).val());
            });
            return checked.join(',');
        }
    };
    vc.custom_checkbox_search_posts = function($field, param_name, values, options) {
        var html = '<input type="text" class="vc-checkbox-search" placeholder="Search posts..." style="width:100%;margin-bottom:8px;padding:4px 8px;">';
        html += '<div class="vc-checkbox-list" style="max-height:220px;overflow:auto;border:1px solid #eee;padding:6px 0;">';
        for(var i=0; i<options.length; i++) {
            var checked = values && values.indexOf(options[i].value) !== -1 ? 'checked' : '';
            html += '<label style="display:block;padding:2px 10px;cursor:pointer;font-size:13px;">';
            html += '<input type="checkbox" value="'+options[i].value+'" '+checked+'> '+options[i].label;
            html += '</label>';
        }
        html += '</div>';
        $field.html(html);
        $field.find('.vc-checkbox-search').on('input', function(){
            var val = $(this).val().toLowerCase();
            $field.find('.vc-checkbox-list label').each(function(){
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(val) !== -1);
            });
        });
        $field.find('.vc-checkbox-list input[type=checkbox]').on('change', function() {
            var checked = [];
            $field.find('input[type=checkbox]:checked').each(function(){
                checked.push($(this).val());
            });
            console.log('Checkbox changed, selected:', checked);
            // Set value on the hidden input so VC saves it
            $field.closest('.wpb_el_type_checkbox_search_posts').find('.wpb_vc_param_value').val(checked.join(',')).trigger('change');
        });
    };
    $(document).on('vcParamAdd', function(e, param){
        if(param && param.param_type === 'checkbox_search_posts'){
            var $field = param.$field;
            var param_name = param.param_name;
            // Fetch the value from the hidden input inside the field if available, otherwise use param.value
            var hiddenVal = $field.find('.wpb_vc_param_value').val();
            var values = hiddenVal ? hiddenVal.split(',') : (param.value ? param.value.split(',') : []);
            var options = param.options || [];
            vc.custom_checkbox_search_posts($field, param_name, values, options);
        }
    });
})(window.jQuery); 