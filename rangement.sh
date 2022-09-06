#!/bin/bash

# [ Date ]        : 05-09-2022
# [ Description ] : Ce script sert à classer les actes dans leur registre respectifs
# [ Author(s) ]   : NANFACK STEVE

dossier_cible=$1"/"
liste_resgistres=liste_registres.txt

# Recuperation des differents registres
for file in "$dossier_cible"*.txt; do

    registre=$(head -n 1 "$file" | cut -d '#' -f 1 | awk -F/ '{print $(NF-1)}')

    # Creation des dossiers de registre si inexistant
    if [ ! -d "$registre" ]; then
        mkdir -p "$dossier_cible$registre"
        echo "$registre" >>"$dossier_cible$liste_resgistres"
    fi

    # Classement des actes
    mv "$file" "$dossier_cible$registre"
    fichier_acte=$(echo "$file" | sed s/"txt"/"pdf"/g)
    mv "$fichier_acte" "$dossier_cible$registre"
done
