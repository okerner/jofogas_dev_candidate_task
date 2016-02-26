var categories_select = '<option value="">Loading categories...</option>';

jQuery(function(){
    jQuery('ul.nav li').removeClass('active');
    jQuery('ul.nav a[href*="index.php/admin"]').parent().addClass('active');
    $.getJSON('/jofogas_dev_candidate_task/index.php/api/categories/',{},function(data){
        var categories_select = '<option value="">Please select a category</option>';
        for (cat_index in data) {
            if (data.hasOwnProperty(cat_index)) {
                categories_select+='<option value="'+data[cat_index].id+'">'+data[cat_index].cat_name+'</option>';
            }
        }
        jQuery('#categories').html(categories_select);
    });
    
    jQuery('#new_category').submit(function(event){
        event.stopPropagation();
        event.preventDefault();
        
        if (jQuery('#new_category_field').val()) {
            var cat_name = jQuery('#new_category_field').val();
            var post_data = {};
            if (jQuery('#categories option:selected').val()) post_data = {parent_id:jQuery('#categories option:selected').val(), cat_name:cat_name};
            
            jQuery.post('/jofogas_dev_candidate_task/index.php/api/create/',post_data,function(data){
                console.log(data);
            });
        }
    });
});