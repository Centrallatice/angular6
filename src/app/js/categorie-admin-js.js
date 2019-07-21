jQuery(document).ready(function(){
  jQuery('#new-categorie-type').on("change",function(){
    if(this.value === "fc_situation"){
      jQuery('#fc_niveau_etudes_hide').removeClass("hidden");
    }
    else if(this.value === "fc_duree_formation"){
      jQuery('#fc_niveau_etudes_hide').removeClass("hidden");
      jQuery('#fc_situation_hide').removeClass("hidden");
    }
    else{
      jQuery('#fc_niveau_etudes_hide').addClass("hidden");
      jQuery('#fc_situation_hide').addClass("hidden");
    }
  });
  var idNe = jQuery('#new-categorie-ne').val();
  populateSituation(idNe);
  jQuery('#new-categorie-ne').on("change",function(){
    populateSituation(this.value)
  });
});
function saveCategorie(){
  var type = jQuery('#new-categorie-type').val();
  var niveauEtude = jQuery('#new-categorie-ne').val();
  var situation = jQuery('#new-situation-ne').val();
  var nom = jQuery('#new-categorie-nom').val();
  if(nom === ''){
    alert("Veuillez saisir un nom");
  } else {
    jQuery.ajax({
        url: ajaxurl,
        method: "POST",
        dataType: "json",
        data: {
          'action': 'add_new_categorie', type:type, ne: niveauEtude, s: situation, nom: nom
        }
      }).done(function(data) {
        if (data.success) {
          window.location.reload();
        } else {
          alert("Une erreur est survenue");
        }
      });
  }
}
function populateSituation(id){
  jQuery('#new-situation-ne option').remove();
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
          document.getElementById('new-situation-ne').appendChild(opt);
        }
      } else {
        alert("Une erreur est survenue");
      }
    });
}
function editCategorie(id,t){
  jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'get_categorie', c:id, type:t
      }
    }).done(function(data) {
      if (data.success) {
        jQuery("#editCategorie #edit-categorie-nom").val(data.data.nom);
        jQuery("#editCategorie #edit-categorie-id").val(data.data.id);
        jQuery("#editCategorie #edit-categorie-type").val(data.type);
        jQuery("#editCategorie").modal();
      } else {
        alert("Une erreur est survenue");
      }
    });
}
function saveEditCategorie(){
  let nom = jQuery('#editCategorie #edit-categorie-nom').val();
  let id = jQuery('#editCategorie #edit-categorie-id').val();
  let type = jQuery('#editCategorie #edit-categorie-type').val();
  if(nom.length<0){
    alert('Vous devez au moins saisir un nom');
  }
  jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'edit_categorie_nom', c:id, nom: nom, t: type
      }
    }).done(function(data) {
      if (data.success) {
        window.location.reload();
      } else {
        alert("Une erreur est survenue");
      }
    });
}
function upCategorie(id,type){
  jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'position_categorie', c:id, sens: 'up',t: type
      }
    }).done(function(data) {
      if (data.success) {
        window.location.reload();
      } else {
        alert("Une erreur est survenue");
      }
    });
}
function downCategorie(id,type){
  jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'position_categorie', c:id, sens: 'down', t:type
      }
    }).done(function(data) {
      if (data.success) {
        window.location.reload();
      } else {
        alert("Une erreur est survenue");
      }
    });
}
function deleteCategorie(id, type){
  if(confirm("Êtes-vous sur de vouloir supprimer cette catégorie et tout les éléments associés ?")){
  jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'del_categorie', c:id, type:type
      }
    }).done(function(data) {
      if (data.success) {
        jQuery('div#'+id).remove()
      } else {
        alert("Une erreur est survenue");
      }
    });
  }
}
