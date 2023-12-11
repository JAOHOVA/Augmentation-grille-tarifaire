<?php
/**
 * Methods for contact document
 *
 * @author Nevea
 * @version $Id$
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package NEVEA_ADDONS
 */
include_once("./FDL/freedom_util.php");

function augmentation_tarifaire ( &$action ) {
    // Récupérer les paramètres de l'url
    $iIGrilleSpecifiqueClient = GetHttpVars("id");
    $fPourcentage = GetHttpVars("augmentation", "");
    $sOption = GetHttpVars("arrondit");

    $oGrilleSpecifiqueClient = new_Doc("", $iIGrilleSpecifiqueClient);
    // Donnée brut de DB
    $aGrilleSpClient = $oGrilleSpecifiqueClient->getArrayRawValues("gts_t_containt");

    $aChampSousTarif = array_keys($aGrilleSpClient[0]);
    $oFamille = new_Doc("", "GRILLETARIFAIRE");
    foreach($aChampSousTarif as $sChampSousTarif){
        $oAttribut = $oFamille->getAttribute($sChampSousTarif);
        $aEntete[] = $oAttribut->labelText;
        // echo $oAttribut->labelText." (".$oAttribut->visibility.")<br/>";
    }
    // Supprimer le premier élément : 'gts_aridlist'
    for($i = 0; $i < count($aGrilleSpClient); $i++) {
        array_shift($aGrilleSpClient[$i]);
    }
    // Supprimer 1er élément du tableau $aEntete
    array_shift($aEntete);
    // Tableau contenant les nouvelles clés
    // Supprimer le premier élément : 'gts_aridlist'
    array_shift($aChampSousTarif);

    // Tableau pour stocker le résultat pour remplacer les clés de $aEntete même taille
    $aColonneBase = remplacerClees($aEntete, $aChampSousTarif);
    // Création des libellés de $aColonneBase
    $aLibelleBase = [];
    foreach($aColonneBase as $sCleLibelle => $sValeurLibelle) {
        $aCreeColonne = creeColonne($sCleLibelle, $sValeurLibelle);
        array_push($aLibelleBase, $aCreeColonne);
    } 

    if(!empty($fPourcentage)) {
        if($sOption == "exces") {
            // Appel de la fonction pour calculer l'augmentation pour la colonne 'gts_arhtlist' par excés
            calculerAugmentationColonne($aGrilleSpClient, 'gts_arhtlist', $fPourcentage, 0);
        } else {
            // Appel de la fonction pour calculer l'augmentation pour la colonne 'gts_arhtlist' par defaut
            calculerAugmentationColonne($aGrilleSpClient, 'gts_arhtlist', $fPourcentage, 2);
        }
        
        $action->lay->Set("VALEUR_ID", $iIGrilleSpecifiqueClient);
        $action->lay->Set("PARAMETRES", "&id=".$iIGrilleSpecifiqueClient."&augmentation=".$fPourcentage."&arrondit=".$sOption);
        $action->lay->Set("boutonValider", false);
        $action->lay->Set("boutonSauvegarder", true);
        $action->lay->Set("LIBELLEBASE", json_encode($aLibelleBase));
        $action->lay->Set("BASE", json_encode($aGrilleSpClient));
    }
    else {

        $action->lay->Set("PARAMETRE", "&id=".$iIGrilleSpecifiqueClient);
        $action->lay->Set("boutonValider", true);
        $action->lay->Set("boutonSauvegarder", false);
        $action->lay->Set("LIBELLEBASE", json_encode($aLibelleBase));
        $action->lay->Set("BASE", json_encode($aGrilleSpClient));
    }

}

// Fonction permet de remplacer les clés d'un tableau associatif de même taille
function remplacerClees($aEntree, $aCle) {
    $aResultat = [];
    // les deux tableaux ont la même longueur
    if (count($aEntree) === count($aCle)) {
        $aValues = array_values($aEntree); // Récupérez les valeurs du tableau associatif
        for ($i = 0; $i < count($aCle); $i++) {
            $aResultat[$aCle[$i]] = $aValues[$i];
        }
    }
    return $aResultat;
}
// Fonction création de colonne
function creeColonne($name, $label){
    $resp = ["field" => $name, "title" => $label];
    return $resp;
}
// Fonction calculerAugmentationColonne
function calculerAugmentationColonne(&$aTableau, $sNomColonne, $fPourcentage, $iNombre) {
    foreach ($aTableau as &$aLigne) {
        if (array_key_exists($sNomColonne, $aLigne)) {
            $fValeurInitiale = $aLigne[$sNomColonne];
            // Calcul de l'augmentation par excès
            $fAugmentation = round(($fValeurInitiale + ($fValeurInitiale * $fPourcentage)/ 100), $iNombre);
            // Mettre à jour les valeurs dans le tableau
            $aLigne[$sNomColonne ] = $fAugmentation;
        }
    }
}

?>
