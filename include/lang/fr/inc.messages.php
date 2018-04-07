<?php
##############################################################################
# PHP Code by Gilnei Moraes
# Contact: gilneim@hotmail.com
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
##############################################################################
# Description: Common Labels/Messages (English)
#
# Date: 2016-06-06
# File: en/inc.messages.php
##############################################################################

$message = array (
	# Welcome Messages
	1 => "Bienvenue à Abook",
	# Confirmation 
	2 => "Information sur le contact ajoutée.",
	3 => "Information du contact mise à jour.",
	4 => "Ce contact a été retiré de votre carnet d'adresse.",
	5 => "Etes-vous certain de vouloir effacer ce contact? Cette action est <b>irréversible</b>.",
	# Labels
	6 => "Identification S.V.P.",
	7 => "Liste des contacts",
	8 => "Modifier Contact",
	9 => "Effacer Contact",
	10 => "Rechercher",
	11 => "Contact Supprimé",
	12 => "Contact Mis à jour",
	13 => "Ajouter Contact",
	14 => "Contact Ajouté",
	15 => "Généré Automatiquement",
	16 => "Nom d'usager:",
	17 => "Mot de passe:",
	18 => "Adresse:",
	19 => "Tél. domicile:",
	20 => "Tél. travail:",
	21 => "Tél. portable:",
	22 => "Tél. Autre:",
	23 => "Site Web:",
	24 => "E-Mail:",
	25 => "Commentairess:",
	# Admin Area Messages
	26 => "Gestion des usagers",
	27 => "Ajouter un usager",
	28 => "Supprimer Usager",
	29 => "Modifier Usager",
	30 => "Admin. Usager:",
	31 => "Etes-vous certain de vouloir supprimer cet usager? Cette action est <b>irréversible</b>.",
	32 => "L'usager <b>$u_user</b> a été ajouté à la liste des usagers autorisés",
	33 => "L'usager <b>$u_user</b> a été supprimé de la liste des usagers autorisés.",
	34 => "L'usager <b>$u_user</b> a été modifié.",
	35 => "Confirmer mot de passe:",
	36 => "Nouveau Mot de passe:",
	37 => "Résultats recherche: $str",
	38 => "Rechercher contact(s) dont",
	39 => "contient",
	40 => "commence par",
	41 => "se termine par",
	42 => "est",
	43 => "Entrer",
	44 => "Quitter",
	45 => "Editer",
	46 => "Voir",
	47 => "OUI",
	48 => "NON",
	49 => "Annuler",
	50 => "Terminé",
	51 => "Imprimer Adresse",
	52 => "Imprimer Tout",
	53 => "Rechercher",
	54 => "Langue",
	55 => "Voir Tous",
	56 => "OK",
	57 => "Précédent",
	58 => "Suivant",
	59 => "Aucun",

	# unknown error...DOH!
	666 => "Erreur inconnue!"
	);
$errorMsg = array (
	# Database Errors
	1 => "Incapable de connecter au serveur '<i>$db_host</i>'",
	2 => "Incapable de sélectionner la base de données '<i>$db_db</i>' sur '<i>$db_host</i>'",
	3 => "Incapable d'exécuter la requête.",
	# Login Errors
	4 => "<b>Login Invalide:</b> S.V.P. Entrez un nom d'usager et un mot de passe valides.",
	# Permission errors
	5 => "Accès Refusé",
	6 => "Désolé, vous n'êtes pas administrateur et ne pouvez exécuter cette action. Vous devez être autorisé par l'administrateur.",
	7 => "Mots de passe incompatibles...<br><a href=\"javascript:history.go(-1);\">Back</a>",
	8 => "Vous ne devriez pas utiliser un mot de passe vide...<br><a href=\"javascript:history.go(-1);\">Back</a>",

	# Unkown Error...DOH!
	666 => "Une erreur inconnue est survenue. S.V.P. Avisez le gestionnaire du réseau des détails relatifs à cette erreur."
	);
$rolArray = array(
	1 => "A",
	2 => "B",
	3 => "C",
	4 => "D",
	5 => "E",
	6 => "F",
	7 => "G",
	8 => "H",
	9 => "I",
	10 => "J",
	11 => "K",
	12 => "L",
	13 => "M",
	14 => "N",
	15 => "O",
	16 => "P",
	17 => "Q",
	18 => "R",
	19 => "S",
	20 => "T",
	21 => "U",
	22 => "V",
	23 => "W",
	24 => "X",
	25 => "Y",
	26 => "Z"
	);

?>