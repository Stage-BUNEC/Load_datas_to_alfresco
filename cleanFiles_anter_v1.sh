#!/bin/bash

# [ Date ]        : 02-09-2022
# [ Description ] : Ce script sert à bien renommer les fichiers *.pdf et *.txt
# [ Author(s) ]   : Mr PROSPERE OTTOU / Mr GAEL MANI / NANFACK STEVE

#workDir=$1
# On remplace les espaces par des "_"
for file in *.txt; do
    if [ "$file" != "${file// /_}" ]; then
        mv "$file" "${file// /_}"
    fi
done

for file in *.pdf; do
    if [ "$file" != "${file// /_}" ]; then
        mv "$file" "${file// /_}"
    fi
done

# On renomme tous les fichiers qui n'ont pas de num de registre
i=1
rplNbr=0
for file in $(ls -t | grep -E "^[_]**.pdf|^[_]**.txt"); do
    if [[ $file == *"pdf"* ]]; then
        #echo "$file" is "pdf"
        mv "$file" "no_num_act_"$i".pdf"
        rplNbr=$((rplNbr + 1))
    fi
    if [[ $file == *"txt"* ]]; then
        #echo "$file" is "txt"
        mv "$file" "no_num_act_"$i".txt"
        rplNbr=$((rplNbr + 1))
    fi
    if [ $rplNbr -eq 2 ]; then
        i=$((i + 1))
        rplNbr=0
    fi
done

# On retire les "_" au debut et la fin des noms de fichiers
for file in $(ls -t | grep -E "*(_{1,}.txt)$|*(_{1,}.pdf)$"); do
    newBegin=$(echo $file | sed -E s/"^(_{1,})"//g)
    if [ "$file" != "$newBegin" ]; then
        mv "$file" "$newBegin"
    fi
done

for file in $(ls -t | grep -E "*(_{1,}.txt)$"); do
    newEnd=$(echo $file | sed -E s/"(_{1,}.txt)$"/.txt/g)
    if [ "$file" != "$newBegin" ]; then
        mv "$file" "$newEnd"
    fi
done

for file in $(ls -t | grep -E "*(_{1,}.pdf)$"); do
    newEnd=$(echo "$file" | sed -E s/"(_{1,}.pdf)$"/.pdf/g)
    if [ "$file" != "$newBegin" ]; then
        mv "$file" "$newEnd"
    fi
done

# On remplace plusieurs "_" par un seul

for file in *.txt; do
    newName=$(echo "$file" | sed -E s/"(_{2,})"/_/g)
    if [ "$file" != "$newName" ]; then
        mv "$file" "$newName"
    fi
done

for file in *.pdf; do
    newName=$(echo "$file" | sed -E s/"(_{2,})"/_/g)
    if [ "$file" != "$newName" ]; then
        mv "$file" "$newName"
    fi
done

# Ajout du "#" a la fin du fichier *.txt
for file in *.txt; do
    sed s/$/'#'/g "$file" >tmp
    cat tmp >"$file"
done
rm tmp
