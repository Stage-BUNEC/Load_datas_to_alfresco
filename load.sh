#!/bin/bash

# [ Date ]        : 30-08-2022
# [ Description ] : Ce script sert à AUTOMATISER l'envoie des fichiers PDF avec leurs Méta-données dans Alfresco
# [Author(s) ]    : NANFACK STEVE

echo -ne "\nNettoyage des fichiers..."
. cleanFiles.sh 2>>cleanFiles.log
echo -ne "[ OK ]\n"

echo -ne "\nChargement des donnees dans ALFRESCO..."
for files in $(ls -t | grep "pdf"); do
    metaDonnees=$(echo "$files" | sed s/"pdf"/"txt"/g)
    php script.php $files $metaDonnees 2>>script.log
done
echo -ne "[ OK ]\n\n"
