#!/bin/bash

inputfile=$1
baseDir=$2
name=$3


echo "\n-----------    convertPDF   --------------"
echo "\n-----------    $(date)   --------------"
echo "inputfile $1"
echo "baseDir $2"
echo "name $3"
mkdir ${baseDir}document_thumb/${name}
mkdir ${baseDir}document_svg/${name}
mkdir ${baseDir}document_pages_pdf/${name}

chmod 777 ${baseDir}document_thumb/${name}
chmod 777 ${baseDir}document_svg/${name}
chmod 777 ${baseDir}document_pages_pdf/${name}

outputImage=${baseDir}document_thumb/${name}/page_%d.jpg
outputSvg=${baseDir}document_svg/${name}/page_%d.svg
outputPDF=${baseDir}document_pages_pdf/${name}/page_%d.pdf

echo $outputImage

echo '\nTASK1 | creating video thumnails'
echo "command: convert -resize 300 -density 150x150 \"$inputfile\" \"$outputImage\""

convert -resize 300 -density 150x150 "$inputfile" "$outputImage"

echo '\nTASK2 | convert to SVG pages'
echo "command: pdf2svg $inputfile $outputSvg all"

pdf2svg $inputfile $outputSvg all

echo '\nTASK3 | splitting pages'
echo "pdftk $inputfile burst output $outputPDF"
pdftk $inputfile burst output $outputPDF
echo "\n-----------    converPDF completed  --------------"
