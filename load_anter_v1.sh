#!/bin/bash

# [ Date ]        : 02-09-2022
# [ Description ] : Ce script sert à AUTOMATISER l'envoie des fichiers PDF avec leurs Méta-données dans Alfresco
# [Author(s) ]    : NANFACK STEVE

workDir=$1

if [ ! "${workDir: -1}" = '/' ]; then
    echo -e "\nErreur: Ajouter un '/' a la fin du dossier\n"
    exit 1
fi
if [ ! -d "$workDir" ]; then
    echo -e "\nErreur: Dossier inexistant !\n"
    exit 1
fi

# Extraction des infos dans le workDir
codeCEC=$(echo "$workDir" | awk -F/ '{print $(NF-2)}')
typeDossierActes=$(echo "$workDir" | awk -F/ '{print $(NF-1)}')
typeDossierConsolid=$(echo "$workDir" | grep -oE "reprise|digit")
date=$(date -I)
#echo "typeConsolid = $typeDossierConsolid | $codeCEC = $codeCEC | typeDossierAct = $typeDossierActes "
dossierCible="/opt/consolidation/$typeDossierConsolid/$codeCEC/$typeDossierActes/$date"
#echo "$dossierCible"

if [ ! -d "$dossierCible" ]; then
    mkdir -p "$dossierCible"
fi

echo -ne "\nNettoyage des fichiers..."
. cleanFiles_anter_v1.sh 2>>"$workDir"cleanFiles_anter_v1.log
echo -ne " - [ OK ]\n"

fichTraites=0
echo -ne "\n"
for files in $(ls -t | grep "pdf"); do

    metaDonnee=$(echo "$files" | sed s/"pdf"/"txt"/g)
    metaDonnees="$workDir$metaDonnee"
    fileName=$"$workDir$files"
    php script_anter_v1.php "$fileName" "$metaDonnees" "$workDir" "$dossierCible" 2>>"$workDir"script_anter_v1.log
    fichTraites=$((fichTraites + 1))
    echo -ne "\rChargement des donnees dans ALFRESCO...($fichTraites) envoyée(s)"
done
echo -ne " - [ OK ]\n\n"
