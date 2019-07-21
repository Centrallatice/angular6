function saveNewformation(){
  let nom = jQuery('#new-formation-nom').val();
  let url = jQuery('#new-formation-url').val();
  if(nom.length<0){
    alert('Vous devez au moins saisir un nom');
  }
  else{
    jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'add_formation', nom:nom,lien:url
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
function openEditModal(id){
  jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'get_formation', f:id
      }
    }).done(function(data) {
      if (data.success) {
        jQuery("#editModal #edit-formation-nom").val(data.data.nom);
        jQuery("#editModal #edit-formation-url").val(data.data.url);
        jQuery("#editModal #edit-formation-id").val(data.data.id);
        jQuery("#editModal").modal();
      } else {
        alert("Une erreur est survenue");
      }
    });
}
function saveEditformation(){
  let nom = jQuery('#editModal #edit-formation-nom').val();
  let url = jQuery('#editModal #edit-formation-url').val();
  let id = jQuery('#editModal #edit-formation-id').val();
  if(nom.length<0){
    alert('Vous devez au moins saisir un nom');
  }
  jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'edit_formation', f:id, nom: nom, url: url
      }
    }).done(function(data) {
      if (data.success) {
        window.location.reload();
      } else {
        alert("Une erreur est survenue");
      }
    });
}
function deleteFormation(id){
  if(confirm("Êtes-vous sur de vouloir supprimer cette formation ?")){
  jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        'action': 'del_formation', f:id
      }
    }).done(function(data) {
      if (data.success) {
        jQuery('table#mesformations tr#'+id).remove()
      } else {
        alert("Une erreur est survenue");
      }
    });
  }
}
