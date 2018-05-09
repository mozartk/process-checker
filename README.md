# process-checker
<p align="left">
<a href='https://opensource.org/licenses/MIT'><img src="https://img.shields.io/travis/php-v/mozartk/process-checker.svg"></a>
<a href="https://travis-ci.org/mozartk/process-checker?branch=master"><img src="https://travis-ci.org/mozartk/process-checker.svg?branch=master" alt="Build Status"></a>
<a href='https://coveralls.io/github/mozartk/process-checker?branch=master'><img src='https://coveralls.io/repos/github/mozartk/process-checker/badge.svg?branch=master' alt='Coverage Status' /></a>
<a href='https://opensource.org/licenses/MIT'><img src='https://img.shields.io/badge/License-MIT-green.svg' alt='Coverage Status' /></a>
<a href='OJDDEV.md'><img src="https://img.shields.io/badge/OJD-mozartk-green.svg" alt="OJD" title="WE ARE OJD"></a>
</p>
  
Now you can easily check the process information with php.  

## Installation
Comming soon.
  
## Basic Usage
### How to run
First, you need a config file in JSON format.  
```json  
{  
  "processList":[  
    "php-fpm",
    "httpd"
  ],
  "outputMode":"array" //or json,yaml,ini
}
```  
  
  
And run tue php script : 
```php
require "vendor/autoload.php";

use mozartk\ProcessChecker\ProcessChecker;

$processHandler = new ProcessChecker();
$processHandler->setConfigPath("config.json");
$data = $processHandler->run();

print_r($data); 
```  

### Results
```
Array
(
    [php-fpm] => Array
        (
            [0] => Array
                (
                    [name] => /bin/php-fpm
                    [name_w] =>
                    [cputime] => 0:22.74
                    [pid] => 65843
                    [running] => 1
                )

            [1] => Array
                (
                    [name] => /bin/php-fpm
                    [name_w] =>
                    [cputime] => 0:00.01
                    [pid] => 65846
                    [running] => 1
                )
                ...
        )
)

```
  
If you wants to get Yaml Results, change the **outputMode** value in config.json to **yaml**
```yaml  
php-fpm:
    -
        name: '/bin/php-fpm'
        name_w: false
        cputime: '0:18.63'
        pid: 65843
        running: true
    -
        name: '/bin/php-fpm'
        name_w: false
        cputime: '0:00.00'
        pid: 65846
        running: true
httpd:
    -
        name: '/bin/httpd -D FOREGROUND'
        name_w: false
        cputime: '0:44.38'
        pid: 94
        running: true
...
```  
  
  
And you can get json results  
```json  
{  
  "php-fpm":[  
    {  
      "name":"/bin/php-fpm",
      "name_w":false,
      "cputime":"0:18.61",
      "pid":12345,
      "running":true
    }
  ],
  "httpd":[  
    {  
      "name":"/bin/httpd -D FOREGROUND",
      "name_w":false,
      "cputime":"0:44.37",
      "pid":3360,
      "running":true
    },
    {  
      "name":"/bin/httpd -D FOREGROUND",
      "name_w":false,
      "cputime":"0:00.00",
      "pid":8801,
      "running":true
    }
  ]
}
```

And you can get **ini** type results too
```ini  
[php-fpm(65843)]  
name = "/bin/php-fpm"  
name_w = 0  
cputime = "0:18.63"  
pid = 65843  
running = 1  
  
[php-fpm(65846)]  
name = "/bin/php-fpm"  
name_w = 0  
cputime = "0:00.01"  
pid = 65846  
running = 1   
```    

## License
Made by mozartk.  
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.