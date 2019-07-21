jQuery(document).ready(function(){
  jQuery('#inputGroupSelect01').on("change",function(){
    getAssociations(this.value);
  });
  var idNe = jQuery('#inputGroupSelect01').val();
  getAssociations(idNe);
});
function getAssociations(id){
  jQuery('#tableAsso').css('opacity',0.5);
  jQuery("input.formationsCheck").each(function(){
    jQuery(this).prop("checked",false);
  });
  jQuery.ajax({
      url: ajaxurl,
      method: "GET",
      dataType: "json",
      data: {
        'action': 'get_associations', f:id
      }
    }).done(function(data) {
      if (data.success) {
        console.log(data.data);
        for(e of data.data){
          jQuery("input.formationsCheck[value="+e.id_formation_situation+"]").prop("checked",true);
        }
        jQuery('#tableAsso').css('opacity',1);
      } else {
        alert("Une erreur est survenue");
        jQuery('#tableAsso').css('opacity',1);
      }
    });
}
function saveFormation(){
  jQuery('#tableAsso').css('opacity',0.5);
  var fFinal = [];
  jQuery("input.formationsCheck").each(function(){
    if(jQuery(this).prop("checked") == true){
      fFinal.push(jQuery(this).val())
    }
  });
  var idNe = jQuery('#inputGroupSelect01').val();
  jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'update_associations', f:idNe, tabs:fFinal
      }
    }).done(function(data) {
      if (data.success) {
        jQuery('#tableAsso').css('opacity',1);
      } else {
        alert("Une erreur est survenue");
        jQuery('#tableAsso').css('opacity',1);
      }
    });
}
