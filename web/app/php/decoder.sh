#!/bin/bash

infile=$1
thumb=$2
tmpfile=${infile}_tmp.mp4
tmpfile2=${infile}_tmp2.mp4
outfile=${1%.*}.mp4

options="-vcodec libx264 -b 512k -flags +loop+mv4 -cmp 256 \
	   -partitions +parti4x4+parti8x8+partp4x4+partp8x8+partb8x8 \
	   -me_method hex -subq 7 -trellis 1 -refs 5 -bf 3 \
	   -flags2 +bpyramid+wpred+mixed_refs+dct8x8 -coder 1 -me_range 16 \
           -g 250 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -qmin 10\
	   -qmax 51 -qdiff 4"

ffmpeg -y -i "$infile" -an -pass 1 -threads 2 $options "$tmpfile"

ffmpeg -y -i "$infile" -acodec libmp3lame -ar 44100 -ab 96k -pass 2 -threads 2 $options "$tmpfile"

qt-faststart "$tmpfile" "$outfile"

#creating video thumbnail

ffmpeg -ss 00:00:1 -i "$infile" -f image2 -vframes 1 $thumb

#clean up intermediate files
rm -f $tmpfile
rm -f $tmpfile2
rm -f *.log
rm -f *.mbtree
