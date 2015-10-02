# Development Site Banner

This is a simple package to display a banner at the top of all pages on your website displaying key dev information:

* Environment
* Git branch
* Last commit
 
## Usage
 
You first need to set the current environment name for your application in the variable ```$environmentName``` 
 
Then output the site banner via:
 
```
$banner = new \Studio24\SiteBanner($environmentName);
echo $banner->getBanner();
```
 
## Credits

Credit to Sebastian Bergmann, the lightweight Git reading functionality in this package is based from his work in https://github.com/sebastianbergmann/git

Warning icon from Open Iconic — www.useiconic.com/open