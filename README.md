#Information

Branch 2.1 was tested with Symfony2.1

DHorchlerConfigBundle stores configuration settings in an ORM database and makes them administrable in Sonata Admin Bundle.
These settings can be used anywhere in your project and can be modified at runtime by a Sonata admin user.

#Features:
- individual validations for different data types
- individual constrains with easily customizable error texts
- jQuery supported form field default values

###Currently supported data types:
string, integer, float, date, datetime, choice, multiplechoice.

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


After all this start managing your settings from the Sonata Admin backend.


#Usage example:
<pre>
$this->em = $this->getDoctrine()->getEntityManager();
$settingsRaw = $this->em->createQueryBuilder()
    ->select('s.name, s.currentValue')
    ->from('DHorchlerConfigBundle:Settings', 's')
    ->getQuery()
    ->getResult();
foreach ($settingsRaw AS $setting) $settings[$setting['name']] = $setting['currentValue']
</pre>

#Preview:

![Screen shot list settings](https://raw.github.com/DHorchler/ConfigBundle/master/srceen_shot_list_settings.png)

![Screen shot edit integer settings](https://github.com/DHorchler/ConfigBundle/blob/master/srceen_shot_edit_integer_setting.png)

![Screen shot edit date settings](https://github.com/DHorchler/ConfigBundle/blob/master/srceen_shot_edit_date_setting.png)

#Todo:
- add more tests
- client side validation

