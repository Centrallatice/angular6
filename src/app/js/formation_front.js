jQuery(document).ready(function(){
  var idNe = jQuery('#niveau_etudes').val();
  populateSituation(idNe);
  jQuery('#niveau_etudes').on("change",function(){
    populateSituation(this.value)
  });
  jQuery('#situation').on("change",function(){
    populateDuree(this.value)
  });
  jQuery('#fc_duree_formation').on("change",function(){
    populateFormation(this.value)
  });
});
function populateSituation(id){
  jQuery('#formations_list li').remove();
  jQuery('#situation option').remove();
  jQuery("<option value='-1'>Choisissez votre situation professionnelle</option>").appendTo("#situation");
  jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'get_situation_by_ne', id:id
      }
    }).done(function(data) {
      if (data.success) {
        for(e of data.data){
          var opt = document.createElement('option');
          opt.value = e.id;
          opt.innerHTML = e.nom;
          document.getElementById('situation').appendChild(opt);
        }
      } else {
        alert("Une erreur est survenue");
      }
    });
}
function populateDuree(id){
  jQuery('#formations_list li').remove();
  jQuery('#fc_duree_formation option').remove();
  jQuery("<option value='-1'>Choisissez la durée</option>").appendTo("#fc_duree_formation");
  jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'get_duree_by_s', id:id
      }
    }).done(function(data) {
      if (data.success) {
        for(e of data.data){
          var opt = document.createElement('option');
          opt.value = e.id;
          opt.innerHTML = e.nom;
          document.getElementById('fc_duree_formation').appendChild(opt);
        }
      } else {
        alert("Une erreur est survenue");
      }
    });
}
function populateFormation(id){
  jQuery('#formations_list li').remove();
  jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'get_formation_by_duree', id:id
      }
    }).done(function(data) {
      if (data.success) {
        for(e of data.data){
          var opt = document.createElement('li');
          opt.value = e.id;
          opt.innerHTML = e.nom;
          document.getElementById('formations_list').appendChild(opt);
        }
      } else {
        alert("Une erreur est survenue");
      }
    });
}
