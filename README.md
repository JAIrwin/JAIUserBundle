JAIUserBundle
=============

This bundle provides user management and forms for Symfony3 Projects.

##Installation

#Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require jairwin/user-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

#Step 2: Enable the Bundle

Enable the bundle by adding it to the list of registered bundles in `app/AppKernel.php`:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new JAI\UserBundle\JAIUserBundle(),
        );

        // ...
    }

    // ...
}
```

#Step 3: Add Routing

To add the provided routes  update 
`app/config/routing.yml`:

```yaml
# app/config/routing.yml

jai_user:
    resource: "@JAIUserBundle/Resources/config/routing.yml"
    prefix:   /
```

#Step 4: Configure ReCaptcha

This bundle uses the EZWRecaptchaBundle which is configured in `app/config/config.yml`
(documentation: [EWZRecaptcha on GitHub](https://github.com/excelwebzone/EWZRecaptchaBundle)):

``` yaml
# app/config/config.yml

ewz_recaptcha:
    public_key:  here_is_your_public_key
    private_key: here_is_your_private_key
    locale_key:  %kernel.default_locale%
```

#Step 5: Enable Translations

To get the correct form labels and placeholders enable translation. In a new Symfony3
project it needs to be uncommented in `app/config/config.yml`:

``` yaml
# app/config/config.yml

framework:
    translator:      { fallbacks: ["%locale%"] }
```

And set the locale in `app/config/parameters.yml`:

``` yaml
# app/config/parameters.yml

    locale: en
```

Note - so far only english translations have been provided in this bundle. Most of
the defaults are rather ugly.

#More Steps



##Using



##To-Do
=====

#Finish The Docs

#Unit Testing

Currently there aren't any unit tests, and that's just not right.


#Remove Dependency on EZWRecaptchaBundle

It would be better if there was an optional setting like "use captcha" and then further 
settings such as only requiring after a certain amount of flooding, and then specifics
related to whatever captcha implementation is used in the current project.

#User Admin Delete User

I don't know why I didn't notice this omission sooner.
