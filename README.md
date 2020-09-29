[![License](https://poser.pugx.org/opportus/object-mapper/license)](https://packagist.org/packages/opportus/object-mapper)
[![Latest Stable Version](https://poser.pugx.org/opportus/object-mapper/v/stable)](https://packagist.org/packages/opportus/object-mapper)
[![Latest Unstable Version](https://poser.pugx.org/opportus/object-mapper/v/unstable)](https://packagist.org/packages/opportus/object-mapper)
[![Build Status](https://travis-ci.com/opportus/object-mapper.svg?branch=master)](https://travis-ci.com/opportus/object-mapper)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d3f5178323844f59a6ef5647cb11d9d7)](https://www.codacy.com/manual/opportus/object-mapper?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=opportus/object-mapper&amp;utm_campaign=Badge_Grade)

## Index

- [Index](#index)
- [Use cases](#use-cases)
- [Roadmap](#roadmap)
  - [v1.0.0 (stable)](#v100-stable)
- [Integrations](#integrations)
- [Setup](#setup)
  - [Step 1 - Installation](#step-1---installation)
  - [Step 2 - Initialization](#step-2---initialization)
- [Mapping](#mapping)
  - [How it works](#how-it-works)
  - [Automatic mapping](#automatic-mapping)
    - [Custom mapping](#custom-mapping)
  - [Manual mapping](#manual-mapping)
    - [Via map builder API](#via-map-builder-api)
    - [Via map definition preloading](#via-map-definition-preloading)
  - [Check point](#check-point)
  - [Recursion](#recursion)

## Use cases

Use this solution for mapping objects via extensible strategies and controls.

Leverage this solution by delegating to it generic controls over source and
target objects to:
 
-   Decouple them from your codebase
-   Dynamically define control flow over data being transferred
-   Dynamically define target model

This project aims to provide a standard core system to many types of other
system such as:

-   Data transformation
-   ORM
-   Form handling
-   Serialization
-   Interlayer data mapping
-   ...

## Roadmap

To develop this solution faster, [contributions](https://github.com/opportus/object-mapper/blob/master/.github/CONTRIBUTING.md) are welcome...

### v1.0.0 (stable)

-   Implement recursion control system
    `PathFinding` strategy
-   Implement last unit tests to reach 100% coverage

## Integrations

-   Symfony 4 application => [oppotus/object-mapper-bundle](https://github.com/opportus/ObjectMapperBundle)
-   Reference here your own integrations

## Setup

### Step 1 - Installation

Open a command console, enter your project directory and execute:

```console
$ composer require opportus/object-mapper
```

### Step 2 - Initialization

This library contains 4 services. 3 of them require a single dependency which is
another lower level service among those 4:

```php
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\ObjectMapper;

$pointFactory = new PointFactory();
$routeBuilder = new RouteBuilder($pointFactory);
$mapBuilder   = new MapBuilder($routeBuilder);
$objectMapper = new ObjectMapper($mapBuilder);
```

In order for the *object mapper* to get properly initialized, each of its
services must be instantiated such as above.

By design, this solution does not provide "helpers" for the instantiation of
its own services which is much better handled the way you're already
instantiating your own services, with a DIC system or whatever.

## Mapping

Mapping object to object is done via the main [`ObjectMapper`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapper.php)
service's method described below:

```php
ObjectMapperInterface::map(object $source, $target, ?MapInterface $map = null): ?object;
```

**Parameters**

`$source` must be an `object` to map data from.

`$target` must be an `object` (or a `string` being the Fully Qualified Name of a
class to instantiate and) to map data to.

`$map` must be a `null` or an instance of [`MapInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapInterface.php).
If it is `null`, the method builds and uses a map composed of the default
 [`PathFinder`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/StaticPathFinder.php)
 strategy.

**Returns**

Either:

-   `null` if the map has no route connecting source points with target points
-   `object` which is the (instantiated and) updated target

### How it works

The `ObjectMapper` method presented above iterates through each *route* that it
gets from the *map*. Doing so, the method assigns the value of the current
*route*'s *source point* to its *target point*. Optionally, on the route,
*check points* can be defined in order to control the value from the
*source point* before it reaches the *target point*.

A [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Route/Route.php)
is defined by and composed of its *source point*, its *target point*, and its
*check points*.

A *source point* can be either:

-   A property
-   A method

A *target point* can be either:

-   A property
-   A method parameter

A *check point* can be any instance of [`CheckPointInterface`](https://github.com/opportus/object-mapper/blob/master/src/Point/CheckPointInterface.php).

These routes can be defined [automatically](#automatic-mapping) via a *map*'s
[`PathFinderInterface`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/PathFinderInterface.php)
and/or [manually](#manual-mapping) via:

-   [Map builder API](#via-map-builder-api)
-   [Map definition preloading](#via-map-definition-preloading)

### Automatic mapping

A basic example of how to automatically map `User`'s data to `UserDto` and
vice-versa:

```php
class User
{
    private $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}

class UserDto
{
    public $username;
}

$user    = new User('Toto');
$userDto = new UserDto();

// Map the data of the User instance to the UserDto instance
$objectMapper->map($user, $userDto);

echo $userDto->username; // Toto

// Map the data of the UserDto instance to a new User instance
$user = $objectMapper->map($userDto, User::class);

echo $user->getUsername(); // Toto
```

Calling the `ObjectMapper::map()` method presented earlier with its `$map`
parameter set on `null`, makes the method build then use a `Map` composed of the
default `StaticPathFinder`.

This default `StaticPathFinder` strategy consists of guessing what is the
appropriate point of the source class to connect to each point of the target
class. The connected *source point* and *target point* compose then a *route*
which is followed by the `ObjectMapper::map()` method.

For the default [`StaticPathFinder`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/PathFinder.php)
strategy, a *target point* can be:

-   A public property ([`PropertyStaticTargetPoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/PropertyStaticTargetPoint.php))
-   A parameter of a public *setter* or constructor ([`MethodParameterStaticTargetPoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/MethodParameterStaticTargetPoint.php))

The corresponding *source point* can be:

-   A public property having for name the same as the target point ([`PropertyStaticSourcePoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/PropertyStaticSourcePoint.php))
-   A public *getter* having for name `'get'.ucfirst($targetPointName)` and
    requiring no argument ([`MethodStaticSourcePoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/MethodStaticSourcePoint.php))

#### Custom mapping

The default `StaticPathFinder` strategy presented above implements a specific
mapping logic. In order for a *pathfinder* to generically map differently
typed objects, it has to follow a certain convention. You can map differently
typed objects generically only accordingly to this convention.

`PathFinderInterface` allows implementing custom mapping logic...

You can try to extract from your domain *control patterns* over your objects.
Implement then each of theses patterns as a type of `PathFinderInterface`.
Doing so effectively, you will decouple these objects from your codebase...
Indeed, when the controled objects change, the control won't. Such generic
controls are very powerful. Later, we will see how to leverage furthermore
this solution by combining this concept with another one.

For concrete example of how to implement `PathFinderInterface`, refer to the
default [`StaticPathFinder`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/StaticPathFinder.php)
or [`DynamicPathFinder`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/DynamicPathFinder.php)
implementations.

Below is described the single method of [`PathFinderInterface`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/PathFinderInterface.php):

```php
PathFinderInterface::getRoutes(SourceInterface $source, TargetInterface $target): RouteCollection;
```

**Parameters**

`$source` An instance of [`SourceInterface`](https://github.com/opportus/object-mapper/blob/master/src/SourceInterface.php)
which encapsulate and represent the *source* to map data from.

`$target` An instance of [`TargetInterface`](https://github.com/opportus/object-mapper/blob/master/src/TargetInterface.php)
which encapsulate and represent the *target* to map data to.

**Returns**

[`RouteCollection`](https://github.com/opportus/object-mapper/blob/master/src/Route/RouteCollection.php)
connecting the *source points* with the *target points*.

**Example**

```php
class MyPathFinder implements PathFinderInterface
{
    public function getRoutes(SourceInterface $source, TargetInterface $target): RouteCollection
    {
        $source->getReflection();
        $target->getReflection();
        
        $routes = new RouteCollection();

        // Custom mapping algorithm

        return $routes;
    }
}

// Pass to the map builder pathfinders you want it to compose the map of
$map = $mapBuilder
    ->addStaticPathFinder()
    ->addDynamicPathFinder()
    ->addPathFinder(new MyPathFinder())
    ->getMap();

// Use the map
$user = $objectMapper->map($userDto, User::class, $map);
```

### Manual mapping

If in your context, such as walked through in the previous
"[automatic mapping](#automatic-mapping)" chapter, a mapping strategy definition
does not scale well, or is either impossible or overkill, you can manually map
the *source* to the *target*.

There are multiple ways to define manually the mapping such as introduced in the
2 next sub-chapters:

-   [Via map builder API](#via-map-builder-api).
-   [Via map definition preloading](#via-map-definition-preloading).

#### Via map builder API

The [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php)
is an immutable service which implement a fluent interface.

A basic example of how to manually map `User`'s data to `ContributorDto` and
vice-versa with the `MapBuilder`:

```php
class User
{
    private $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}

class ContributorDto
{
    public $name;
}

$user = new User('Toto');
$contributorDto = new ContributorDto();

// Define the route manually
$map = $mapBuilder
    ->getRouteBuilder()
        ->setStaticSourcePoint('User.getUsername()')
        ->setStaticTargetPoint('ContributorDto.$name')
        ->addRouteToMapBuilder()
        ->getMapBuilder()
    ->getMap();

// Map the data of the User instance to the ContributorDto instance
$objectMapper->map($user, $contributorDto, $map);

echo $contributorDto->name; // Toto

// Define the route manually
$map = $mapBuilder
    ->getRouteBuilder()
        ->setStaticSourcePoint('ContributorDto.$name')
        ->setStaticTargetPoint('User.__construct().$username')
        ->addRouteToMapBuilder()
        ->getMapBuilder()
    ->getMap();

// Map the data of the ContributorDto instance to a new User instance
$user = $objectMapper->map($contributorDto, User::class, $map);

echo $user->getUsername(); // 'Toto'
```

Such as in the example above, you can add routes to a map with the methods
described below:

---

```php
RouteBuilderInterface::setStaticSourcePoint(string $sourcePointFqn): RouteBuilderInterface
```

**Parameter**

`$sourcePointFqn` must be a `string` representing the Fully Qualified Name of a
*source point* which can be:

-   A public, protected or private property ([`PropertyStaticSourcePoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/PropertyStaticSourcePoint.php))
    represented by its FQN having for syntax `'My\Class.$property'`.
-   A public, protected or private method requiring no argument ([`MethodStaticSourcePoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/MethodStaticSourcePoint.php))
    represented by its FQN having for syntax `'My\Class.method()'`.

**Returns**

A **new** instance of [`RouteBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Route/RouteBuilder.php).

---

```php
RouteBuilderInterface::setStaticTargetPoint(string $targetPointFqn): RouteBuilderInterface
```

**Parameter**

`$targetPointFqn` must be a `string` representing the Fully Qualified Name of a
*target point* which can be:

-   A public, protected or private property ([`PropertyStaticTargetPoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/PropertyStaticTargetPoint.php))
    represented by its FQN having for syntax `'My\Class.$property'`.
-   A parameter of a public, protected or private method ([`MethodParameterStaticTargetPoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/MethodParameterStaticTargetPoint.php))
    represented by its FQN having for syntax `'My\Class.method().$parameter'`.

**Returns**

A **new** instance of [`RouteBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Route/RouteBuilder.php).

---

#### Via map definition preloading

[Via the map builder API](#via-map-builder-api) presented above, we define the
map (adding to it routes) *on the go*. There is another way to define the map,
preloading its definition.

While this library is designed with *map definition preloading* in mind, it
does not provide a way to effectively preload a *map definition* which could be:

-   Any type of file, commonly used for configuration (XML, YAML, JSON, etc...),
    defining statically a map
-   Any type of PHP routine defining dynamically a map
-   ...

So in order to create routes to compose the map of, you can:

-   Parse your map configuration files, extract from them *source point* and
    *target point* and inject them as is into the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilderInterface.php)
-   Implement some sort of map generator routine making use itself of the
    `MapBuilder` service instance

Because an *object mapper* has a wide range of different use case contexts, this
solution is designed as a minimalist, flexible, and extensible core in order to
get **integrated**, **adapted**, and **extended** seamlessly into any of these
contexts. Therefore, this solution delegates *map definition preloading* to the
integrating higher level system which can make use contextually of its own DIC,
configuration, and cache systems required for achieving
*map definition preloading*.

[opportus/object-mapper-bundle](https://github.com/opportus/ObjectMapperBundle)
is one system integrating this library (into Symfony 4 application context).
You can refer to it for concrete examples of how to implement
*map definition preloading*.

### Check point

A *check point*, added to a *route*, allows you to control (or transform) the
value from the *source point* before it reaches the *target point*.

You can add multiple *check points* to a *route*. In this case, these
*check points* form a chain. The first *check point* controls the original value
from the *source point* and returns the value (transformed or not) to the
*object mapper*. Then, the *object mapper* passes the value to the next
*check point* and so on... Until the last *check point* returns the final value
to be assigned to the *target point* by the *object mapper*.

So it is important to keep in mind that each *check point* has a unique
position (priority) on a *route*. The routed value goes through each of the
*check points* from the lowest to the highest positioned ones such as
represented below:

```
SourcePoint --> $value' --> CheckPoint1 --> $value'' --> CheckPoint2 --> $value''' --> TargetPoint
```

An example of how to use *check points*:

```php
class Contributor
{
    private $bio;

    public function __construct(string $bio)
    {
        $this->bio = $bio;
    }

    public function getBio(): string
    {
        return $this->bio;
    }
}

class ContributorView
{
    private $bio;

    public function __construct(string $bio)
    {
        $this->bio = $bio;
    }

    public function getBio(): string
    {
        return $this->bio;
    }
}

class ContributorViewHtmlTagStripper implements CheckPointInterface
{
    public function control($value, RouteInterface $route, MapInterface $map, SourceInterface $source, TargetInterface $target)
    {
        if (ContributorView::class === $route->getTargetPoint()->getTargetFqn() && \is_string($value)) {
            return \strip_tags($value);
        }

        return $value;
    }
}

class ContributorViewMarkdownTransformer implements CheckPointInterface
{
    // ...

    public function control($value, RouteInterface $route, MapInterface $map, SourceInterface $source, TargetInterface $target)
    {
        if (ContributorView::class === $route->getTargetPoint()->getTargetFqn() && \is_string($value)) {
            return $this->markdownParser->transform($value);
        }

        return $value;
    }
}

$contributor = new Contributor('<script>**Hello World!**</script>');

$map = $mapBuilder
    ->getRouteBuilder()
        ->setStaticSourcePoint('Contributor.getBio()')
        ->setStaticTargetPoint('ContributorView.__construct().$bio')
        ->addCheckPoint(new ContributorViewHtmlTagStripper, 10)
        ->addCheckPoint(new ContributorViewMarkdownTransformer, 20)
        ->addRouteToMapBuilder()
        ->getMapBuilder()
    ->getMap();
;

$objectMapper->map($contributor, ContributorView::class, $map);

echo $contributorView->getBio(); // <b>Hello World!</b>
```

Below is described the unique method of the [`CheckPointInterface`](https://github.com/opportus/object-mapper/blob/master/src/Point/CheckPointInterface.php):

```php
CheckPointInterface::control($subject, RouteInterface $route, MapInterface $map, SourceInterface $source, TargetInterface $target);
```

**Parameters**

`$subject` is the value that `CheckPointInterface` implementation is meant to
control.

`$route` is the instance of [`RouteInterface`](https://github.com/opportus/object-mapper/blob/master/src/Route/RouteInterface.php)
which the `ObjectMapper` is currently on, containing the *source point* which
the `$subject` comes from, the *target point* which the `$subject` goes to, and the
[`CheckPointCollection`](https://github.com/opportus/object-mapper/blob/master/src/Point/CheckPointCollection.php)
which contain your current `CheckPointInterface` instance.

`$map` is an instance of [`MapInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapInterface.php)
which the *route* is on.

`$source` An instance of [`SourceInterface`](https://github.com/opportus/object-mapper/blob/master/src/SourceInterface.php)
which encapsulate and represent the *source* to map data from.

`$target` An instance of [`TargetInterface`](https://github.com/opportus/object-mapper/blob/master/src/TargetInterface.php)
which encapsulate and represent and wraps the *target* to map data to.

**Returns**

A `mixed` value to get assigned to the *target point*.

### Recursion

Although a *recursion* dedicated feature may come later, you can implement [`CheckPointInterface`](https://github.com/opportus/object-mapper/blob/master/src/Point/CheckPointInterface.php)
such as [introduced previously](#check-point) to recursively map a
*source point* to a *target point*. For example:

-   If you map an instance of `A` (that *has* `C`) to `B` (that *has* `D`) and
    that you want in the same time to map `C` to `D`, AKA *simple recursion*.
-   If you map an instance of `A` (that *has many* `C`) to `B`
    (that *has many* `D`) and that you want in the same time to map many `C` to
    many `D`, AKA *in-width recursion* or *iterable recursion*.
-   If you map an instance of `A` (that *has* `C` which *has* `E`) to `B`
    (that *has* `D` which *has* `F`) and that you want in the same time to map
    `C` and `E` to `D` and `F`, AKA *in-depth recursion*.
