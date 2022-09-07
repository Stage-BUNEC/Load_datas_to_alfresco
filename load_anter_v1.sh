#!/bin/bash

# [ Date ]        : 06-09-2022
# [ Description ] : Ce script sert à AUTOMATISER l'envoie des fichiers PDF avec leurs Méta-données dans Alfresco
# [ Author(s) ]   : NANFACK STEVE

dossier_travail=$1
dossier_stats=/tools/

# ------------ verification du chemin des donnees sources -----

if [ ! "${dossier_travail: -1}" = '/' ]; then
    echo -e "\nErreur: Ajouter un '/' a la fin du dossier\n"
    exit 1
fi
if [ ! -d "$dossier_travail" ]; then
    echo -e "\nErreur: Dossier inexistant !\n"
    exit 1
fi
if [ ! "$(ls -A "$dossier_travail")" ]; then # test si dossier Vide
    echo -e "\nErreur: Dossier Vide !\n"
    exit 1
fi

# -- Extraction des infos [ du chemin des donnees sources] --------

totalPDF=$(ls "$dossier_travail"*.pdf | wc -l)
codeCEC=$(echo "$dossier_travail" | awk -F/ '{print $(NF-2)}')
typeDossierActes=$(echo "$dossier_travail" | awk -F/ '{print $(NF-1)}')
typeDossierConsolid=$(echo "$dossier_travail" | grep -oE "reprise|digit")
date=$(date -I)
dossierCible="/opt/consolidation/$typeDossierConsolid/$codeCEC/$typeDossierActes/$date"

if [ ! -d "$dossierCible" ]; then
    mkdir -p "$dossierCible"
fi

# ----------------- Nettoyage/Renommage --------------------------

echo -ne "\nNettoyage des fichiers..."
. cleanFiles_anter_v1.sh 2>"$dossier_travail"error_cleanFiles_anter_v1.log
echo -ne " - [ OK ]\n"

# ----------- Chargement dans Alfrsco ------------------

fichierTraites=0
echo -ne "\n"
for files in $(ls -t "$dossier_travail" | grep "pdf"); do
    metaDonnees="$dossier_travail${files//pdf/txt}"
    fileName=$"$dossier_travail$files"

    php script_anter_v1.php "$fileName" "$metaDonnees" "$dossier_travail" "$dossierCible" 2>"$dossier_travail"error_script_anter_v1.log
    fichierTraites=$((fichierTraites + 1))
    echo -ne "\rChargement des donnees dans ALFRESCO...($fichierTraites) Traité(s)"
done
echo -ne " - [ OK ]\n\n"

# -------------------- Gestion des Statistiques ----------------------------------

champs_stats="Date#type_Consolid#Code_CEC#type_Actes#orgnasition_fichiers#Total_Actes#Total_charges#"
totalCharges=$(ls "$dossierCible/"*.pdf | wc -l)
fichier_stats="$dossier_stats"stats_"$date".csv

# Creation du dossier des stats
if [ ! -d "$dossier_stats" ]; then
    mkdir -p "$dossier_stats"
fi

# Ajout de l'entete du fichier de stats
if [ ! -e "$fichier_stats" ]; then
    echo "$champs_stats" >"$dossier_stats"stats_"$date".csv
fi

# Ajout des stats dans le fichier
echo "$date#$typeDossierConsolid#$codeCEC#$typeDossierActes#v1#$totalPDF#$totalCharges#" >>"$dossier_stats"stats_"$date".csv

# -------------- Deplacement dans les registres ---------------------------------

echo -ne "Classement des actes..."
. organizeFiles.sh "$dossierCible" 2>"$dossierCible"_error_range.log
echo -ne " [ OK ] \n\n"
