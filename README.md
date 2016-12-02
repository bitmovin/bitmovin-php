# [![bitmovin](https://cloudfront-prod.bitmovin.com/wp-content/themes/Bitmovin-V-0.1/images/logo3.png)](http://www.bitmovin.com)
PHP-Client which enables you to seamlessly integrate the Bitmovin API into your existing projects

Installation 
------------

Requirements: PHP 5.6.0 or higher is required

### Composer ###
 
  
To install the api-client with composer, add the following to your `composer.json` file:  
```json
{
"require": 
  {
    "bitmovin/bitmovin-php": "1.3.*"
  }
}
```
Then run `php composer.phar install`

OR

run the following command: `php composer.phar require bitmovin/bitmovin-php:1.3.*`

Example
-----
The following example creates a simple transcoding job and transfers it to a GCS output location ([CreateSimpleEncoding.php](https://github.com/bitmovin/bitmovin-php/tree/master/examples/CreateSimpleEncoding.php)):
```php
<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\GcsOutput;

require_once __DIR__ . '/vendor/autoload.php';

$client = new BitmovinClient('INSERT YOUR API KEY HERE');

// CONFIGURATION
$videoInputPath = 'http://eu-storage.bitcodin.com/inputs/Sintel.2010.720p.mkv';
$gcs_accessKey = 'INSERT YOUR GCS OUTPUT ACCESS KEY HERE';
$gcs_secretKey = 'INSERT YOUR GCS OUTPUT SECRET KEY HERE';
$gcs_bucketName = 'INSERT YOUR GCS OUTPUT BUCKET NAME HERE';
$gcs_prefix = 'path/to/your/output/destination/';

// CREATE ENCODING PROFILE
$encodingProfile = new EncodingProfileConfig();
$encodingProfile->name = 'Test Encoding';
$encodingProfile->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;

// CREATE VIDEO STREAM CONFIG FOR 1080p
$videoStreamConfig_1080 = new H264VideoStreamConfig();
$videoStreamConfig_1080->input = new HttpInput($videoInputPath);
$videoStreamConfig_1080->width = 1920;
$videoStreamConfig_1080->height = 1080;
$videoStreamConfig_1080->bitrate = 4800000;
$videoStreamConfig_1080->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_1080;

// CREATE VIDEO STREAM CONFIG FOR 720p
$videoStreamConfig_720 = new H264VideoStreamConfig();
$videoStreamConfig_720->input = new HttpInput($videoInputPath);
$videoStreamConfig_720->width = 1280;
$videoStreamConfig_720->height = 720;
$videoStreamConfig_720->bitrate = 2400000;
$videoStreamConfig_720->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_720;

// CREATE AUDIO STREAM CONFIG
$audioConfig = new AudioStreamConfig();
$audioConfig->input = new HttpInput($videoInputPath);
$audioConfig->bitrate = 128000;
$audioConfig->rate = 48000;
$audioConfig->name = 'English';
$audioConfig->lang = 'en';
$audioConfig->position = 1;
$encodingProfile->audioStreamConfigs[] = $audioConfig;

// CREATE JOB CONFIG
$jobConfig = new JobConfig();
// ASSIGN OUTPUT
$jobConfig->output = new GcsOutput($gcs_accessKey, $gcs_secretKey, $gcs_bucketName, $gcs_prefix);
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfile;
// ENABLE DASH OUTPUT
$jobConfig->outputFormat[] = new DashOutputFormat();
// ENABLE HLS OUTPUT
$jobConfig->outputFormat[] = new HlsOutputFormat();

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$client->runJobAndWaitForCompletion($jobConfig);
```

For more examples go to our [example page](https://github.com/bitmovin/bitmovin-php/tree/master/examples/).