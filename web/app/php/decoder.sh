#!/bin/bash

infile=$1
thumb=$2
tmpfile=${infile}_tmp.mp4
tmpfile2=${infile}_tmp2.mp4
outfile=${1%.*}.mp4

echo "\n\n----------------   START  ENCODING FILE  $infile    ------------------\n\n\n"

options="-vcodec libx264 -b 512k -flags +loop+mv4 -cmp 256 \
	   -me_method hex -subq 7 -trellis 1 -refs 5 -bf 3 \
	   -flags2 +bpyramid+wpred+mixed_refs+dct8x8 -coder 1 -me_range 16 \
           -g 250 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -qmin 10\
	   -qmax 51 -qdiff 4"

#ffmpeg -y -i "$infile" -an -pass 1 -threads 2 $options "$tmpfile"

#ffmpeg -y -i "$infile" -acodec libmp3lame -ar 44100 -ab 96k -pass 2 -threads 2 $options "$tmpfile"

#qt-faststart "$tmpfile" "$outfile"

echo "Converting file to h264"
echo "command: ffmpeg -y -i \"$infile\" -codec:v libx264 -profile:v high -preset slow -b:v 500k -maxrate 500k -bufsize 1000k -vf scale=-2:480 -threads 0 -codec:a libfdk_aac -b:a 128k \"$outfile\""

ffmpeg -y -i "$infile" -codec:v libx264 -profile:v high -preset slow -b:v 500k -maxrate 500k -bufsize 1000k -vf scale=-2:480 -threads 0 -codec:a libfdk_aac -b:a 128k "$outfile"


echo "creating video thumbnail"
echo "command: ffmpeg -y -ss 00:00:1 -i \"$infile\" -f image2 -vframes 1 $thumb"
ffmpeg -y -ss 00:00:1 -i "$infile" -f image2 -vframes 1 $thumb

#clean up intermediate files
rm -f $tmpfile
rm -f $tmpfile2
rm -f *.log
rm -f *.mbtree

echo "\n\n----------------   END  ENCODING FILE  $infile    ------------------\n\n\n\n"
