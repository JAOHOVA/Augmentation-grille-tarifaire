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

function sauvegarde_augmentation_tarifaire ( &$action ) {
    
    // Récupérer les paramètres de l'url
    $iIGrilleSpecifiqueClient = GetHttpVars("id");
    $fPourcentage = GetHttpVars("augmentation", "");
    $sOption = GetHttpVars("arrondit");
    $oGrilleSpecifiqueClient = new_Doc("", $iIGrilleSpecifiqueClient);
    // Donnée brut de DB
    $aGrilleSpClient = $oGrilleSpecifiqueClient->getArrayRawValues("gts_t_containt");

    if(!empty($fPourcentage)) {
        if($sOption == "exces") {
            // Appel de la fonction pour calculer l'augmentation pour la colonne 'gts_arhtlist' par excés
            calculerAugmentationColonne($aGrilleSpClient, 'gts_arhtlist', $fPourcentage, 0);
             // Appel de la fonction setAttributeValueAndStoreArray pour enregistrer dans la base de données
            setAttributeValueAndStoreArray($oGrilleSpecifiqueClient, 'gts_t_containt', $aGrilleSpClient);
        } else {
            // Appel de la fonction pour calculer l'augmentation pour la colonne 'gts_arhtlist' par defaut
            calculerAugmentationColonne($aGrilleSpClient, 'gts_arhtlist', $fPourcentage, 2);
             // Appel de la fonction setAttributeValueAndStoreArray pour enregistrer dans la base de données
            setAttributeValueAndStoreArray($oGrilleSpecifiqueClient, 'gts_t_containt', $aGrilleSpClient);
        }
    }

    // Redirection vers la page d'accueil
    $url = '?app=AFFAIRE&action=AUGMENTATION_TARIFAIRE&id='.$iIGrilleSpecifiqueClient;
    header("Location:".$url);
            
}

// Fonction setAttributeValueAndStoreArray
function setAttributeValueAndStoreArray($oGrilleSpecifiqueClient, $sAttribute, $aValeur) {
    $oGrilleSpecifiqueClient->setAttributeValue($sAttribute, $aValeur);
    $oGrilleSpecifiqueClient->store();
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
