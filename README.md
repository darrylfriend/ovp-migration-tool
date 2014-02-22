ovp-migration-tool
==============

Online Video Platform Migration Tool:

Currently has support for migrating from Brightcove to Kaltura.
With some additional work this could be abstract to translate from any online video provider to another (providing they support export / import functionality)

1. Get an xml file of all your videos from Brightcove

http://api.brightcove.com/services/library?

command=find_all_videos
media_delivery=http
output=mrss
page_number=0
page_size=100
sort_by=PUBLISH_DATE
sort_order=DESC
token='your access token'

2. Run the following command from your command line:

$ php migrate.php -f ‘brightcove_export.xml’ -l 5

Param Options:
-f File
-l Limit

3. Locate the file and upload to Kalturas bulk import section

www.kaltura......


This process can take some time, they will be encoding all of your videos again.