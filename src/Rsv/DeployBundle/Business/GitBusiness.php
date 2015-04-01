<?php
/**
 * Created by PhpStorm.
 * User: thibault
 * Date: 18/03/15
 * Time: 08:50
 */

namespace Rsv\DeployBundle\Business;

class GitBusiness {

    const GIT = "git ";

    const NAME_BRANCH 	= "branch";
    const NAME_TAG 	 	= "tag";

    /*
      * BRANCHES
     */

    // Mise à jour des données
    const CMD_FETCH_DATA = "git fetch origin ";

    // Liste des branches
    const CMD_BRANCH_ALL 	 = "git branch";

    // Branche actuelle
    const CMD_BRANCH_CURRENT = "git rev-parse --abbrev-ref HEAD";

    // Changement de branche
    const CMD_CHANGE_SOURCE = "git fetch origin; git checkout -f ";

    // Changement de branche
    const CMD_UPDATE_SOURCE = "git pull origin ";

    /*
     * TAGS
     */

    // Liste des Tags
    const CMD_TAG_ALL		= "git tag";

    // Quel est le tag actuelle
    const CMD_TAG_CURRENT 	= "git describe --always --tag";

    // nouveau Tag: création du tag avec Nom & Date, puis Push vers dépôt
    const CMD_TAG_NEW 		= 'git tag -a #name# -m "création Tag Rsv Front #date#"; git push origin --tags';

    // activation d'un Tag
    const CMD_TAG_ACTIV		= "git fetch origin; git checkout -f ";
}