Online Video Platform Migration Tool:
====================================

The intended usage of this little library is to be able to migrate video content from one provider to another.
Currently there is only support for BrightCove to Kaltura, however it has been abstracted to allow easy implementation for others.

If you have a specific request why not get in contact me or even better fork the repo.

## BrightCove to Kaltura Usage:

#####Make a request to BrightCove for all your videos:
  
```
// Get all videos
http://api.brightcove.com/services/library?command=find_all_videos&token=0Z2dtxTdJAxtbZ-d0U7Bhio2V1Rhr5Iafl5FFtDPY8E.
```
See here for more info: http://support.brightcove.com/en/video-cloud/docs/javascript-media-api-examples#find_all 

#####Set up the parser:

```
require('Lib/Migrate.php');

// Migrate the file
$migrate = new Migrate();
$migrate->setFile('Data/BrightCove.xml');
$migrate->setOutputType(Migrate::KALTURA);
$migrate->setOutputFileName('Data/Kaltura.xml');
$migrate->go();
```

#####Run the following command (or navigate to the file in a web browser):

```
$ php index.php
```

Now upload the exported file to Kaltura's bulk upload. For more info see here: http://knowledge.kaltura.com/faq/what-bulk-upload
