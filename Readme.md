FotoRestBundle
==============

Documentation:

Doc about bundle: https://github.com/antwebes/PhotoRestBundle/blob/master/Doc/index.md

bundle to manage photo with rest

Codes:

'Unable to find Photo entity', '42', '404'

'Access denied', '44', '403'

'You have already voted this photo ', '46', '409'

Upload image by curl
=====================

curl -v -H "Accept: application/json" -H "Content-Type: multipart/form-data" -X POST -F "title=holaaa" -F "image=@/home/pc/Escritorio/torrente222.jpg" http://url/api/ant/photo?access_token={token}