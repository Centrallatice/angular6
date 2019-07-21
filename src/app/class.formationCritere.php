<?php
require('class.formationCategories.php');
class formationCritere {
    private static $initiated = false;

    public static function init() {
  		if ( ! self::$initiated ) {
  			self::init_hooks();
  		}
  	}

  	/**
  	 * Initializes WordPress hooks
  	 */
  	private static function init_hooks() {
  		self::$initiated = true;
  		add_action('admin_menu', array('formationCritere', 'add_admin_menu'));
      add_shortcode('form_choix_formation', array('formationCritere', 'form_html'));
      add_action('wp_ajax_add_formation', array('formationCritere', 'add_formation'));
      add_action('wp_ajax_del_formation', array('formationCritere', 'del_formation'));
      add_action('wp_ajax_get_formation', array('formationCritere', 'get_formation'));
      add_action('wp_ajax_get_categorie', array('formationCritere', 'get_categorie'));
      add_action('wp_ajax_edit_formation', array('formationCritere', 'edit_formation'));
      add_action('wp_ajax_position_categorie', array('formationCritere', 'update_position_categorie'));
      add_action('wp_ajax_get_situation_by_ne', array('formationCritere', 'get_situation_by_ne'));
      add_action('wp_ajax_nopriv_get_situation_by_ne', array('formationCritere', 'get_situation_by_ne'));
      add_action('wp_ajax_nopriv_get_duree_by_s', array('formationCritere', 'get_duree_by_s'));
      add_action('wp_ajax_nopriv_get_formation_by_duree', array('formationCritere', 'get_formation_by_duree'));
      add_action('wp_ajax_add_new_categorie', array('formationCritere', 'add_new_categorie'));
      add_action('wp_ajax_edit_categorie_nom', array('formationCritere', 'edit_categorie_nom'));
      add_action('wp_ajax_del_categorie', array('formationCritere', 'del_categorie'));
      add_action('wp_ajax_get_associations', array('formationCritere', 'get_associations'));
      add_action('wp_ajax_update_associations', array('formationCritere', 'update_associations'));
  	}

