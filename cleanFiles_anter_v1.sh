#!/bin/bash

# [ Last Update ] : 07-09-2022
# [ Description ] : Ce script sert à bien renommer les fichiers *.pdf et *.txt
# [ Author(s) ]   : Mr PROSPERE OTTOU / Mr GAEL MANI / NANFACK STEVE

workDir=$1

# On remplace les espaces par des "_"
# ex: CE N 1.pdf --> CE_N_1_.pdf ou "  .pdf" --> "__.pdf"

for file in "$workDir"*.txt; do
    if [ "$file" != "${file// /_}" ]; then
        mv "$file" "${file// /_}"
    fi
done

for file in "$workDir"*.pdf; do
    if [ "$file" != "${file// /_}" ]; then
        mv "$file" "${file// /_}"
    fi
done

# On renomme tous les fichiers qui n'ont pas de num de registre
# Le format de nom est "no_num_act_i.pdf" "no_num_act_i.txt"
# ex: __.pdf --> no_num_act_1.pdf

i=1
rplNbr=0
for file in $(ls -t "$workDir" | grep -E "^[_]**.pdf|^[_]**.txt"); do
    if [[ $file == *"pdf"* ]]; then
        #echo "$file" is "pdf"
        mv "$workDir$file" "$workDir""no_num_act_$i.pdf"
        rplNbr=$((rplNbr + 1))
    fi
    if [[ $file == *"txt"* ]]; then
        #echo "$file" is "txt"
        mv "$workDir$file" "$workDir""no_num_act_$i.txt"
        rplNbr=$((rplNbr + 1))
    fi
    if [ $rplNbr -eq 2 ]; then
        i=$((i + 1))
        rplNbr=0
    fi
done

# On retire les "_" au debut et la fin des noms de fichiers
# ex: CE_N_1_.pdf --> CE_N_1.pdf ou _CE_N_1.pdf --> CE_N_1.pdf

for file in $(ls -t "$workDir" | grep -E "*(_{1,}.txt)$|*(_{1,}.pdf)$"); do
    newBegin=$(echo "$file" | sed -E s/"^(_{1,})"//g)
    if [ "$file" != "$newBegin" ]; then
        mv "$workDir$file" "$workDir$newBegin"
    fi
done

for file in $(ls -t "$workDir" | grep -E "*(_{1,}.txt)$"); do
    newEnd=$(echo "$file" | sed -E s/"(_{1,}.txt)$"/.txt/g)
    if [ "$file" != "$newEnd" ]; then
        mv "$workDir$file" "$workDir$newEnd"
    fi
done

for file in $(ls -t "$workDir" | grep -E "*(_{1,}.pdf)$"); do
    newEnd=$(echo "$file" | sed -E s/"(_{1,}.pdf)$"/.pdf/g)
    if [ "$file" != "$newEnd" ]; then
        mv "$workDir$file" "$workDir$newEnd"
    fi
done

# On remplace plusieurs "_" par un seul. ex: CE___N__1 --> CE_N_1

# cas des *.txt
for file in "$workDir"*.txt; do
    newName=$(echo "$file" | sed -E s/"(_{2,})"/_/g)
    if [ "$file" != "$newName" ]; then
        mv "$file" "$newName"
    fi
done

# cas des *.pdf
for file in "$workDir"*.pdf; do
    newName=$(echo "$file" | sed -E s/"(_{2,})"/_/g)
    if [ "$file" != "$newName" ]; then
        mv "$file" "$newName"
    fi
done

# Ajout du "#" a la fin du fichier *.txt (metadonnees)

for file in "$workDir"*.txt; do
    sed s/$/'#'/g "$file" >"$workDir"tmp
    cat "$workDir"tmp >"$file"
done
rm "$workDir"tmp
