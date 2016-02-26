var categories_select = '<option value="">Please select a category</option>';

function getChildren(data,parent,level){
    level+=1;
    for (cat_index in data) {
        if (data.hasOwnProperty(cat_index) && data[cat_index].parent_id!=null && data[cat_index].parent_id==parent.id) {
            var spaces='';
            for(var i=1;i<=level;i+=1) spaces+='&nbsp;&nbsp;';
            categories_select+='<option value="'+data[cat_index].cat_name+'">'+spaces+data[cat_index].cat_name+'</option>';
            getChildren(data,data[cat_index],level);
        }
    }
}
jQuery(function(){
    $.getJSON('/jofogas_dev_candidate_task/index.php/api/categories/',{},function(data){
        for (cat_index in data) {
            if (data.hasOwnProperty(cat_index) && data[cat_index].parent_id==null) {
                categories_select+='<option value="'+data[cat_index].cat_name+'">'+data[cat_index].cat_name+'</option>';
                getChildren(data,data[cat_index],0);
            }
        }
        jQuery('#keywords').html(categories_select);
    });
    
    jQuery('#search_form').submit(function(event){
        event.stopPropagation();
        event.preventDefault();
        
        var search_elements = [];
        
        if (jQuery('#searchtext').val()) search_elements.push(jQuery('#searchtext').val());
        if (jQuery('#keywords option:selected').val()) search_elements.push(jQuery('#keywords option:selected').val());
        
        $.getJSON('/jofogas_dev_candidate_task/index.php/api/search/'+encodeURI(search_elements.join('+')),{},function(data){
            console.log(data);
            
            var images_html = '';
            
            for (image_index in data.photos) {
                if (data.photos.hasOwnProperty(image_index)) {
                    images_html+='<div class="flickr_image"><img src="'+data.photos[image_index].url+'"><span class="image_title">'+data.photos[image_index].title+'</span></div>';
                }
            }
            jQuery('#search_result').html(images_html);
     });
    });
});