    public static function add_admin_menu()
    {
        add_menu_page('Demandes', 'Formations', 'manage_options', 'formationCritere', array('formationCritere', 'associations_formations'));
        add_submenu_page('formationCritere', 'Gestion des formations', 'Gestion des formations', 'manage_options', 'formationCritere_gestionformations', array('formationCritere', 'gestion_formations'));
        add_submenu_page('formationCritere', 'Gestion des catégories', 'Gestion des catégories', 'manage_options', 'formationCritere_gestioncategories', array('formationCategories', 'gestion_categories'));
    }
    public  function associations_formations(){
      global $wpdb;
      echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">';
      wp_enqueue_script( 'association-admin-js', plugins_url( '/js/association-admin-js.js', __FILE__ ),array(),false,true);
      $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fc_formations WHERE deleted_at IS NULL ORDER BY nom ASC");
      ?>
      <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb" style="margin:25px 0 ">
                <li class="breadcrumb-item" aria-current="page">Demandes de formations</li>
                <li class="breadcrumb-item active" aria-current="page">Association des formations</li>
              </ol>
            </nav>
          </div>
        </div>
        <div class="row" style="margin-top:25px">
          <div class="col-12">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <label class="input-group-text" for="inputGroupSelect01">Formation</label>
              </div>
              <select name="formation" style="height:40px" class="custom-select" id="inputGroupSelect01">
                <?php
                foreach($rows as $row):
                  ?>
                    <option value="<?php echo $row->id;?>"><?php echo $row->nom;?></option>
                  <?php
                endforeach;
                ?>
              </select>
            </div>
          </div>
        </div>
        <div class="row" style="margin-top:25px">
          <div class="col-10">
            <table class="table table-sm" id="tableAsso">
              <thead>
                <tr>
                  <th scope="col">Catégorie</th>
                  <th scope="col">Associer</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $reqNE = "SELECT * FROM {$wpdb->prefix}fc_niveau_etudes ORDER BY ordre ASC";
                  $rowsNE = $wpdb->get_results($reqNE);
                  foreach($rowsNE as $rowNE):
                    ?>
                    <tr>
                      <td><?php echo $rowNE->nom;?></td>
                      <td></td>
                    </tr>
                    <?php
                    $reqS = "SELECT * FROM {$wpdb->prefix}fc_situation WHERE niveau_etudes = ".$rowNE->id." ORDER BY ordre ASC";
                    $rowsS = $wpdb->get_results($reqS);
                    foreach($rowsS as $rowS):
                      ?>
                      <tr>
                        <td style="padding-left:50px"><?php echo $rowS->nom;?></td>
                        <td></td>
                      </tr>
                      <?php
                      $reqDF = "SELECT * FROM {$wpdb->prefix}fc_duree_formation WHERE situation = ".$rowS->id." ORDER BY ordre ASC";
                      $rowsDF = $wpdb->get_results($reqDF);
                      foreach($rowsDF as $rowDF):
                        ?>
                        <tr>
                          <td style="padding-left:100px"><?php echo $rowDF->nom;?></td>
                          <td><input type="checkbox" class="formationsCheck" name="setFormation[]" value="<?php echo $rowDF->id;?>"></td>
                        </tr>
                        <?php
                      endforeach;
                    endforeach;
                  endforeach;
                ?>
              </tbody>
            </table>
          </div>
          <div class="col-2"><button onclick="saveFormation()" type="button" style="position:fixed;right:50px;top:250px" class="btn btn-success">Enregister</button></div>
        </div>
      </div>
      <?php
    }
    public function get_associations(){
      global $wpdb;
      if(isset($_GET['f'])):
        $row = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fc_formation_duree_formations WHERE id_formation=".$_GET['f']);
        echo json_encode(array("success"=>true,"message"=>null,"data"=>$row));
      else:
        echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
      endif;
      wp_die();
    }
    public function update_associations(){
      global $wpdb;
      if(isset($_POST['f']) && isset($_POST['tabs'])):
        $wpdb->query("DELETE FROM {$wpdb->prefix}fc_formation_duree_formations WHERE id_formation=".$_POST['f']);
        foreach($_POST['tabs'] as $a):
          $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formation_duree_formations (`id_formation`,`id_formation_situation`) VALUES ('".$_POST['f']."','".$a."')");
        endforeach;
        echo json_encode(array("success"=>true,"message"=>null,"data"=>$row));
      else:
        echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
      endif;
      wp_die();
    }
    public  function gestion_formations()
    {
        global $wpdb;
        echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">';
        wp_enqueue_script( 'formations-admin-js', plugins_url( '/js/formations-admin-js.js', __FILE__ ),array(),false,true);
        $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fc_formations WHERE deleted_at IS NULL ORDER BY nom ASC");
        ?>
        <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="margin:25px 0 ">
                  <li class="breadcrumb-item" aria-current="page">Demandes de formations</li>
                  <li class="breadcrumb-item active" aria-current="page">Gestion des formations</li>
                </ol>
              </nav>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="button" class="btn btn-success float-right" data-toggle="modal" data-target="#addModal">Ajouter une formation <i class="fas fa-plus"></i></button><br />
            </div>
          </div>
          <div class="row" style="margin-top:25px">
            <div class="col-12">
              <table class='table' id='mesformations' style='width:100%;border-collapse:collapse'>
                <thead class="thead-dark">
                  <tr><th>NOM</th><th>LIEN</th><th></th></tr>
                </thead>
                <tbody>
                <?php
                foreach($rows as $row):
                  ?>
                    <tr id="<?php echo $row->id;?>">
                      <td><?php echo $row->nom;?></td>
                      <td><a href="<?php echo $row->url;?>" target="_blank"><?php echo $row->url;?></a></td>
                      <td>
                        <div class="btn-group" role="group">
                          <button type="button" class="btn btn-primary" onclick="openEditModal(<?php echo $row->id;?>)"><i class="fas fa-edit"></i></button>
                          <button type="button" class="btn btn-danger" onclick="deleteFormation(<?php echo $row->id;?>)"><i class="fas fa-trash-alt"></i></button>
                        </div>
                      </td>
                    </tr>
                  <?php
                endforeach;
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Ajout d'une nouvelle formation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label for="new-formation-nom">Nom</label>
                  <input type="text" class="form-control" id="new-formation-nom" placeholder="Entrez le nom de la nouvelle formation">
                </div>
                <div class="form-group">
                  <label for="new-formation-url">URL</label>
                  <input type="url" class="form-control" id="new-formation-url" placeholder="Entrez le lien de la nouvelle formation">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveNewformation()">Enregister cette formation</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Modification d'une formation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label for="new-formation-nom">Nom</label>
                  <input type="hidden" id="edit-formation-id">
                  <input type="text" class="form-control" id="edit-formation-nom" placeholder="Entrez le nom de la formation">
                </div>
                <div class="form-group">
                  <label for="new-formation-url">URL</label>
                  <input type="url" class="form-control" id="edit-formation-url" placeholder="Entrez le lien de la formation">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveEditformation()">Enregister</button>
              </div>
            </div>
          </div>
        </div>
        <?php
    }

