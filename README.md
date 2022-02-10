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

To make the ownership of this bundles entities work, you need to configure the user and repository you use first.

```yaml
# config/packages/simplystream_souls_death.yaml

simplystream_soulsdeath:
    objects:
        user:
            model: '\Your\User\Entity'
            repository: 'Your\User\Repository'

```

#### Doctrine

Second, you need to map your user entity to the interface this bundle provides

```yaml
# config/packages/doctrine.yaml

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

#### Routing

To make use of the routes this bundle provides, simply configure them in your Symfony application as follows

```yaml
# config/routes/simplystream_souls_death.yaml

_soulsdeath_tracker:
    resource: '@SimplyStreamSoulsDeathBundle/src/Resources/config/routes.xml'
    prefix: /tracker

```

### Step 4: Using the bundle

This bundle provides all the entities, controller and events you need to create new trackers per user, processes the commands from chat messages
(e.g. Twitch or YouTube chat) or by simply adding counts to your tracker by adding them through the frontend interface or API.

#### Processing chat messages

Use the CommandExecutionEvent to handle commands sent from chats like Twitch, YouTube or Discord. All you need to do is
provide the logic to receive a chat message and send the CommandExecutionEvent. The EventSubscriber of this bundle
will handle the rest.

The event needs the following values:

```PHP
    public function __construct(string $command, string $channel, array $chatMessage)
    {
        $this->command = $command;
        $this->channel = $channel;
        $this->chatMessage = $chatMessage;
    }
```

**Command**:      The command that'll be executed.

**Channel**:      The channel this chatmessage has been received from. Can be used to send an answer to, will also be re-send when 
                  the command execution finished (successful or not)

**Chat Message**: The message a user has sent in your chat

### Events

| Name                                            | Description                                                                                                                            |
|-------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------|
| **CommandExecutionEvent**                       | This event should be executed by you. This will trigger the processing of chat messages. **Parameters**: Command, Channel, ChatMessage |
| **CommandExecutionSuccessEvent**                | Will be thrown when the command execution was successful. **Parameters**: Counter, User, Channel                                       |
| **CommandExecutionFailureEvent** (Not used yet) | Will be thrown when the command execution was not successful. **Parameters**: Counter, User, Channel, ?Error                           |
