Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require simply-stream/souls-death
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable
version of this bundle:

```console
$ composer require simply-stream/souls-death
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    SimplyStream\SoulsDeath\SimplyStreamSoulsDeathBundle::class => ['all' => true],
];
```

### Step 3: Configuration

#### Bundle Configuration

```yaml
simplystream_soulsdeath:
    objects:
        user:
            model: '\Your\User\Entity'
            repository: 'Your\User\Repository'

```

#### Doctrine

```yaml
doctrine:
    # ...
    orm:
        # ...
        resolve_target_entities:
            SimplyStream\SoulsDeathBundle\Entity\UserInterface: Your\User\Entity
        mappings:
            # ...
            SimplyStreamSoulsDeathBundle:
                type: xml
```