    public static function form_html($atts, $content)    {
        global $wpdb;
        wp_enqueue_style( 'formation_front', plugins_url( '/css/formation_front.css', __FILE__ ));
        wp_enqueue_script( 'formation_front-js', plugins_url( '/js/formation_front.js', __FILE__ ),array(),false,true);
        $rowsNE = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fc_niveau_etudes ORDER BY ordre ASC");
        ?>
        <select class='form-control' size='5' name='niveau_etudes' id='niveau_etudes'>
          <option value='-1'>Choisissez votre niveau d'études</option>
        <?php
        foreach($rowsNE as $rowNE):
          ?> <option value="<?php echo $rowNE->id;?>"><?php echo $rowNE->nom;?></option> <?php
        endforeach; ?>
        </select>
        <select class='form-control'  size='5' name='situation' id='situation' class='hidden'><option value='-1'>Choisissez votre situation professionnelle</option></select>
        <select class='form-control'  size='5' name='fc_duree_formation' id='fc_duree_formation' class='hidden'><option value='-1'>Choisissez la durée</option></select>
        <ul id="formations_list"></ul>
        <?php
      }
      public function add_formation(){
        global $wpdb;
        if(isset($_POST['nom'])):
          if(strlen($_POST['lien'])>0):
            $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`,`url`) VALUES ('".$_POST['nom']."','".$_POST['lien']."')");
          else:
            $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('".$_POST['nom']."')");
          endif;
          echo json_encode(array("success"=>true,"message"=>null,"data"=>null));
        else:
          echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
        endif;
        wp_die();
      }
      public function add_new_categorie(){
        global $wpdb;
        if(isset($_POST['nom']) && isset($_POST['type'])):
          if($_POST['type'] == 'fc_niveau_etudes'):
            $row = $wpdb->get_row("SELECT MAX(ordre) as maxordre FROM {$wpdb->prefix}fc_niveau_etudes");
            $wpdb->query("INSERT INTO {$wpdb->prefix}fc_niveau_etudes (`nom`,`ordre`) VALUES ('".$_POST['nom']."',$row->maxordre + 1)");
          elseif($_POST['type'] == 'fc_situation'):
            $row = $wpdb->get_row("SELECT MAX(ordre) as maxordre FROM {$wpdb->prefix}fc_situation WHERE niveau_etudes=".$_POST['ne']);
            $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`nom`,`ordre`,`niveau_etudes`) VALUES ('".$_POST['nom']."',$row->maxordre + 1,'".$_POST['ne']."')");
          elseif($_POST['type'] == 'fc_duree_formation'):
            $row = $wpdb->get_row("SELECT MAX(ordre) as maxordre FROM {$wpdb->prefix}fc_duree_formation WHERE situation=".$_POST['s']);
            $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`ordre`,`situation`) VALUES ('".$_POST['nom']."',$row->maxordre + 1,'".$_POST['s']."')");

          endif;
          echo json_encode(array("success"=>true,"message"=>null,"data"=>null));
        else:
          echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
        endif;
        wp_die();
      }
      public function del_formation(){
        global $wpdb;
        if(isset($_POST['f'])):
            $date = new \DateTime();
            $wpdb->query("DELETE FROM {$wpdb->prefix}fc_formation_duree_formations WHERE id_formation=".$_POST['f']);
            $wpdb->query("DELETE FROM {$wpdb->prefix}fc_formations WHERE id=".$_POST['f']);
          echo json_encode(array("success"=>true,"message"=>null,"data"=>null));
        else:
          echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
        endif;
        wp_die();
      }
      public function del_categorie(){
        global $wpdb;
        if(isset($_POST['c']) && isset($_POST['type'])):
          if($_POST['type'] == 'fc_niveau_etudes'):
            $rowNiveau = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}fc_niveau_etudes WHERE id=".$_POST['c']);
            $rowSituation = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fc_situation WHERE niveau_etudes=".$_POST['c']);
            foreach($rowSituation as $rowSitu):
              $wpdb->query("DELETE FROM {$wpdb->prefix}fc_duree_formation WHERE situation=".$rowSitu->id);
              $wpdb->query("DELETE FROM {$wpdb->prefix}fc_situation WHERE id=".$rowSitu->id);
            endforeach;
            $wpdb->query("DELETE FROM {$wpdb->prefix}fc_niveau_etudes WHERE id=".$_POST['c']);
          elseif($_POST['type'] == 'fc_situation'):
            $rowSituation = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fc_situation WHERE id=".$_POST['c']);
            foreach($rowSituation as $rowSitu):
              $wpdb->query("DELETE FROM {$wpdb->prefix}fc_duree_formation WHERE situation=".$rowSitu->id);
              $wpdb->query("DELETE FROM {$wpdb->prefix}fc_situation WHERE id=".$_POST['c']);
            endforeach;
          elseif($_POST['type'] == 'fc_duree_formation'):
            $wpdb->query("DELETE FROM {$wpdb->prefix}fc_duree_formation WHERE id=".$_POST['c']);
            $wpdb->query("DELETE FROM {$wpdb->prefix}fc_formation_duree_formations WHERE id_formation_situation=".$_POST['c']);
          endif;
          //Maintenant on reordonne TOUT
          $reqNE = "SELECT * FROM {$wpdb->prefix}fc_niveau_etudes ORDER BY ordre ASC";
          $rowsNE = $wpdb->get_results($reqNE);
          $cptn=1;
          foreach($rowsNE as $rowNE):
            $wpdb->query("UPDATE {$wpdb->prefix}fc_niveau_etudes SET ordre =".$cptn." WHERE id=".$rowNE->id);
            $reqS = "SELECT * FROM {$wpdb->prefix}fc_situation WHERE niveau_etudes = ".$rowNE->id." ORDER BY ordre ASC";
            $rowsS = $wpdb->get_results($reqS);
            $cpts = 1;
            foreach($rowsS as $rowS):
              $wpdb->query("UPDATE {$wpdb->prefix}fc_situation SET ordre =".$cpts." WHERE id=".$rowS->id);
              $reqDF = "SELECT * FROM {$wpdb->prefix}fc_duree_formation WHERE situation = ".$rowS->id." ORDER BY ordre ASC";
              $rowsDF = $wpdb->get_results($reqDF);
              $cpt = 1;
              foreach($rowsDF as $rowDF):
                $wpdb->query("UPDATE {$wpdb->prefix}fc_duree_formation SET ordre =".$cpt." WHERE id=".$rowDF->id);
                $cpt++;
              endforeach;
            $cpts++;
            endforeach;
          $cptn++;
          endforeach;
          echo json_encode(array("success"=>true,"message"=>null,"data"=>null));
        else:
          echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
        endif;
        wp_die();
      }
      public function edit_formation(){
        global $wpdb;
        if(isset($_POST['f'])):
            $wpdb->query("UPDATE {$wpdb->prefix}fc_formations SET nom='".$_POST['nom']."',url='".$_POST['url']."' WHERE id=".$_POST['f']);
          echo json_encode(array("success"=>true,"message"=>null,"data"=>null));
        else:
          echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
        endif;
        wp_die();
      }
      public function edit_categorie_nom(){
        global $wpdb;
        if(isset($_POST['c'])):
            $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET nom='".$_POST['nom']."' WHERE id=".$_POST['c']);
          echo json_encode(array("success"=>true,"message"=>null,"data"=>null));
        else:
          echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
        endif;
        wp_die();
      }
      public function get_formation(){
        global $wpdb;
        if(isset($_POST['f'])):
          $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}fc_formations WHERE id=".$_POST['f']);
          echo json_encode(array("success"=>true,"message"=>null,"data"=>$row));
        else:
          echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
        endif;
        wp_die();
      }
      public function get_categorie(){
        global $wpdb;
        if(isset($_POST['c']) && isset($_POST['type'])):
          $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}".$_POST['type']." WHERE id=".$_POST['c']);
          echo json_encode(array("success"=>true,"message"=>null,"data"=>$row,"type"=>$_POST['type']));
        else:
          echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
        endif;
        wp_die();
      }
      public function get_situation_by_ne(){
        global $wpdb;
        if(isset($_POST['id'])):
          $row = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fc_situation WHERE niveau_etudes=".$_POST['id']." ORDER BY ordre ASC");
          echo json_encode(array("success"=>true,"message"=>null,"data"=>$row));
        else:
          echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
        endif;
        wp_die();
      }
      public function get_duree_by_s(){
        global $wpdb;
        if(isset($_POST['id'])):
          $row = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fc_duree_formation WHERE situation=".$_POST['id']."  ORDER BY ordre ASC");
          echo json_encode(array("success"=>true,"message"=>null,"data"=>$row));
        else:
          echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
        endif;
        wp_die();
      }
      public function get_formation_by_duree(){
        global $wpdb;
        if(isset($_POST['id'])):
          $row = $wpdb->get_results("SELECT f.id,f.nom FROM {$wpdb->prefix}fc_formation_duree_formations d LEFT JOIN {$wpdb->prefix}fc_formations f on f.id = d.id_formation WHERE d.id_formation_situation=".$_POST['id']);
          echo json_encode(array("success"=>true,"message"=>null,"data"=>$row));
        else:
          echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
        endif;
        wp_die();
      }
      public function update_position_categorie(){
        global $wpdb;
        if(isset($_POST['c']) && isset($_POST['sens']) && isset($_POST['t'])):
          if($_POST['t'] == "fc_niveau_etudes"):
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}".$_POST['t']." WHERE id=".$_POST['c']);
            if($_POST['sens'] === 'down'):
              $row2 = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}".$_POST['t']." WHERE ordre=".($row->ordre+1));
              if($row2 && $row):
                $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET ordre='".($row->ordre+1)."' WHERE id=".$row->id);
                $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET ordre='".($row2->ordre-1)."' WHERE id=".$row2->id);
              endif;
            elseif($_POST['sens'] === 'up'):
              $row2 = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}".$_POST['t']." WHERE ordre=".($row->ordre-1));
              if($row2 && $row):
                $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET ordre='".($row->ordre-1)."' WHERE id=".$row->id);
                $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET ordre='".($row2->ordre+1)."' WHERE id=".$row2->id);
              endif;
            endif;
          elseif($_POST['t'] == "fc_situation"):
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}".$_POST['t']." WHERE id=".$_POST['c']);
            if($_POST['sens'] === 'down'):
              $row2 = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}".$_POST['t']." WHERE ordre=".($row->ordre+1)." AND niveau_etudes=".$row->niveau_etudes);
              if($row2 && $row):
                $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET ordre='".($row->ordre+1)."' WHERE id=".$row->id);
                $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET ordre='".($row2->ordre-1)."' WHERE id=".$row2->id);
              endif;
            elseif($_POST['sens'] === 'up'):
              $row2 = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}".$_POST['t']." WHERE ordre=".($row->ordre-1)." AND niveau_etudes=".$row->niveau_etudes);
              if($row2 && $row):
                $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET ordre='".($row->ordre-1)."' WHERE id=".$row->id);
                $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET ordre='".($row2->ordre+1)."' WHERE id=".$row2->id);
              endif;
            endif;
          elseif($_POST['t'] == "fc_duree_formation"):
            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}".$_POST['t']." WHERE id=".$_POST['c']);
            if($_POST['sens'] === 'down'):
              $row2 = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}".$_POST['t']." WHERE ordre=".($row->ordre+1)." AND situation=".$row->situation);
              if($row2 && $row):
                $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET ordre='".($row->ordre+1)."' WHERE id=".$row->id);
                $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET ordre='".($row2->ordre-1)."' WHERE id=".$row2->id);
              endif;
            elseif($_POST['sens'] === 'up'):
              $row2 = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}".$_POST['t']." WHERE ordre=".($row->ordre-1)." AND situation=".$row->situation);
              if($row2 && $row):
                $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET ordre='".($row->ordre-1)."' WHERE id=".$row->id);
                $wpdb->query("UPDATE {$wpdb->prefix}".$_POST['t']." SET ordre='".($row2->ordre+1)."' WHERE id=".$row2->id);
              endif;
            endif;
          endif;
          echo json_encode(array("success"=>($row2 && $row),"message"=>null,"data"=>$row,"data2"=>$row2));
        else:
          echo json_encode(array("success"=>false,"message"=>null,"data"=>null));
        endif;
        wp_die();
      }
      public static function render_form($row,$get){
          global $wpdb;
          $row = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cc_listeformation ORDER BY nom ASC");
          $html="<div class='container'>";
            $html.="<form method='POST' action='#' name='formdemande'>";
                  $html.="<div class='row'>";
                          $html.="<div class='col-12'>";
                            $html.='<div class="form-group">
                              <label for="nom">Nom :</label>
                              <input type="text" class="form-control" id="nom">
                            </div>';
                          $html.="</div>";
                          $html.="<div class='col-12'>";
                            $html.='<div class="form-group">
                              <label for="prenom">Prénom :</label>
                              <input type="text" class="form-control" id="prenom">
                            </div>';
                          $html.="</div>";
                          $html.="<div class='col-12'>";
                            $html.='<div class="form-group">
                              <label for="email">Email :</label>
                              <input type="email" class="form-control" id="email">
                            </div>';
                          $html.="</div>";
                          $html.="<div class='col-12'>";
                            $html.='<div class="form-group">
                              <label for="tel">Téléphone :</label>
                              <input type="text" class="form-control" id="tel">
                            </div>';
                          $html.="</div>";
                          $html.="<div class='col-12'>";
                            $html.='<div class="form-group">
                              <label for="aideopca">Je bénéficie d\'une aide de l\'OPCA :</label>
                              <div class="input-group-text" style="background:#fff;border:none">
                                <input type="radio" name="aideopca[]" value="0">Non
                              </div>
                              <div class="input-group-text" style="background:#fff;border:none">
                                <input type="radio" name="aideopca[]" value="1">Oui
                              </div>
                            </div>';
                          $html.="</div>";
                          $html.="<div class='col-12'>";
                            $html.='<div class="form-group">
                              <label for="typedemandeur">Je suis :</label>
                              <div class="input-group-text" style="background:#fff;border:none">
                                <input type="radio" name="typedemandeur[]" value="pro">Entreprise
                              </div>
                              <div class="input-group-text" style="background:#fff;border:none">
                                <input type="radio" name="typedemandeur[]" value="part">Particulier
                              </div>
                            </div>';
                          $html.="</div>";
                          $html.="<div class='col-12'>";
                            $html.='<div class="form-group">
                              <label for="nbr_participant">Nombre de participant(s):</label>
                              <div class="input-group-text" style="background:#fff;border:none">
                                <input type="radio" name="nbr_participant" value="unique">1 seul participant
                              </div>
                              <div class="input-group-text" style="background:#fff;border:none">
                                <input type="radio" name="nbr_participant" value="plusieurs">Plusieurs participants
                              </div>
                            </div>';
                          $html.="</div>";
                          $html.="<div class='col-12 tohide reveal_liste_formations'>";
                            $html.='<div class="form-group hidden" id="liste_formations">
                              <label for="liste_modules">Je choisis les modules de formation :</label>';
                                foreach($row as $r):
                                  $html.='<div class="input-group-text" style="background:#fff">';
                                    $html.='<input type="checkbox" name="liste_modules[]" id="liste_modules[]" value="'.$r->id.'">'.$r->nom;
                                $html.='</div>';
                                endforeach;
                            $html.='</div>';
                          $html.="</div>";
                          $html.="<div class='col-12 tohide reveal_societaire_ou_pas'>";
                            $html.='<div class="form-group">
                              <label for="societaire_ou_pas">Je suis :</label>
                              <select class="form-control" id="societaire_ou_pas">
                                <option value="0">Non sociétaire</option>
                                <option value="1">Sociétaire</option>
                              </select>
                            </div>';
                          $html.="</div>";
                          $html.="<div class='col-12'>";
                            $html.='<center><input class="btn btn-success"  type="submit" name="validDemande" value="Je valide"></center>';
                          $html.='</div>';
                  $html.="</div>";
          $html.="</form>";
          $html.="</div>";

          return $html;
      }

      public static function plugin_deactivation()
      {
          global $wpdb;
          $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}devis_saisie;");
      }
      public static function plugin_activation()
      {
          try{
              global $wpdb;
  	          $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}fc_niveau_etudes;");
              $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fc_niveau_etudes (id INT AUTO_INCREMENT PRIMARY KEY,
                  nom VARCHAR(255) NOT NULL,
                  ordre int not null,
                  deleted_at DATETIME NOT NULL
              );");
  	          $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}fc_situation;");
              $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fc_situation (id INT AUTO_INCREMENT PRIMARY KEY,
                  nom VARCHAR(255) NOT NULL,
                  deleted_at DATETIME NULL,
                  niveau_etudes INT NOT NULL,
                  ordre int not null,
                  FOREIGN KEY (niveau_etudes)
                    REFERENCES fc_niveau_etudes(id)
              );");
  	          $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}fc_duree_formation;");
              $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fc_duree_formation (id INT AUTO_INCREMENT PRIMARY KEY,
                  nom VARCHAR(255) NOT NULL,
                  deleted_at DATETIME NULL,
                  situation INT NOT NULL,
                  ordre int not null,
                  FOREIGN KEY (situation)
                    REFERENCES fc_situation(id)
              );");
  	          $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}fc_formations;");
              $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fc_formations (id INT AUTO_INCREMENT PRIMARY KEY,
                  nom VARCHAR(255) NOT NULL,
                  url VARCHAR(255) NULL,
                  deleted_at DATETIME NULL
              );");
  	          /*$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}fc_result_formation;");
              $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fc_result_formation (id INT AUTO_INCREMENT PRIMARY KEY,
                  id_formation INT NOT NULL
                  duree_formation INT NOT NULL,
                  deleted_at DATETIME NULL,
                  ordre int not null,
                  duree_formation INT NOT NULL,
                  FOREIGN KEY (id_formation)
                    REFERENCES fc_formations(id)
                  FOREIGN KEY (duree_formation)
                    REFERENCES fc_duree_formation(id)
              );");*/
              $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}fc_formation_duree_formations;");
              $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fc_formation_duree_formations (id INT AUTO_INCREMENT PRIMARY KEY,
                  id_formation INT NOT NULL,
                  id_formation_situation INT NOT NULL,
                  FOREIGN KEY (id_formation)
                    REFERENCES fc_formations(id),
                  FOREIGN KEY (id_formation_situation)
                    REFERENCES fc_duree_formation(id)
              );");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_niveau_etudes (`id`,`nom`,`ordre`) VALUES (1,'Vous n\'avez pas de diplôme',1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (1,'Lycéen, étudiant',1,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',1,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',1,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',1,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',1,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',1,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (2,'Salarié',1,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',2,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',2,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',2,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',2,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',2,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (3,'Demandeur d\'emploi',1,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',3,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',3,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',3,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',3,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',3,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (4,'Futur apprenti',1,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',4,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',4,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',4,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',4,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',4,5)");


              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_niveau_etudes (`id`,`nom`,`ordre`) VALUES (2,'Vous avez un diplôme de niveau V (CAP/BEP)',2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (5,'Lycéen, étudiant',2,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',5,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',5,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',5,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',5,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',5,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (6,'Salarié',2,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',6,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',6,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',6,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',6,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',6,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (7,'Demandeur d\'emploi',2,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',7,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',7,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',7,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',7,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',7,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (8,'Futur apprenti',2,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',8,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',8,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',8,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',8,5)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',8,4)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_niveau_etudes (`id`,`nom`,`ordre`) VALUES (3,'Vous avez un diplôme de niveau VI (Baccalauréat ou équivalent) ou vous êtes en cours d\'obtention',3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (9,'Lycéen, étudiant',3,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',9,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',9,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',9,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',9,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',9,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (10,'Salarié',3,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',10,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',10,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',10,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',10,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',10,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (11,'Demandeur d\'emploi',3,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',11,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',11,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',11,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',11,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',11,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (12,'Futur apprenti',3,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',12,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',12,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',12,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',12,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',12,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_niveau_etudes (`id`,`nom`,`ordre`) VALUES (4,'Vous avez un diplôme équivalent ou supérieur au niveau III (BTS, DUT,...)',4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (13,'Lycéen, étudiant',4,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',13,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',13,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',13,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',13,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',13,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (14,'Salarié',4,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',14,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',14,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',14,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',14,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',14,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (15,'Demandeur d\'emploi',4,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',15,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',15,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',15,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',15,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',15,5)");

              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_situation (`id`,`nom`,`niveau_etudes`,`ordre`) VALUES (16,'Futur apprenti',4,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('Non renseigné',16,1)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De moins d\'un an',16,2)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('D\'un an max',16,3)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 2 ans max',16,4)");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_duree_formation (`nom`,`situation`,`ordre`) VALUES ('De 3 ans max',16,5)");

              //Insertion des résultats
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Diplôme d\'Etat d\'Assistant Familial (DEAF)')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`,`url`) VALUES ('Diplôme d\'Etat d\'Assistant de Service Social (DEASS)','http://www.irtsaquitaine.fr/offres/assistant_service_social.php')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`,`url`) VALUES ('Diplôme d\'Etat d\'Educateur Spécialisé (DEES)','http://www.irtsaquitaine.fr/offres/educateur_specialise.php')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Passerelle Diplôme d\'Etat d\'Educateur Spécialisé (DEES) pour les ME')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`,`url`) VALUES ('Diplôme d\'Etat d\'Educateur Technique Spécialisé (DEETS)','http://www.irtsaquitaine.fr/offres/educateur_technique_specialise.php')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Titre de Moniteur d\'Atelier (TMA)')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`,`url`) VALUES ('Diplôme d\'Etat de Moniteur Educateur (DEME)','http://www.irtsaquitaine.fr/offres/moniteur_educateur.php')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Certificat national de Compétences de Mandataire Judiciaire à la Protection des Majeurs (MJPM)')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`,`url`) VALUES ('Diplôme d\'Etat d\'Accompagnant Educatif et Social (DEAES)','http://www.irtsaquitaine.fr/offres/accompagnement_educatif_et_social.php')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Aidant Familial – Proche Aidant Familial')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Assistant de soins en gérontologie (ASG)')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Titre Assistant de Vie aux Familles (ADVF)')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`,`url`) VALUES ('Diplôme d\'Etat de Technicien de l\'Intervention Sociale et Familiale (DETISF)','http://www.irtsaquitaine.fr/offres/technicien_de_intervention_sociale_familiale.php')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Certificat de Surveillant de Nuit (SN)')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Certificat de Maitre(sse) de Maison (MM)')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Modules de spécialisation complémentaire au DEAES')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Certificat d\'Aptitude aux Fonctions d\'Encadrement et de Responsable d\'Unité d\'Intervention Sociale (CAFERUIS)')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Dirigeant d\'Entreprise de l\'Economie Sociale et Solidaire (DEESS)')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Préparation aux métiers de social')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('OASIS Handicap préformation aux métiers du social')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Formations tutorales (Tuteurs) – Maitre d\'apprentissage')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Module autisme')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Prévention secours, santé, sécurité')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('Référent de parcours')");
              $wpdb->query("INSERT INTO {$wpdb->prefix}fc_formations (`nom`) VALUES ('OGDPC')");
          }
          catch(\Exception $e){
              var_dump($e->getMessage());
              wp_die();
          }
      }
}
