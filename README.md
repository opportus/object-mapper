# opportus/object-mapper

Provides a flexible and extensible object mapping system.

Contributions are welcome...

To do before BETA Release:

- [ ] Introduce mapping recursion
- [ ] Turn private (actually protected) a maximum of methods and properties

## Index

- [Introduction](#introduction)
- [Integrations](#integrations)
- [Installation](#installation)
    - [Step 1 - Download the Package](#step-1---download-the-package)
    - [Step 2 - Resolve Services Dependencies](#step-2---resolve-services-dependencies)
- [Mapping Objects](#mapping-objects)
    - [Automatic Mapping](#automatic-mapping)
    - [Manual Mapping](#manual-mapping)
    - [Static Mapping](#static-mapping)

## Introduction

Here's how you can basically use the API:

```php
class User
{
    public $username = 'toto'
    private $email = 'toto@example.com';

    public function getCountry()
    {
        return 'France';
    }
}

class Contributor
{
    public $username;
    private $email;

    public function getEmail()
    {
        return $this->email;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }
}

$contributor = $objectMapper->map(new User(), new Contributor());

echo $contributor->username; // toto
echo $contributor->getEmail(); // toto@example.com
echo $contributor->country // France
```

## Integrations

- Symfony 4 => [oppotus/object-mapper-bundle](https://github.com/opportus/ObjectMapperBundle)
- Reference here your own integrations by requesting it at [opportus@gmail.com](mailto:opportus@gmail.com)

## Installation

### Step 1 - Download the Package

Open a command console, enter your project directory and execute:

```console
$ composer require opportus/object-mapper
```

### Step 2 - Resolve Services Dependencies

The library contains 6 services. 4 of them require to be instantiated with their respective dependencies which are other lower level services among those 6.

Below are listed from the lower to the higher level service, their respective dependencies as constructor's parameters:

```php
namespace Opportus\ObjectMapper;

Map\Route\RouteBuilder::__construct(Map\Route\Point\PointFactoryInterface $pointFactory);

Map\Definition\MapDefinitionBuilder::__construct(Map\Route\RouteBuilderInterface $routeBuilder);

Map\MapBuilder::__construct(Map\Definition\MapDefinitionBuilderInterface $mapDefinitionBuilder);

ObjectMapper::__construct(Map\MapBuilderInterface $mapBuilder);
```

In order to achieve this properly, you should use a DI container or instantiate manually the services in your composition root and register them.

## Mapping Objects

Mapping objects to objects is done via the main [`ObjectMapper`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapper.php) service's method:

```php
ObjectMapper::map($sources, $targets, ?MapInterface $map = null)
```

**Parameters**

The `$sources` parameter can be:

- An *object* holding data to map to the target(s)
- An *array* of objects holding data to map to the target(s)

The `$targets` parameter can be:

- An *object* to map the source(s) data to
- A *string* being the class name of an object to instantiate and to map the source(s) data to
- An *array* of single or both type of element above

The `$map` parameter can be:

- A *null*
- An instance of *`MapInterface`*

**Returns**

The return value depends on the type of the `$targets` parameter and on the `$map` parameter:

- If `$targets` is an object, the very same object will be returned
- If `$targets` is a class name, an object of this type will be returned
- If `$targets` is an array of single or both type of element above, the same array of both type of return value above will be returned
- If the map does not contain any route to a target element, the target element will be returned as it has been passed

### Automatic Mapping

A basic example about how to *automatically* map one `User` to one `UserDto` and vice-versa:

```php
$objectMapper; // Opportus\ObjectMapper\ObjectMapper service instance...

class User
{
    private $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function getUsername() : string
    {
        return $this->username;
    }
}

class UserDto
{
    public $username;
}

$user    = new User('foobar');
$userDto = new UserDto();

// Map the User instance to the UserDto instance...
$objectMapper->map($user, $userDto);

echo $userDto->username; // Outputs 'foobar'...

// Map back the UserDto instance to one new User...
$user = $objectMapper->map($userDto, 'User');

echo $user->getUsername(); // Outputs 'foobar'...
```

The automatic mapping allows to map seemlessly objects to objects.

Calling the `ObjectMapper::map()` method presented earlier, with its `$map` parameter set on `null` makes the method build then use a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) composed of the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php).

The default `PathFindingStrategy` behavior consists of guessing what is the appropriate point of the source class to connect to each point of the target class. The connected `SourcePoint` and `TargetPoint` compose then a [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) which is followed by the `ObjectMapper::map()`.

For the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php), a `TargetPoint` can be:

- A public property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php))
- A parameter of a public setter or a public constructor ([`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php))

The corresponding `SourcePoint` can be:

- A public property having for name the same as the target point ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php))
- A public getter having for name `'get'.ucfirst($targetPointName)` and requiring no argument ([`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php))

#### Custom PathFindingStrategy

Sometime, the default `PathFindingStrategy` may not be the most appropriate behavior anymore. In this case, you can implement your own [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php), and in order to make it guess the appropriate routes, reverse-engineer the classes passed as argument to the strategy's single method:

```php
PathFindingStrategyInterface::getRouteCollection(string $sourceClassFqn, string $targetClassFqn) : RouteCollection;
```

**Parameters**

`$sourceClassFqn` is the Fully Qualified Name of the source class to map from.

`$targetClassFqn` is the Fully Qualified Name of the target class to map source's data to.

**Returns**

A [`RouteCollection`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/RouteCollection.php) connecting the source's points with the tagets's points.

**Example**

```php
class MyPathFindingStrategy implements PathFindingStrategyInterface
{
    // ...

    public function getRouteCollection(string $sourceClassFqn, string $targetClassFqn) : RouteCollection
    {
        // Custom algorithm...
    }

    // ...
}

// Pass to the map builder the strategy you want it to compose the map of...
$map = $objectMapper->getMapBuilder()->buildMap(new MyPathFindingStrategy());

echo $map->getPathFindingStrategyType(); // Outputs 'MyPathFindingStrategy'

// Use the map...
$user = $objectMapper->map($userDto, 'User', $map);

// ...
```

### Manual Mapping

A basic example about how to *manually* map one `User` to one `ContributorDto` and vice-versa:

```php
$objectMapper; // Opportus\ObjectMapper\ObjectMapper service instance...

class User
{
    private $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function getUsername() : string
    {
        return $this->username;
    }
}

class ContributorDto
{
    public $name;
}

$user           = new User('foobar');
$contributorDto = new ContributorDto();

// Build the map...
$map = $objectMapper->getMapBuilder()
    ->prepareMap()
    ->addRoute('User::getUsername()', 'ContributorDto::$name')
    ->buildMap()
;

// Map the User instance to the ContributorDto instance...
$objectMapper->map($user, $contributorDto, $map);

echo $contributorDto->name; // Outputs 'foobar'...

// Build the map...
$map = $objectMapper->getMapBuilder()
    ->prepareMap()
    ->addRoute('ContributorDto::$name', 'User::__construct()::$username')
    ->buildMap()
;

// Map back the ContributorDto instance to one new User...
$user = $objectMapper->map($contributorDto, 'User', $map);

echo $user->getUsername(); // Outputs 'foobar'...
```

The manual mapping requires a little more effort than the automatic mapping but gives you unlimited control over which source point to map to which target point.

Building a map manually requires you to use the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) API. The `MapBuilder` is an immutable service which implement a fluent interface.

Building a map manually is actually nothing more than adding routes to a map via the following method:

```php
MapBuilder::addRoute(string $sourcePointFqn, string $targetPointFqn) : MapBuilderInterface
```

**Parameters**

The `$sourcePointFqn` parameter is a *string* representing the Fully Qualified Name of a source point which can be:

- A public, protected or private property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)) represented by its FQN having for syntax `'Class::$property'`
- A public, protected or private method requiring no argument ([`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php)) represented by its FQN having for syntax `'Class::method()'`

The `$targetPointFqn` parameter is a *string* representing the Fully Qualified Name of a target point which can be:

- A public, protected or private property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)) represented by its FQN having for syntax `'Class::$property'`
- A parameter of a public, protected or private method ([`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php)) represented by its FQN having for syntax `'Class::method()::$parameter'`

**Returns**

The method returns a **new** instance of the `MapBuilder`.

### Static Mapping

This library aims to remain dependence-less and as much simple as possible, therefore it doesn't ship any configuration loading system. However, this library has been designed with the static mapping functionality in mind.

For instance, the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) uses internally the [`MapDefinitionBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/Definition/MapDefinitionBuilder.php). In fact, when building manually a map via `MapBuilder::addRoute()`, you're dynamically building a [`MapDefinition`](https://github.com/opportus/object-mapper/blob/master/src/Map/Definition/MapDefinition.php).

To implement a static mapping system:

1. Validate and load map configurations *by your own way*
2. Build a `MapDefinition` from the map configuration with the help of the `MapDefinitionBuilder`
3. Register the `MapDefinition` with the help of the [`MapDefinitionRegistry`](https://github.com/opportus/object-mapper/blob/master/src/Map/Definition/MapDefinitionRegistry.php)

Then the client will be able to get back the registered map definiton corresponding to its static map configuration and finally build a map from it.

**Example**

```php
$mapConfig; // Say it's an array loaded from a PHP map configuration file...

// Build a map definition from the map configuration...
$mapDefinitionBuilder = $mapDefinitionBuilder
    ->prepareMapDefinition()
    ->setId($mapConfig['id']) // Say 'id' value = '1'...
;

foreach ($mapConfig['routes'] as $route) {
    $mapDefinitionBuilder = $mapDefinitionBuilder->addRoute($route['source_point'], $route['target_point']);
}

$mapDefinition = $mapDefinitionBuilder->buildMapDefinition();

// Register the map definition for later use by the client...
$mapDefinitionRegistry->registerMapDefinition($mapDefinition); // Throws an exception if a different map definition with same ID is already registered...

// Fetch back the map definition later in the client code...
$mapDefinition = $mapDefinitionRegistry->getMapDefinition('1'); // Found by its ID...

// Build a map from its definition...
$map = $mapBuilder->buildMap($mapDefinition);

// Use the map...
$targets = $objectMapper->map($sources, $targets, $map);
```
