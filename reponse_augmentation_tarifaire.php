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

function reponse_augmentation_tarifaire ( &$action ) {

    $iIGrilleSpecifiqueClient = GetHttpVars("id");
    $fPourcentage = $_POST["augmentation"];
    $sOption = $_POST["arrondit"];


    $url = '?app=AFFAIRE&action=AUGMENTATION_TARIFAIRE&id='.$iIGrilleSpecifiqueClient.'&augmentation='.$fPourcentage.'&arrondit='.$sOption;
    header("Location:".$url);
}

?>
