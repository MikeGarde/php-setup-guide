# PHP Development on MacOs High Sierra

This guide will get your machine ready to develop using PHP, Apache, and XDebug. As a consequence we will also setup a handful
of other tools.

### Show hidden files

```bash
defaults write com.apple.finder AppleShowAllFiles YES
```

You will have to restart finder for the changes to take effect, go to the apple menu, Force Quit, select Finder 
and "Relaunch".


### Homebrew

If needed this will also install Xcode Command Line Tools.

```bash
/usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
brew doctor
```

### Apache

Your macOs shipped with a copy of Apache, unfortunately it is missing key pieces we need so we will install a second
copy of Apache. Note that `apachectl -k graceful` and other `apachectl` commands will not work and you will have to
transition to using `brew`. More on that later. Also, if you're doing this after a clean install you won't be running
apache so the first command may result in an error, you can ignore that error and run the second command.

```bash
sudo apachectl stop
sudo launchctl unload -w /System/Library/LaunchDaemons/org.apache.httpd.plist 2>/dev/null
```

Install Apache using brew

```bash
brew install httpd
```

Start Apache Server

```bash
sudo brew services start httpd
```

You can test by visiting `http://localhost:8080`


### PHP

We are going to install both PHP 5.6 and 7.1, as 7.2 is not yet stable. However your machine will only be able to use one
version at a time.

```bash
brew tap homebrew/php
brew install php71 --with-httpd
brew unlink php71
brew install php56 --with-httpd
```

#### PHP Xdebug

Note that un-linking is not required here because the proper version will be referenced in the php.ini file.

```bash
brew install php71-xdebug
brew install php56-xdebug
```

#### Config Files

Create development folder, note that if you decide to use a different location note it in future instructions you will have to 
reference it properly, I am using the folder `dev` inside my home folder.

```bash
mkdir ~/dev
mkdir ~/dev/logs
git clone https://github.com/MikeGarde/php-setup-guide.git ~/dev/php-setup-guide
```

These commands will allow for easy access to all of our config files.

```bash
ln -s $(brew --prefix)/etc/php/7.1/php.ini ~/dev/php71.ini
ln -s $(brew --prefix)/etc/php/5.6/php.ini ~/dev/php56.ini
ln -s $(brew --prefix)/etc/php/7.1/conf.d/ext-xdebug.ini ~/dev/php71-xdebug.ini
ln -s $(brew --prefix)/etc/php/5.6/conf.d/ext-xdebug.ini ~/dev/php56-xdebug.ini
ln -s $(brew --prefix)/etc/httpd/httpd.conf ~/dev/httpd.conf
ln -s $(brew --prefix)/etc/httpd/extra/httpd-vhosts.conf ~/dev/httpd-vhosts.conf
ln -s ~/dev/php-setup-guide/localhost.php ~/dev/index.php
```

### dnsmasq

This will route all traffic ending with `.test` to your machine so that you don't have to setup each project and domain.
Note, you don't have to use `.test`, you can replace it with whatever you want however `.dev` was purchased by Google and
Chrome may not honor your desired actions in the future. Other options are `.localhost` `.example` where as `.local` may 
compete with other devices on your network.

After the install when you create the directory `etc` you may get an error if the directory already exists however this should 
work, you can verify by running `vi $(brew --prefix)/etc/dnsmasq.conf` (:q to quit) however it shouldn't be necessary.

```bash
brew install dnsmasq
cd $(brew --prefix); mkdir etc; echo 'address=/.test/127.0.0.1' > etc/dnsmasq.conf'
sudo cp -v $(brew --prefix dnsmasq)/homebrew.mxcl.dnsmasq.plist /Library/LaunchDaemons
sudo launchctl load -w /Library/LaunchDaemons/homebrew.mxcl.dnsmasq.plist
sudo mkdir /etc/resolver
sudo bash -c 'echo "nameserver 127.0.0.1" > /etc/resolver/test'
ping -c 1 me.test # will demonstrate that your machine will resolve me.test to your local machine
cd ~
```

### Apache Setup

Now that we are routing `.test` traffic to your machine we will get apache to resolve it to the proper directory within 
your development folder.

Open `~/dev/php-setup-guide/httpd.conf` and replace all `YOUR_HOME_FOLDER` references with your home folder location. Next 
use the resulting file and replace `~/dev/httpd.conf` with it.

Open `~/dev/httpd-vhosts.conf`, you can delete everything and replace it with this.

```apacheconfig
<VirtualHost *:80>
    DocumentRoot "/Users/YOUR_HOME_FOLDER/dev"
    ServerName localhost
    ServerAlias localhost
</VirtualHost>

<VirtualHost *:80>
    DocumentRoot "/Users/YOUR_HOME_FOLDER/dev/php-setup-guide"
    ServerAlias php-setup-guide.test
    ErrorLog "/Users/YOUR_HOME_FOLDER/dev/logs/php-setup-guide-error_log"
    CustomLog "/Users/YOUR_HOME_FOLDER/dev/logs/php-setup-guide-access_log" common
</VirtualHost>
```

Anytime you modify `httpd.conf` or `httpd-vhosts.conf` file you will need to restart apache,

```bash
brew services restart httpd
```

### xdebug Setup

Add the following to both of your `~/dev/xdebug.ini` files, the last two lines are for more detailed debugging but be aware, these can 
generate large files.

```bash
xdebug.remote_enable=1
xdebug.remote_port=9000
xdebug.remote_host=127.0.0.1
;xdebug.profiler_enable=1
;xdebug.profiler_output_dir="/Users/YOURNAME/dev/logs/xdebug"
```

```bash
brew services restart httpd
```

If there is a problem with using port 9000 you can change it but note it when setting up xdebug in your IDE.

### Composer

Follow instructions at [Composer](https://getcomposer.org/download/), when done make it globally available by moving it.

```bash
mv composer.phar /usr/local/bin/composer
```

## And Your Done, Lets Test

If you haven't already...

```bash
brew services restart httpd
```

[http://php-setup-guide.test/](http://php-setup-guide.test/) will show you a hello world while 
[http://localhost/](http://localhost/) will give you phpinfo and links to your projects.


### Adding Projects

Simply open `~/dev/httpd-vhosts.conf` and copy the `php-setup-guide example`, if you don't need separated logging simply remove 
the ErrorLog and/or CustomLog lines.

### Switching between PHP 5.6 and 7.1

Switching to 5.6 | Switching to 7.1
---------------- | ----------------
`brew unlink php71` | `brew unlink php56`
`brew link php56` | `brew link php71`

Update httpd.conf appropriately.

```apacheconfig
#LoadModule php5_module /usr/local/opt/php56/libexec/apache2/libphp5.so
LoadModule php7_module /usr/local/opt/php71/libexec/apache2/libphp7.so
```

```bash
brew services restart httpd
```

## Extra Stuff I Use

### AWS CLI

```bash
brew install awscli
aws configure
```

### Python 3

```bash
brew install python3
curl -O https://bootstrap.pypa.io/get-pip.py
sudo python get-pip.py
rm get-pip.py
```