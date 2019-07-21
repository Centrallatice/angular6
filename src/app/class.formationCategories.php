<?php

class formationCategories {

    public  function gestion_categories()
    {
        global $wpdb;
        echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">';
        wp_enqueue_script( 'categorie-admin-js', plugins_url( '/js/categorie-admin-js.js', __FILE__ ),array(),false,true);
        wp_register_style( 'categorie-admin-css', plugins_url( '/css/categorie-admin-css.css', __FILE__ ), array(), AKISMET_VERSION );
  			wp_enqueue_style( 'categorie-admin-css');
        $reqNE = "SELECT * FROM {$wpdb->prefix}fc_niveau_etudes ORDER BY ordre ASC";
        $rowsNE = $wpdb->get_results($reqNE);
        ?>
        <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
        <div class="container-fluid container-cat">
          <div class="row">
            <div class="col-12">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="margin:25px 0 ">
                  <li class="breadcrumb-item" aria-current="page">Demandes de formations</li>
                  <li class="breadcrumb-item active" aria-current="page">Gestion des catégories</li>
                </ol>
              </nav>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="button" class="btn btn-success float-right" data-toggle="modal" data-target="#addModal">Ajouter une nouvelle catégorie <i class="fas fa-plus"></i></button><br />
            </div>
          </div>

            <?php
            $cptn=1;
            foreach($rowsNE as $rowNE):
              ?>
              <div class="row rowcat" style="margin-bottom:5px">
                <div class="col-12" id="<?php echo $rowNE->id;?>">
                  <div class="btn-group" role="group">
                    <button type="button" class="btn btn-normal btn-sm" <?php if($cptn == 1) echo 'disabled="disabled"'; ?> onclick="upCategorie(<?php echo $rowNE->id;?>,'fc_niveau_etudes')"><i style="font-size:12px" title="Remonter" class="fas fa-arrow-up"></i></button>
                    <button type="button" class="btn btn-normal btn-sm" <?php if($cptn == count($rowsNE)) echo 'disabled="disabled"'; ?>onclick="downCategorie(<?php echo $rowNE->id;?>,'fc_niveau_etudes')"><i style="font-size:12px" title="Descendre" class="fas fa-arrow-down"></i></button>
                  </div>
                  <div class="categorie_name" role="group">
                  <?php
                    echo $rowNE->nom;
                  ?>
                  </div>
                  <div class="action_boutons">
                    <a onclick="editCategorie(<?php echo $rowNE->id;?>,'fc_niveau_etudes')" style="color:blue" title="Renommer cette catégorie"><i class="fas fa-edit"></i>
                    <a onclick="deleteCategorie(<?php echo $rowNE->id;?>,'fc_niveau_etudes')"  style="color:red" title="Supprimer cette catégorie"><i class="fas fa-trash"></i></a>
                  </div>
                </div>
              </div>
              <?php
                $reqS = "SELECT * FROM {$wpdb->prefix}fc_situation WHERE niveau_etudes = ".$rowNE->id." ORDER BY ordre ASC";
                $rowsS = $wpdb->get_results($reqS);
                $cpts = 1;
                foreach($rowsS as $rowS):
                  ?>
                    <div class="row rowcat" style="padding-left:50px;margin-bottom:5px">
                      <div class="col-12" id="<?php echo $rowS->id;?>">
                        <div class="btn-group" role="group">
                          <button type="button" class="btn btn-normal btn-sm" <?php if($cpts == 1) echo 'disabled="disabled"'; ?> onclick="upCategorie(<?php echo $rowS->id;?>,'fc_situation')"><i style="font-size:12px" title="Remonter" class="fas fa-arrow-up"></i></button>
                          <button type="button" class="btn btn-normal btn-sm" <?php if($cpts == count($rowsS)) echo 'disabled="disabled"'; ?>onclick="downCategorie(<?php echo $rowS->id;?>,'fc_situation')"><i style="font-size:12px" title="Descendre" class="fas fa-arrow-down"></i></button>
                        </div>
                        <div class="categorie_name" role="group">
                        <?php
                          echo $rowS->nom;
                        ?>
                        </div>
                        <div class="action_boutons">
                          <a onclick="editCategorie(<?php echo $rowS->id;?>,'fc_situation')" style="color:blue" title="Renommer cette catégorie"><i class="fas fa-edit"></i>
                            <a onclick="deleteCategorie(<?php echo $rowS->id;?>,'fc_situation')" style="color:red" title="Supprimer cette catégorie"><i class="fas fa-trash"></i></a>
                          </div>
                      </div>
                    </div>
                  <?php
                    $reqDF = "SELECT * FROM {$wpdb->prefix}fc_duree_formation WHERE situation = ".$rowS->id." ORDER BY ordre ASC";
                    $rowsDF = $wpdb->get_results($reqDF);
                    $cpt = 1;
                    foreach($rowsDF as $rowDF):
                      ?>
                      <div class="row rowcat" style="padding-left:100px;margin-bottom:5px">
                        <div class="col-12" id="<?php echo $rowDF->id;?>">
                          <div class="btn-group" role="group">
                            <button type="button" class="btn btn-normal btn-sm" <?php if($cpt == 1) echo 'disabled="disabled"'; ?> onclick="upCategorie(<?php echo $rowDF->id;?>,'fc_duree_formation')"><i style="font-size:12px" title="Remonter" class="fas fa-arrow-up"></i></button>
                            <button type="button" class="btn btn-normal btn-sm" <?php if($cpt == count($rowsDF)) echo 'disabled="disabled"'; ?>onclick="downCategorie(<?php echo $rowDF->id;?>,'fc_duree_formation')"><i style="font-size:12px" title="Descendre" class="fas fa-arrow-down"></i></button>
                          </div>
                          <div class="categorie_name" role="group">
                          <?php
                            echo $rowDF->nom;
                          ?>
                          </div>
                          <div class="action_boutons">
                              <a onclick="editCategorie(<?php echo $rowDF->id;?>,'fc_duree_formation')" style="color:blue" title="Renommer cette catégorie"><i class="fas fa-edit"></i>
                              <a onclick="deleteCategorie(<?php echo $rowDF->id;?>,'fc_duree_formation')" style="color:red" title="Supprimer cette catégorie"><i class="fas fa-trash"></i></a>
                            </div>
                        </div>
                      </div>
                      <?php
                      $cpt++;
                    endforeach;
                    $cpts++;
                endforeach;
                $cptn++;
            endforeach;
            ?>
        </div>
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Ajout d'une nouvelle categorie</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label for="new-categorie-nom">Nom</label>
                  <input type="text" class="form-control" id="new-categorie-nom" placeholder="Entrez le nom de la nouvelle catégorie">
                </div>
                <div class="form-group">
                  <label for="new-categorie-type">Type de la catégorie</label>
                  <select class="form-control" id="new-categorie-type">
                    <option value="fc_niveau_etudes">Niveau d'études</option>
                    <option value="fc_situation">Situation professionnelle</option>
                    <option value="fc_duree_formation">Durée de la formation</option>
                  </select>
                </div>
                <div class="form-group hidden" id="fc_niveau_etudes_hide">
                  <label for="new-categorie-ne">Niveau d'études</label>
                  <select class="form-control" id="new-categorie-ne">
                    <?php
                    foreach($rowsNE as $rowNE):
                      ?>
                      <option value="<?php echo $rowNE->id;?>"><?php echo $rowNE->nom;?></option>
                    <?php
                      endforeach;
                    ?>
                  </select>
                </div>
                <div class="form-group hidden" id="fc_situation_hide">
                  <label for="new-situation-ne">Situation professionnelle associée</label>
                  <select class="form-control" id="new-situation-ne"></select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveCategorie()">Enregister cette catégorie</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="editCategorie" tabindex="-1" role="dialog" aria-labelledby="editCategorieLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editCategorieLabel">Renommer une catégorie</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label for="edit-categorie-nom">Nom</label>
                  <input type="hidden" class="form-control" id="edit-categorie-id">
                  <input type="hidden" class="form-control" id="edit-categorie-type">
                  <input type="text" class="form-control" id="edit-categorie-nom">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveEditCategorie()">Modifier cette catégorie</button>
              </div>
            </div>
          </div>
        </div>
        <?php
    }

}
