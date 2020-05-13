#!/bin/bash

DIRECTORY_TO_COMPRESS="mz-mbo-access"
ZIPPED_FILE="mz-mbo-access.zip"

cd ../
echo "Changing directory and moving to new temp dir."
mv mz-mbo-access mz-mbo-access-temp
echo "Make temporary version of the plugin and copying desired files."
mkdir mz-mbo-access
cp -r mz-mbo-access-temp/inc mz-mbo-access
cp -r mz-mbo-access-temp/dist mz-mbo-access         
cp -r mz-mbo-access-temp/languages mz-mbo-access
cp mz-mbo-access-temp/*.php mz-mbo-access
cp mz-mbo-access-temp/README.txt mz-mbo-access
echo "Files copied. Making zip file."
zip -r "$ZIPPED_FILE" "$DIRECTORY_TO_COMPRESS"
echo $DIRECTORY_TO_COMPRESS "compressed as" $ZIPPED_FILE > /dev/null
echo "Removing temp file and changing directories."
rm -r "$DIRECTORY_TO_COMPRESS"
echo "Renaming to original directory name."
mv mz-mbo-access-temp mz-mbo-access
cd mz-mbo-access
echo "Zip file made and back home again."