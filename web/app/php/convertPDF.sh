#!/bin/bash

inputfile=$1
baseDir=$2
name=$3

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

echo 'creating video thumnails'

convert -resize 300 -density 150x150 "$inputfile" "$outputImage"

echo 'convert to SVG pages'

pdf2svg $inputfile $outputSvg all

echo 'splitting pages'

pdftk $inputfile burst output $outputPDF

