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

```yaml
# app/config/config.yml

ewz_recaptcha:
    public_key:  here_is_your_public_key
    private_key: here_is_your_private_key
    locale_key:  %kernel.default_locale%
```

#Step 5: Enable Translations

To get the correct form labels and placeholders enable translation. In a new Symfony3
project it needs to be uncommented in `app/config/config.yml`:

```yaml
# app/config/config.yml

framework:
    translator:      { fallbacks: ["%locale%"] }
```

And set the locale in `app/config/parameters.yml`:

```yaml
# app/config/parameters.yml

    locale: en
```

Note - so far only english translations have been provided in this bundle. Most of
the defaults are rather ugly.

#Step 6: Configure Required parameters

Make sure your database and mailer parameters are correct in `app/config/parameters.yml`.
You will also need to add `site_name` and `from_email` parameters.

```yaml
# app/config/parameters.yml

parameters:
    database_host: 127.0.0.1
    database_port: null
    database_name: your_database_name
    database_user: your_database_user
    database_password: your_database_user_password
    mailer_transport: smtp
    mailer_host: 127.0.0.1
    mailer_user: your_mail_user
    mailer_password: your_mail_password
    secret: somethingmorerandomthanthis
    locale: en
# JAI User Bundle Configuration
    site_name:  My Great Website
    from_email:  greeter@yourdomain.tld
```

#Step 7: Configure Security

Configure security to your needs (documentation: [Security from the Symfony Book](http://symfony.com/doc/current/book/security.html)).
Here is an example you can start with:

```yaml
# app/config/security.yml

security:
    encoders:
        JAI\UserBundle\Entity\User:
            algorithm: bcrypt
    role_hierarchy:
        ROLE_ADMIN:       ROLE_USER

    # http://symfony.com/doc/current/book/security.html#where-do-users-come-from-user-providers
    providers:
        our_db_provider:
            entity:
                class: JAIUserBundle:User

    firewalls:
        # disables authentication for assets and the profiler, adapt it according to your needs
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        main:
            anonymous: ~
            form_login:
                login_path: jai_user_login
                check_path: jai_user_login
                username_parameter: login[username]
                password_parameter: login[password]
                provider: our_db_provider
            logout:
                path:   /logout
                target: /
            remember_me:
                secret: "preferablysomethingmoresecretthanthis"
                remember_me_parameter: login[remember]
                
    access_control:
        # require ROLE_ADMIN for /admin*
        #- { path: ^/admin, roles: ROLE_ADMIN }
        # require ROLE_USER for /user*
        - { path: ^/user, roles: ROLE_USER }
```

For the moment we've disabled the firewall for the admin page, there's a reason for that.
we'll fix it later.

#Step 8: Initialize the Database

Update the database schema and load the initial roles from the console:

```bash
$ php bin/console doctrine:schema:update --force
$ php bin/console doctrine:fixtures:load
```

#Step 9: Create an Admin User

At this point all of the routes should be working, so you can register a new user by
going to `/register`. If email is configured correctly an activation email will be sent.
Using the link in the activation email will land you on the login page with the message
that your account is now active. Log in as your newly created user. Now go to `/admin/user`
Click on the name of your new user and you will see the profile information displayed.
Find the button under "Available Roles" named "ROLE_ADMIN" and click it. The selected
user will update with the role ROLE_ADMIN.

#Step 10: Secure the Admin Page

Now that we have a user who can access the admin page we'll fix the admin firewall in
`app/config/security.yml`. Just un-comment it:

```yaml
# app/config/security.yml

    access_control:
        # require ROLE_ADMIN for /admin*
        - { path: ^/admin, roles: ROLE_ADMIN }
        # require ROLE_USER for /user*
        - { path: ^/user, roles: ROLE_USER }
```

##Using

This bundle provides the routes `/register`, `/activate`, `/login`, `/user/profile`, `/forgot`,
`/reset`, and `/admin/user`. Their purposes should be self-explanatory. The forms use the
Symfony Forms Component (documentation: [Forms from the Symfony Book](http://symfony.com/doc/current/book/forms.html)),
so they will use the form themes (documentation: [Form Themes from the Symfony Book](https://symfony.com/doc/current/cookbook/form/form_customization.html#cookbook-form-customization-form-themes)).

To make the routes visually consistent with your own site start by overriding
`UserBundle/Resources/views/base.html.twig` to extend one of your own templates
:

```twig
{# UserBundle/Resources/views/base.html.twig #}

{% extends 'base.html.twig' %}
{% block body %}
	{% block user %}{% endblock %}
{% endblock %}
```

##To-Do
=====

#Unit Testing

Currently there aren't any unit tests, and that's just not right.


#Remove Dependency on EWZRecaptchaBundle

It would be better if there was an optional setting like "use captcha" and then further 
settings such as only requiring after a certain amount of flooding, and then specifics
related to whatever captcha implementation is used in the current project.

#User Admin Delete User

I don't know why I didn't notice this omission sooner.

#User Admin Manage Roles

This is a little tricky because ideally you should be able to both add roles and
change the heirarchy. Currently roles are in the database while the heirarchy is in
`app/config/security.yml`.
