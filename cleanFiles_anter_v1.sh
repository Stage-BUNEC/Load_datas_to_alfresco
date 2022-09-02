#!/bin/bash

# [ Date ]        : 02-09-2022
# [ Description ] : Ce script sert à bien renommer les fichiers *.pdf et *.txt
# [ Author(s) ]   : Mr PROSPERE OTTOU / Mr GAEL MANI / NANFACK STEVE

workDir=$1
# On remplace les espaces par des "_"
for file in "$workDir"*.txt; do mv "$file" "${file// /_}"; done
for file in "$workDir"*.pdf; do mv "$file" "${file// /_}"; done

# On renomme tous les fichiers qui n'ont pas de num de registre
i=1
rplNbr=0
for file in $(ls -t "$workDir" | grep -E "^[_]**.pdf|^[_]**.txt"); do
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
for file in $(ls -t "$workDir" | grep -E "*(_{1,}.txt)$|*(_{1,}.pdf)$"); do
    newBegin=$(echo $file | sed -E s/"^(_{1,})"//g)
    mv "$file" "$newBegin"
done

for file in $(ls -t "$workDir" | grep -E "*(_{1,}.txt)$"); do
    newEnd=$(echo $file | sed -E s/"(_{1,}.txt)$"/.txt/g)
    mv "$file" "$newEnd"
done

for file in $(ls -t "$workDir" | grep -E "*(_{1,}.pdf)$"); do
    newEnd=$(echo $file | sed -E s/"(_{1,}.pdf)$"/.pdf/g)
    mv "$file" "$newEnd"
done

# On remplace plusieurs "_" par un seul

for file in "$workDir"*.txt; do
    newName=$(echo "$file" | sed -E s/"(_{2,})"/_/g)
    mv "$file" "$newName"
done

for file in "$workDir"*.pdf; do
    newName=$(echo "$file" | sed -E s/"(_{2,})"/_/g)
    mv "$file" "$newName"
done

# Ajout du "#" a la fin du fichier *.txt
for file in "$workDir"*.txt; do
    sed s/$/'#'/g "$file" >"$workDir"tmp
    cat "$workDir"tmp >"$file"
done
rm "$workDir"tmp
