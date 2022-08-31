#!/bin/bash

# [ Date ]        : 31-08-2022
# [ Description ] : Ce script sert à AUTOMATISER l'envoie des fichiers PDF avec leurs Méta-données dans Alfresco
# [Author(s) ]    : NANFACK STEVE

echo -ne "\nNettoyage des fichiers..."
. cleanFiles_anter_v1.sh 2>>cleanFiles_anter_v1.log
echo -ne "[ OK ]\n"

echo -ne "\nChargement des donnees dans ALFRESCO..."
for files in $(ls -t | grep "pdf"); do
    metaDonnees=$(echo "$files" | sed s/"pdf"/"txt"/g)
    php script_anter_v1.php $files $metaDonnees 2>>script_anter_v1.log
done
echo -ne "[ OK ]\n\n"
