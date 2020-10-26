# Object Mapper

[![License](https://poser.pugx.org/opportus/object-mapper/license)](https://packagist.org/packages/opportus/object-mapper)
[![Latest Stable Version](https://poser.pugx.org/opportus/object-mapper/v/stable)](https://packagist.org/packages/opportus/object-mapper)
[![Latest Unstable Version](https://poser.pugx.org/opportus/object-mapper/v/unstable)](https://packagist.org/packages/opportus/object-mapper)
[![Build](https://github.com/opportus/object-mapper/workflows/Build/badge.svg)](https://github.com/opportus/object-mapper/actions?query=workflow%3ABuild)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d3f5178323844f59a6ef5647cb11d9d7)](https://www.codacy.com/manual/opportus/object-mapper?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=opportus/object-mapper&amp;utm_campaign=Badge_Grade)

**Index**

- [Meta](#meta)
- [Use Cases](#use-cases)
- [Roadmap](#roadmap)
  - [v1.0.0 (stable)](#v100-stable)
  - [v1.1.0](#v110)
- [Integrations](#integrations)
- [Setup](#setup)
  - [Step 1 - Installation](#step-1---installation)
  - [Step 2 - Initialization](#step-2---initialization)
- [Mapping](#mapping)
  - [How It Works](#how-it-works)
  - [Automatic Mapping](#automatic-mapping)
    - [Static Path Finder](#static-path-finder)
    - [Static Source To Dynamic Target Path Finder](#static-source-to-dynamic-target-path-finder)
    - [Dynamic Source To Static Target Path Finder](#dynamic-source-to-static-target-path-finder)
    - [Custom Path Finder](#custom-path-finder)
  - [Manual Mapping](#manual-mapping)
    - [Via Map Builder API](#via-map-builder-api)
    - [Via Map Definition Preloading](#via-map-definition-preloading)
  - [Check Point](#check-point)
  - [Recursion](#recursion)

## Meta

This document is a guide mainly walking you through the setup, concepts, and use
cases of this solution.

API documentation is bound to code and complies to PHPDoc standards...

Sections covering optional features for most use cases (yet important to be
aware of) are collapsed in order to keep this document as easy as possible to
read by new users.

## Use Cases

Use this solution for mapping source to target object via extensible
strategies and controls.

Leverage this solution to:
 
-   Decouple your codebase from source and target mapping logic
-   Dynamically define control flow over data being transferred from source to
    target
-   Dynamically define target model depending on source model and reciprocally

This project aims to provide a standard core system to higher level systems
such as:

-   Data transformation
-   ORM
-   Form handling
-   Serialization
-   Interlayer data mapping
-   ...

## Roadmap

To develop this solution faster, [contributions](https://github.com/opportus/object-mapper/blob/master/.github/CONTRIBUTING.md) are welcome...

### v1.0.0 (stable)

-   Implement last unit tests to reach 100% coverage
-   Update supported version of PHP and other dependencies
-   Improve doc

### v1.1.0

-   Implement recursion path finder feature
-   Implement callable check point feature

## Integrations

-   Symfony 4 application => [opportus/object-mapper-bundle](https://github.com/opportus/ObjectMapperBundle)
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

### How It Works

In order to transfer data from a *source* object to a *target* object, the
[`ObjectMapper`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapper.php)
iterates through each
[`Route`](https://github.com/opportus/object-mapper/blob/master/src/Route/Route.php)
that it gets from a
[`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php),
assigning the value of the current *route*'s *source point* to this *route*'s
*target point*.

Optionally, on the *route*, *check points* can be defined in
order to control the value from the *source point* before it reaches the
*target point*.

A *route* is defined by and composed of its *source point*, its *target point*,
and its *check points*.

A *source point* can be either:

-   A static/dynamic property
-   A static/dynamic method
-   Any extended type of static/dynamic source point

A *target point* can be either:

-   A static/dynamic property
-   A static/dynamic method parameter
-   Any extended type of static/dynamic target point

A *check point* can be any implementation of
[`CheckPointInterface`](https://github.com/opportus/object-mapper/blob/master/src/Point/CheckPointInterface.php).

These *routes* can be defined [automatically](#automatic-mapping) via a *map*'s
[`PathFinderInterface`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/PathFinderInterface.php)
strategy implementation and/or [manually](#manual-mapping) via:

-   [Map builder API](#via-map-builder-api)
-   [Map definition preloading](#via-map-definition-preloading)

### Automatic Mapping

Remember that `PathFinderInterface` implementations such as those covered next
in this section can get combined.

#### Static Path Finder

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

Calling the `ObjectMapper::map()` method passing no `$map` argument makes
the method build then use a `Map` composed of the default `StaticPathFinder`
strategy.

The default `StaticPathFinder` strategy determines the appropriate point of the
*source* class to connect to each point of the *target* class.

For the default [`StaticPathFinder`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/StaticPathFinder.php),
a reference *target point* can be:

-   A public property ([`PropertyStaticTargetPoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/PropertyStaticTargetPoint.php))
-   A parameter of a public setter or constructor ([`MethodParameterStaticTargetPoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/MethodParameterStaticTargetPoint.php))

The corresponding *source point* can be:

-   A public property having for name the same as the *target point* ([`PropertyStaticSourcePoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/PropertyStaticSourcePoint.php))
-   A public getter having for name `'get'.ucfirst($targetPointName)` and
    requiring no argument ([`MethodStaticSourcePoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/MethodStaticSourcePoint.php))

#### Static Source To Dynamic Target Path Finder

<details>

<summary>Click for details</summary>


A basic example of how to automatically map `User`'s data to `DynamicUserDto`:

```php
class DynamicUserDto {}

$user    = new User('Toto');
$userDto = new DynamicUserDto();

// Build the map
$map = $mapBuilder
    ->addStaticSourceToDynamicTargetPathFinder()
    ->getMap();

// Map the data of the User instance to the DynamicUserDto instance
$objectMapper->map($user, $userDto, $map);

echo $userDto->username; // Toto
```

The default `StaticSourceToDynamicTargetPathFinder` strategy determines the
appropriate point of the *target* **object** (*dynamic point*) to connect to
each point of the *source* **class** (*static point*).

For the default [`StaticSourceToDynamicTargetPathFinder`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/StaticSourceToDynamicTargetPathFinder.php),
a reference *source point* can be:

-   A public property ([`PropertyStaticSourcePoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/PropertyStaticSourcePoint.php))
-   A public getter ([`MethodStaticSourcePoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/MethodStaticSourcePoint.php))

The corresponding *target point* can be:

-   A statically non-existing property having for name the same as the property
    *source point* or `lcfirst(str_replace('get', '', $getterSourcePoint))` ([`PropertyDynamicTargetPoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/PropertyDynamicTargetPoint.php))
-   A parameter of a statically non-existing setter having for name `'set'.ucfirst($propertySourcePoint)`
    `str_replace('get', 'set', $getterSourcePoint)` ([`MethodParameterDynamicTargetPoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/MethodParameterDynamicTargetPoint.php))

</details>

#### Dynamic Source To Static Target Path Finder

<details>

<summary>Click for details</summary>


A basic example of how to automatically map `DynamicUserDto`'s data to `User`:

```php
$userDto = new DynamicUserDto();
$userDto->username = 'Toto';

// Build the map
$map = $mapBuilder
    ->addDynamicSourceToStaticTargetPathFinder()
    ->getMap();

// Map the data of the DynamicUserDto instance to a new User instance
$user = $objectMapper->map($userDto, User::class, $map);

echo $user->getUsername(); // Toto
```

The default `DynamicSourceToStaticTargetPathFinder` strategy determines the
appropriate point of the *source* **object** (*dynamic point*) to connect to
each point of the *target* **class** (*static point*). 

For the default [`StaticSourceToDynamicTargetPathFinder`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/DynamicSourceToStaticTargetPathFinder.php),
a reference *target point* can be:

-   A public property ([`PropertyStaticTargetPoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/PropertyStaticTargetPoint.php))
-   A parameter of a public setter or constructor ([`MethodParameterStaticTargetPoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/MethodParameterStaticTargetPoint.php))

The corresponding *source point* can be:

-   A statically non-existing property having for name the same as the
    *target point* ([`PropertyDynamicSourcePoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/PropertyDynamicSourcePoint.php))
-   A statically non-existing getter having for name
    `'get'.ucfirst($targetPointName)` ([`MethodDynamicSourcePoint`](https://github.com/opportus/object-mapper/blob/master/src/Point/MethodDynamicSourcePoint.php))

</details>

#### Custom Path Finder

The default *path finders* presented above implement each a specific mapping
logic. In order for those to generically map differently typed objects, they
have to follow a certain convention de facto established by these
*path finders*. You can map generically differently typed objects only
accordingly to the *path finders* the *map* is composed of.

If the default *path finders* do not suit your needs, you still
can genericize and encapsulate your domain's mapping logic as subtype(s) of
`PathFinderInterface`. Doing so effectively, you leverage *Object Mapper* to
decouple these objects from your mapping logic... Indeed, when the mapped
objects change, the mapping doesn't.

Later, we'll cover how to leverage further the *Object Mapper* by encapsulating
into a *path finder* dynamic control flow (*check points*) over data being
mapped...

For concrete example of how to implement [`PathFinderInterface`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/PathFinderInterface.php), refer to the default [`StaticPathFinder`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/StaticPathFinder.php), [`StaticSourceToDynamicTargetPathFinder`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/StaticSourceToDynamicTargetPathFinder.php), and
[`DynamicSourceToStaticTargetPathFinder`](https://github.com/opportus/object-mapper/blob/master/src/PathFinder/DynamicSourceToStaticTargetPathFinder.php)
implementations.

**Example**

```php
class MyPathFinder implements PathFinderInterface
{
    public function getRoutes(SourceInterface $source, TargetInterface $target): RouteCollection
    {
        $source->getClassReflection();
        $target->getClassReflection();
        
        $routes = new RouteCollection();

        // Custom mapping algorithm

        return $routes;
    }
}

// Pass to the map builder pathfinders you want it to compose the map of
$map = $mapBuilder
    ->addStaticPathFinder()
    ->addStaticSourceToDynamicTargetPathFinder()
    ->addPathFinder(new MyPathFinder())
    ->getMap();

// Use the map
$user = $objectMapper->map($userDto, User::class, $map);
```

### Manual Mapping

If in your context, such as walked through in the previous
"[automatic mapping](#automatic-mapping)" section, a mapping strategy does not
scale well, or is either impossible or overkill, you can manually map the
*source* to the *target*.

There are multiple ways to define manually the mapping such as introduced in the
next sub-sections:

-   [Via map builder API](#via-map-builder-api)
-   [Via map definition preloading](#via-map-definition-preloading)

#### Via Map Builder API

<details>

<summary>Click for details</summary>


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
        ->setStaticSourcePoint('User::getUsername()')
        ->setStaticTargetPoint('ContributorDto::$name')
        ->addRouteToMapBuilder()
        ->getMapBuilder()
    ->getMap();

// Map the data of the User instance to the ContributorDto instance
$objectMapper->map($user, $contributorDto, $map);

echo $contributorDto->name; // Toto

// Define the route manually
$map = $mapBuilder
    ->getRouteBuilder()
        ->setStaticSourcePoint('ContributorDto::$name')
        ->setStaticTargetPoint('User::__construct()::$username')
        ->addRouteToMapBuilder()
        ->getMapBuilder()
    ->getMap();

// Map the data of the ContributorDto instance to a new User instance
$user = $objectMapper->map($contributorDto, User::class, $map);

echo $user->getUsername(); // 'Toto'
```

</details>

#### Via Map Definition Preloading

<details>

<summary>Click for details</summary>


[Via the map builder API](#via-map-builder-api) presented above, we define the
*map* (adding to it *routes*) *on the go*. There is another way to define the
*map*, *preloading* its definition.

While this library is designed with *map definition preloading* in mind, it
does not provide a way to effectively *preload a map definition* which could be:

-   Any type of file, commonly used for configuration (XML, YAML, JSON, etc...),
    defining statically a *map* to build at runtime
-   Any type ofH annotation in *source* and *target* classes, defining statically
    a *map* to build at runtime
-   Any type of PHP routine, defining dynamically a *map* to build at runtime
-   ...

A *map* being not much more than a collection of *routes*, you can statically
define it for example by defining its *routes* FQN this way:

```yaml
map:
  - source1::$property=>target1::$property
  - source1::$property=>target2::$property
  - source2::$property=>target2::$property
```

Then at runtime, in order to create *routes* to compose a *map* of, you can:

-   Parse your map configuration files, extract from them *route* definitions
-   Parse your *source* and *target* annotations, extract from them *route*
    definitions
-   Implement any sort of map generator logic outputing *route* definitions

Then, based on their definitions, build these *routes* with the initial instance
of the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilderInterface.php)
which will keep and inject them into its built *maps* which in turn might return
these *routes* to the *object mapper* depending on the source and target being
mapped.

Because an *object mapper* has a wide range of different use case contexts, this
solution is designed as a minimalist, flexible, and extensible core in order to
get integrated, adapted, and extended seamlessly into any of these contexts.
Therefore, this solution delegates *map definition preloading* to the
integrating higher level system which can make use contextually of its own DIC,
configuration, and cache systems required for achieving
*map definition preloading*.

[opportus/object-mapper-bundle](https://github.com/opportus/ObjectMapperBundle)
is one system integrating this library (into Symfony 4 application context).
You can refer to it for concrete examples of how to implement
*map definition preloading*.

</details>

### Check Point

A *check point*, added to a *route*, allows you to control/transform the value
from the *source point* before it reaches the *target point*.

You can add multiple *check points* to a *route*. In this case, these
*check points* form a chain. The first *check point* controls the original value
from the *source point* and returns the value (transformed or not) to the
*object mapper*. Then, the *object mapper* passes the value to the next
*check point* and so on... Until the last *check point* returns the final value
to be assigned to the *target point* by the *object mapper*.

So it is important to keep in mind that each *check point* has a unique position
(priority) on a *route*. The routed value goes through each of the
*check points* from the lowest to the highest positioned ones such as
represented below:

```txt
SourcePoint --> $value' --> CheckPoint1 --> $value'' --> CheckPoint2 --> $value''' --> TargetPoint
```

A basic example of how to use *check points*, implementing [`CheckPointInterface`](https://github.com/opportus/object-mapper/blob/master/src/Point/CheckPointInterface.php) as a sort of presenter:

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
        return \strip_tags($value);
    }
}

class ContributorViewMarkdownTransformer implements CheckPointInterface
{
    // ...

    public function control($value, RouteInterface $route, MapInterface $map, SourceInterface $source, TargetInterface $target)
    {
        return $this->markdownParser->transform($value);
    }
}

$contributor = new Contributor('<script>**Hello World!**</script>');

$map = $mapBuilder
    ->getRouteBuilder()
        ->setStaticSourcePoint('Contributor::getBio()')
        ->setStaticTargetPoint('ContributorView::__construct()::$bio')
        ->addCheckPoint(new ContributorViewHtmlTagStripper, 10)
        ->addCheckPoint(new ContributorViewMarkdownTransformer($markdownParser), 20)
        ->addRouteToMapBuilder()
        ->getMapBuilder()
    ->getMap();
;

$objectMapper->map($contributor, ContributorView::class, $map);

echo $contributorView->getBio(); // <b>Hello World!</b>
```

### Recursion

A *recursion* implements [`CheckPointInterface`](https://github.com/opportus/object-mapper/blob/master/src/Point/CheckPointInterface.php).
It is used to recursively map a *source point* to a *target point*.
More concretely, it is used:

-   To map an instance of `A` (that *has* `C`) to `B` (that *has* `D`) and
    in same time map `C` to `D`, AKA *simple recursion*.
-   To map an instance of `A` (that *has many* `C`) to `B`
    (that *has many* `D`) and in same time map many `C` to many `D`, AKA
    *in-width recursion* or *iterable recursion*.
-   To map an instance of `A` (that *has* `C` which *has* `E`) to `B`
    (that *has* `D` which *has* `F`) and in same time map `C` and `E` to `D` and
    `F`, AKA *in-depth recursion*.

A basic example of how to manually map a `Post` and its composite objects to its
`PostDto` and its composite DTO objects:

```php
class Post
{
    public Author $author;
    public Comment[] $comments;
}

class Author
{
    public string $name;
}

class Comment
{
    public Author $author;
}

class PostDto {}
class AuthorDto {}
class CommentDto {}

$comment1 = new Comment();
$comment1->author = new Author();
$comment1->author->name = 'clem';

$comment2 = new Comment();
$comment2->author = new Author();
$comment2->author->name = 'bob';

$post = new Post();
$post->author = new Author();
$post->author->name = 'Martin Fowler';
$post->comments = [$comment1, $comment2];

// Let's map the Post instance above and its composites to a new PostDto instance and DTO composites...
$mapBuilder
    ->getRouteBuilder
        ->setStaticSourcePoint('Post::$author')
        ->setDynamicTargetPoint('PostDto::$author')
        ->addRecursionCheckPoint('Author', 'AuthorDto', 'PostDto::$author') // Mapping also Post's Author to PostDto's AuthorDto
        ->addRouteToMapBuilder()

        ->setStaticSourcePoint('Comment::$author')
        ->setDynamicTargetPoint('CommentDto::$author')
        ->addRecursionCheckPoint('Author', 'AuthorDto', 'CommentDto::$author') // Mapping also Comment's Author to CommentDto's AuthorDto
        ->addRouteToMapBuilder()

        ->setStaticSourcePoint('Post::$comments')
        ->setDynamicTargetPoint('PostDto::$comments')
        ->addIterableRecursionCheckPoint('Comment', 'CommentDto', 'PostDto::$comments') // Mapping also Post's Comment's to PostDto's CommentDto's
        ->addRouteToMapBuilder()
    ->getMapBuilder()
    ->addStaticSourceToDynamicTargetPathFinder()
    ->getMap();

$postDto = $objectMapper->($post, PostDto::class, $map)

get_class($postDto); // PostDto

get_class($postDto->author); // AuthorDto
echo $postDto->author->name; // Matin Fowler

get_class($postDto->comments[0]); // CommentDto
get_class($postDto->comments[0]->author); // AuthorDto
echo $postDto->comments[0]->author->name; // clem

get_class($postDto->comments[1]); // CommentDto
get_class($postDto->comments[1]->author); // AuthorDto
echo $postDto->comments[1]->author->name; // bob
```

Naturally, all that can get simplified with higher level `PathFinderInterface`
implementation defining these recursions automatically based on source and
target point types. These types being hinted in source and target classes
either with PHP or PHPDoc.

This library may feature such `PathFinder` in near future.