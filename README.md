[![License](https://poser.pugx.org/opportus/object-mapper/license)](https://packagist.org/packages/opportus/object-mapper)
[![Latest Stable Version](https://poser.pugx.org/opportus/object-mapper/v/stable)](https://packagist.org/packages/opportus/object-mapper)
[![Latest Unstable Version](https://poser.pugx.org/opportus/object-mapper/v/unstable)](https://packagist.org/packages/opportus/object-mapper)
[![Build Status](https://travis-ci.com/opportus/object-mapper.svg?branch=master)](https://travis-ci.com/opportus/object-mapper)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d3f5178323844f59a6ef5647cb11d9d7)](https://www.codacy.com/manual/opportus/object-mapper?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=opportus/object-mapper&amp;utm_campaign=Badge_Grade)

## Index

-   [Use cases](#use-cases)
-   [Roadmap](#roadmap)
-   [Integrations](#integrations)
-   [Installation](#installation)
-   [Mapping](#mapping)
    -   [How it works](#how-it-works)
    -   [Automatic mapping](#automatic-mapping)
        - [Custom automatic mapping](#custom-automatic-mapping)
    -   [Manual mapping](#manual-mapping)
        - [Via map builder API](#via-map-builder-api)
        - [Via source and target classes annotations](#via-source-and-target-classes-annotations)
    -   [Check point](#check-point)
    -   [Recursion](#recursion)

## Use cases

Use this solution for mapping data of objects to differently typed objects using extensible strategies and controls.

Flexible, extensible, optimized, tested, this tool can be used in many use cases such as:

-   Mapping state of objects from/to DTOs.
-   Mapping state of domain model from/to persistance model.
-   Mapping state of domain model to view model.
-   Mapping state of objects to differently typed objects to get operated on, so that these operations don't mess with the state of the source objects.
-   As an *object mapping* subsystem to be integrated by systems such as frameworks, serializers, form handlers, ORMs, etc...
-   And so on...

## Roadmap

To develop this solution faster, [contributions](https://github.com/opportus/object-mapper/blob/master/.github/CONTRIBUTING.md) are welcome...

### v1.0.0 (stable)

-   Implement source and taget class mapping annotations loading system
-   Implement last unit tests

### v1.1.0

-   Implement *adders*, *removers*, *isers*, *hasers* support with the default PathFindingStrategy
-   ...

## Integrations

-   Symfony 4 => [oppotus/object-mapper-bundle](https://github.com/opportus/ObjectMapperBundle)
-   Reference here your own integrations

## Installation

### Step 1 - Download the package

Open a command console, enter your project directory and execute:

```console
$ composer require opportus/object-mapper
```

### Step 2 - Instantiate the services

This library contains 4 services. 3 of them require a single dependency which is another lower level service among those 4:

```php
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\RouteBuilder;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\ObjectMapper;

$pointFactory = new PointFactory();
$routeBuilder = new RouteBuilder($pointFactory);
$mapBuilder   = new MapBuilder($routeBuilder);
$objectMapper = new ObjectMapper($mapBuilder);
```

In order to make full use of this library, each of its services must be instantiated and *usable* such as above.

By design, this solution does not implement "helpers" for the instantiation of its services which is better handled directly the way you're already instantiating your own services, with a DIC system or whatever.

The rational behind this design is that:

In any use case context, there is already a dedicated solution for handling **properly** instantiation of services, because this is not a problem that an *object mapper* is meant to solve... Such dedicated solutions handle properly instantiation of services because they implement *Dependency Injection*, *Inversion of Control*, *lazy loading*, and many other features. So that delegating the instantiation of its services to such solution, the *object mapper* becomes more simple, flexible, extensible, performant, and integrable into its use case context...

## Mapping

Mapping object to object is done via the main [`ObjectMapper`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapper.php) service's method described below:

```php
ObjectMapperInterface::map(object $source, $target, ?Map $map = null): ?object
```

**Parameters**

`$source` must be an `object` to map data from.

`$target` must be an `object` (or a `string` being the Fully Qualified Name of a class to istantiate and) to map data to.

`$map` must be a `null` or an instance of [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php). If it is `null`, the method builds and uses a map composed of the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php).

**Returns**

Either:

-   `null` if the map has no route connecting source points with target points.
-   `object` which is the (instantiated and) updated target.

### How it works

The `ObjectMapper` method presented above iterates through each `Route` that it gets from the `Map`. Doing so, the method assigns the value of the current `Route`'s *source point* to its *target point*. Optionally, on the route, *check points* can be defined in order to control the value from the *source point* before it reaches the *target point*.

A [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) is defined by and composed of its *source point*, its *target point*, and its *check points*.

A *source point* can be either:

-   A [`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php).
-   A [`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php).

A *target point* can be either:

-   A [`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php).
-   A [`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php).

A *check point* can be any instance of [`CheckPointInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/CheckPointInterface.php).

These routes can be defined [automatically](#automatic-mapping) via the `Map`'s [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php) and/or [manually](#manual-mapping) [via the map builder's API](#via-map-builder-api) and [source and target classes annotations](#via-source-and-target-classes-annotations).

### Automatic mapping

A basic example of how to automatically map `User`'s data to `UserDto` and vice-versa:

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

// Map the data of the `User` instance to the `UserDto` instance
$objectMapper->map($user, $userDto);

echo $userDto->username; // 'Toto'

// Map the data of the `UserDto` instance to a new `User` instance
$user = $objectMapper->map($userDto, User::class);

echo $user->getUsername(); // 'Toto'
```

Calling the `ObjectMapper::map(object $source, $target, ?Map $map = null): ?object` method presented earlier, with its `$map` parameter set on `null`, makes the method build then use a `Map` composed of the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php).

The default `PathFindingStrategy` behavior consists of guessing what is the appropriate point of the source class to connect to each point of the target class. The connected *source point* and *target point* compose then a `Route` which is followed by the `ObjectMapper::map(object $source, $target, ?Map $map = null): ?object` method.

For the default `PathFindingStrategy`, a *target point* can be:

-   A public property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php))
-   A parameter of a public method ([`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php))

The corresponding *source point* can be:

-   A public property having for name the same as the target point ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php))
-   A public getter having for name `'get'.ucfirst($targetPointName)` and requiring no argument ([`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php))

#### Custom automatic mapping

The default `PathFindingStrategy` presented above is based on one particular *convention* that the *source* and the *target* have to comply with in order for this strategy to automatically map those for you. However, you may want to automatically map *source* and *target* not complying with this particular *convention*...

One solution is to implement [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php) defining another *convention* which your *source* and *target* can comply with in order for this custom strategy to "automatically" map those for you the way you need.

For concrete example of how to implement a `PathFindingStrategyInterface`, refer to the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php).

Below is described the single method of the `PathFindingStrategyInterface`:

```php
PathFindingStrategyInterface::getRoutes(Context $context): RouteCollection
```

**Parameters**

`$context` An instance of [`Context`](https://github.com/opportus/object-mapper/blob/master/src/Context.php) which contain the arguments passed to the `ObjectMapper::map(object $source, $target, ?Map $map = null): ?object` method and offer helper methods manipulating these arguments.

**Returns**

[`RouteCollection`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/RouteCollection.php) connecting the *source points* with the *taget points*.

**Example**

```php
class MyPathFindingStrategy implements PathFindingStrategyInterface
{
    public function getRoutes(Context $context): RouteCollection
    {
        $context->getSource();
        $context->getTarget();
        $context->getSourceClassReflection();
        $context->getTargetClassReflection();

        // Custom mapping algorithm

        return $routeCollection;
    }
}

// Pass to the map builder the strategy you want it to compose the map of
$map = $mapBuilder->buildMap(new MyPathFindingStrategy());

echo $map->getPathFindingStrategyFqn(); // 'MyPathFindingStrategy'

// Use the map
$user = $objectMapper->map($userDto, User::class, $map);
```

### Manual mapping

If in your context, such as walked through in the previous "[custom automatic mapping](#custom-automatic-mapping)" chapter, a custom mapping strategy definition does not scale well, or is either impossible or overkill, you can manually map the *source* to the *target*.

There are multiple ways to define manually the mapping such as introduced in the 2 next subchapters:

-   [Via the map builder API](#via-map-builder-api).
-   [Via source and target classes annotations](#via-source-and-target-classes-annotations) (incoming feature).

#### Via map builder API

The [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) is an immutable service which implement a fluent interface.

A basic example of how to manually map `User`'s data to `ContributorDto` and vice-versa with the `MapBuilder`:

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

$user           = new User('Toto');
$contributorDto = new ContributorDto();

// Define *on the go* the route
$map = $mapBuilder
    ->addRoute('User.getUsername()', 'ContributorDto.$name')
    ->buildMap()
;

// Map the data of the `User` instance to the `ContributorDto` instance
$objectMapper->map($user, $contributorDto, $map);

echo $contributorDto->name; // 'Toto'

// Define *on the go* the route
$map = $mapBuilder
    ->addRoute('ContributorDto.$name', 'User.__construct().$username')
    ->buildMap()
;

// Map the data of the `ContributorDto` instance to a new `User` instance
$user = $objectMapper->map($contributorDto, User::class, $map);

echo $user->getUsername(); // 'Toto'
```

Such as in the example above, you can add routes to a map with the method described below:

```php
MapBuilderInterface::addRoute(string $sourcePointFqn, string $targetPointFqn): MapBuilderInterface
```

**Parameters**

`$sourcePointFqn` must be a `string` representing the Fully Qualified Name of a *source point* which can be:

-   A public, protected or private property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)) represented by its FQN having for syntax `'My\Class.$property'`.
-   A public, protected or private method requiring no argument ([`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php)) represented by its FQN having for syntax `'My\Class.method()'`.

`$targetPointFqn` must be a `string` representing the Fully Qualified Name of a *target point* which can be:

-   A public, protected or private property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)) represented by its FQN having for syntax `'My\Class.$property'`.
-   A parameter of a public, protected or private method ([`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php)) represented by its FQN having for syntax `'My\Class.method().$parameter'`.

`$checkPoints` must be an instance of [`CheckPointCollection`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/CheckPointCollection.php). See the [check point](#check-point) chapter.

**Returns**

A **new** instance of [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php).

---

Once routes are defined with the method described above, build the `Map` with the method described below:

```php
MapBuilderInterface::buildMap($pathFindingStrategy = false): Map
```

**Parameters**

`$pathFindingStrategy` must be either a `boolean` or an instance of [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php).

-   If it is `false`, a `Map` composed of the [`NoPathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/NoPathFindingStrategy.php) will be built.
-   If it is `true`, a `Map` with the [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php) is built.
-   If it is an instance of [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php), a `Map` composed of this instance is built.

**Returns**

An instance of [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php).

#### Via source and target classes annotations

Incoming feature...

### Check point

A *check point*, added to a route, allows you to control (or transform) the value from the *source point* before it reaches the *target point*.

You can add multiple *check points* to a route. In this case, these *check points* form a chain. The first *check point* controls the original value from the *source point* and returns the value (transformed or not) to the object mapper. Then, the object mapper passes the value to the next checkpoint and so on... Until the last checkpoint returns the final value to be assigned to the *target point* by the object mapper.

So it is important to keep in mind that each *check point* has an unique position (priority) on a route. The routed value goes through each of the *check points* from the lowest to the highest positioned ones such as shematized below:

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
    public function control($value, Route $route, Context $context, ObjectMapperInterface $objectMapper)
    {
        if (ContributorView::class === $route->getTargetPoint()->getClassFqn() &&
            \is_string($value)
        ) {
            return \strip_tags($value);
        }

        return $value;
    }
}

class ContributorViewMarkdownTransformer implements CheckPointInterface
{
    // ...

    public function control($value, Route $route, Context $context, ObjectMapperInterface $objectMapper)
    {
        if (ContributorView::class === $route->getTargetPoint()->getClassFqn() &&
            \is_string($value)
        ) {
            return $this->markdownParser->transform($value);
        }

        return $value;
    }
}

$contributor = new Contributor('<script>**Hello World!**</script>', true);

$checkPoints = new CheckPointCollection([
    10 => new ContributorViewHtmlTagStripper(),     // Index 10 represents the position of this checkpoint on the route
    20 => new ContributorViewMarkdownTransformer(), // Index 20 represents the position of this checkpoint on the route
]);

$map = $mapBuilder
    ->addRoute('Contributor.getBio()', 'ContributorView.__construct().$bio', $checkPoints)
    ->buildMap()
;

$objectMapper->map($contributor, ContributorView::class, $map);

echo $contributorView->bio; // <b>Hello World!</b>'
```

### Recursion

Although a recursion dedicated feature may come later, you can use a *check point* such as [introduced previously](#check-point) to recursively map a *source point* to a *target point*. For example:

-   If you map an instance of `A` (that *has* `C`) to `B` (that *has* `D`) and that you want in the same time to map `C` to `D`, AKA *simple recursion*.
-   If you map an instance of `A` (that *has many* `C`) to `B` (that *has many* `D`) and that you want in the same time to map many `C` to many `D`, AKA *in-width recursion* or *iterable recursion*.
-   If you map an instance of `A` (that *has* `C` which *has* `E`) to `B` (that *has* `D` which *has* `F`) and that you want in the same time to map `C` and `E` to `D` and `F`, AKA *in-depth recursion*.
