
#Information

DHorchlerConfigBundle stores configuration settings in an ORM database and makes them administrable in Sonata Admin Bundle.
These settings are similar to those defined in parameters.yml or parameters.ini but can be modified at runtime by a Sonata admin user.


#Installation


##Get the bundle

Let Composer download and install the bundle by first adding it to your composer.json
<pre>

{
    "require": {
        "dhorchler/config-bundle": "dev-master"
    }
}
</pre>
and then running

<pre>php composer.phar update dhorchler/config-bundle</pre>


##Enable the bundle
in app/AppKernel.php
<pre>
public function registerBundles() {
    $bundles = array(
        // ...
        new DHorchler\ConfigBundle\DHorchlerConfigBundle(),
    );
    // ...
}
</pre>
##Create the settings table

You can do this by calling
<pre>
php app/console doctrine:migrations:diff
php app/console doctrine:migrations:migrate
</pre>
or
<pre>
php app/console doctrine:schema:update
</pre>
or how ever you like.

#Configuration

In your app/config/config.yml add
<pre>
sonata_block:
    default_contexts: [cms]
    blocks:
        sonata.admin.block.admin_list:
            contexts:   [admin]
        sonata.block.service.text:
        sonata.block.service.rss:

sonata_admin:
    title:      Sonata Project
    title_logo: /bundles/sonataadmin/logo_title.png
    templates:
        layout:  SonataAdminBundle::standard_layout.html.twig
        ajax:    SonataAdminBundle::ajax_layout.html.twig
        list:    SonataAdminBundle:CRUD:list.html.twig
        show:    SonataAdminBundle:CRUD:show.html.twig
        edit:    DHorchlerConfigBundle::edit.html.twig
    dashboard:
        blocks:
            - { position: left, type: sonata.admin.block.admin_list }
            
services:
      sonata.dh.admin.settings:
        class: DHorchler\ConfigBundle\Admin\ConfigAdmin
        tags:
            - { name: sonata.admin, manager_type: orm, group: 'settings', label: Settings }
        arguments:
            - null
            - DHorchler\ConfigBundle\Entity\Settings
            - DHorchlerConfigBundle:Admin
</pre>


After all this start administering your settings from the Sonata Admin backend.
Currently supported data types: string, integer, float, date, datetime.

#Features:
- validations for different data types
- constrains with easily customizable error texts
- jQuery supported default values
