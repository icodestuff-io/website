<p align="center"><a href="https://icodestuff.io" target="_blank"><img src="/public/logo.png" width="414" alt="logo"></a></p>

This repo is home to the Icodestuff website built with Laravel

## Local Setup
Homestead is the development environment the website uses to ensure consistency between environments you must use it in order to contribute. 

#### Mac Setup:
##### System Requirements
- Download <a href='https://brew.sh/'>Homebrew</a>
- Download <a href='https://www.virtualbox.org/wiki/Downloads'>VirtualBox6.X</a>
- Download <a href='https://www.vagrantup.com/'>Vagrant</a>

##### Install PHP & Composer
In your terminal run the following commands:
``` 
brew install php
brew install composer
```

##### Install Dependencies & Setup Virtual Machine
``` 
composer install
php vendor/bin/homestead make
ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
```

##### Update Homestead.yml 
Here is an example of my Homestead.yml file: 
~~~yaml
ip: 192.168.10.10
memory: 2048
cpus: 2
provider: virtualbox
authorize: ~/.ssh/id_rsa.pub
keys:
    - ~/.ssh/id_rsa
folders:
    - map: /Users/solomonantoine/icodestuff/website
      to: /home/vagrant/code
sites:
    - map: local.icodestuff.io
      to: /home/vagrant/code/public
      php: "7.4"
      schedule: true
      wildcard: "yes"
databases:
    - icodestuff
    - icodestuff_testing
features:
    - mysql: true
    - mariadb: false
    - ohmyzsh: true
    - webdriver: true
name: icodestuff
hostname: icodestuff
~~~

##### Start Virtual Machine
```
vagrant up
vagrant ssh
```

##### Setup Environment
Setup your local environment just type `setup` into your terminal. This will setup your daemon, redis and
database. 

##### Configure Hosts File
This should happen automatically, but if it doesn't go to: /etc/hosts and paste this: 

`192.168.10.10 local.icodestuff.io`

Be sure the ip address isn't being used more than once. 

### Coding Standards
#### Use IoC container
Please use dependency injection and the IOC container when needing to access services.
Stay away from Facades as they are anti-pattern. Read more <a href='https://programmingarehard.com/2014/01/11/stop-using-facades.html/'>here</a> why not to use Facades.
##### Bad Practice
~~~php
<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function foo()
    {
        Hash::make(); // bad practice
    }
}
~~~
##### Good Practice
~~~php
<?php
namespace App\Http\Controllers;
use Illuminate\Contracts\Hashing\Hasher;
class ExampleController extends Controller
{
    private Hasher $hasher;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }
    public function foo()
    {
        $this->hasher->make();
    }
}
~~~
#### Do not get data from the .env file directly
Pass the data to config files instead and then use the `config()` helper function to use the data in an application.
##### Bad Practice:
~~~php
$apiKey = env('API_KEY');
~~~
##### Good Practice:
~~~php
// config/api.php
'key' => env('API_KEY');
// Use the data
$apiKey = config('api.key');
~~~

#### Static Analysis
To ensure your build doesn't fail, run `./vendor/bin/phpstan analyse` to perform a static analysis check.

#### Testing
Please write tests!! Run `php artisan test` before committing your code, to ensure your build doesn't fail.

### Troubleshooting
#### Composer Memory
If you get an issue about composer memory limit just type `memory` into your terminal. This will create a swap file 
and boost your memory to 4GB. 

#### Redis Failure
Sometimes Redis fails to run, to get it going again just run: `sudo /etc/init.d/redis-server restart`

### Contributing Guide
TODO
