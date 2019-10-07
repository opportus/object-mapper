[![License](https://poser.pugx.org/opportus/object-mapper/license)](https://packagist.org/packages/opportus/object-mapper)
[![Latest Stable Version](https://poser.pugx.org/opportus/object-mapper/v/stable)](https://packagist.org/packages/opportus/object-mapper)
[![Latest Unstable Version](https://poser.pugx.org/opportus/object-mapper/v/unstable)](https://packagist.org/packages/opportus/object-mapper)
[![Build Status](https://travis-ci.com/opportus/object-mapper.svg?branch=master)](https://travis-ci.com/opportus/object-mapper)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d3f5178323844f59a6ef5647cb11d9d7)](https://www.codacy.com/manual/opportus/object-mapper?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=opportus/object-mapper&amp;utm_campaign=Badge_Grade)

## Index

- [Use cases](#use-cases)
- [Roadmap](#roadmap)
- [Integrations](#integrations)
- [Installation](#installation)
- [Mapping](#mapping)
  - [How it works](#how-it-works)
  - [Automatic mapping (dynamic mapping)](#automatic-mapping-aka-dynamic-mapping)
  - [Manual mapping (static mapping)](#manual-mapping-aka-static-mapping)
  - [Filtering](#filtering)
  - [Recursion](#recursion)
- [Mapping preloading](#mapping-preloading)

## Use cases

Use this solution for copying state of objects to differently typed objects using extensible controls and mapping strategies.

Simple, flexible, extensible, optimized, this tool can be used in many use cases such as:

- Mapping state of objects from/to DTOs.
- Mapping state of domain model from/to persistance model.
- Mapping state of domain model to view model.
- Mapping state of objects to differently typed objects to get operated on, so that these operations don't mess with the state of the source objects.
- As an *object mapping* subsystem to be integrated by systems such as frameworks, serializers, form handlers, ORMs, DICs, etc...
- And so on...

## Roadmap

To develop this solution faster, [contributions](https://github.com/opportus/object-mapper/blob/master/.github/CONTRIBUTING.md) are welcome...

### v1.0.0 (stable)

- Implement last unit tests

### v1.1.0

- Implement *adders*, *removers*, *isers*, *hasers* support with the default PathFindingStrategy
- ...

## Integrations

- Symfony 4 => [oppotus/object-mapper-bundle](https://github.com/opportus/ObjectMapperBundle)
- Reference here your own integrations

## Installation

### Step 1 - Download the package

Open a command console, enter your project directory and execute:

```console
$ composer require opportus/object-mapper
```

### Step 2 - Instantiate the services

By design, this solution does not implement "helpers" for the instantiation of its services which is better handled directly the way you're already instantiating your own services, with a DIC system or whatever.

The rational behind this design is that:

In any use case context, there is already a dedicated solution for handling properly instantiation of services, because this is not a problem that an *object mapper* is meant to solve... Such dedicated solutions handle properly instantiation of services because they implement *Dependency Injection*, *Inversion of Control*, *lazy loading*, and many other features. So that delegating the instantiation of its services to such solution, the *object mapper* becomes more simple, flexible, extensible, performant, and integrable into its use case context...

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

In order to make full use of this library, each of its services must be instantiated and usable such as above.

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

- `null` if the map has no route connecting source points with target points.
- `object` which is the (instantiated and) updated target.

### How it works

The [`ObjectMapper`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapper.php) method presented above iterates through each [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) that it gets from the [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php). Doing so, the method assigns the value of the current [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php)'s *source point* to its *target point*, optionally applying your [`FilterInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Filter/FilterInterface.php) instance during this value assignment.

A [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) is defined by and composed of its *source point* and its *target point*.

A *source point* can be either:

- A [`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)
- A [`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php)

A *target point* can be either:

- A [`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)
- A [`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php)

Routes can be defined either [automatically](#automatic-mapping-aka-dynamic-mapping) (default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php)) or [manually](#manual-mapping-aka-static-mapping) ([`NoPathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/NoPathFindingStrategy.php)). In the former case, routes are defined dynamically while in the later case, routes are defined statically.

### Automatic mapping AKA dynamic mapping

A basic example of how to automatically map `User`'s state to `UserDto` and vice-versa:

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

The *automatic mapping* allows seamless mapping of source object's state to the target object.

Calling the `ObjectMapper::map(object $source, $target, ?Map $map = null): ?object` method presented earlier, with its `$map` parameter set on `null`, makes the method build then use a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) composed of the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php).

The default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php) behavior consists of guessing what is the appropriate point of the source class to connect to each point of the target class. The connected *source point* and *target point* compose then a [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) which is followed by the `ObjectMapper::map(object $source, $target, ?Map $map = null): ?object` method.

For the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php), a *target point* can be:

- A public property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php))
- A parameter of a public method ([`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php))

The corresponding *source point* can be:

- A public property having for name the same as the target point ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php))
- A public getter having for name `'get'.ucfirst($targetPointName)` and requiring no argument ([`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php))

#### Custom automatic mapping

The default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php) presented above is based on one particular *convention* that the *source* and the *target* have to comply with in order for this strategy to automatically map those for you. However, you may want to automatically map *source* and *target* not complying with this particular *convention*...

One solution is to implement a custom [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php) based on another *convention* which your *source* and *target* can comply with in order for this custom strategy to "automatically" map those for you.

For concrete example of how to implement a [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php), refer to the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php).

Below is described the single method of the [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php):

```php
PathFindingStrategyInterface::getRoutes(Context $context): RouteCollection
```

**Parameters**

`$context` An instance of [`Context`](https://github.com/opportus/object-mapper/blob/master/src/Context.php) which contain the arguments passed to the `ObjectMapper::map(object $source, $target, ?Map $map = null): ?object` method and offer contextual helper methods manipulating these arguments.

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

        // Custom mapping logic

        return $routeCollection;
    }
}

// Pass to the map builder the strategy you want it to compose the map of
$map = $mapBuilder->buildMap(new MyPathFindingStrategy());

echo $map->getPathFindingStrategyFqn(); // 'MyPathFindingStrategy'

// Use the map
$user = $objectMapper->map($userDto, User::class, $map);
```

### Manual mapping AKA static mapping

If custom mapping strategy definition such as walked through in the previous "[custom automatic mapping](#custom-automatic-mapping)" chapter is impossible or unworthy, you can manually map the *source* to the *target*.

There are two ways of defining manually the mapping. One way is [pretoloading mapping definitions](#mapping-preloading) and the second way is defining routes *on the go* via the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) API.

The [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) is an immutable service which implement a fluent interface.

A basic example of how to manually map `User`'s state to `ContributorDto` and vice-versa with the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php):

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

- A public, protected or private property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)) represented by its FQN having for syntax `'My\Class.$property'`.
- A public, protected or private method requiring no argument ([`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php)) represented by its FQN having for syntax `'My\Class.method()'`.

`$targetPointFqn` must be a `string` representing the Fully Qualified Name of a *target point* which can be:

- A public, protected or private property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)) represented by its FQN having for syntax `'My\Class.$property'`.
- A parameter of a public, protected or private method ([`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php)) represented by its FQN having for syntax `'My\Class.method().$parameter'`.

**Returns**

A **new** instance of [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php).

---

Once routes are defined, build the [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) with the method described below:

```php
MapBuilderInterface::buildMap($pathFindingStrategy = false): Map
```

**Parameters**

`$pathFindingStrategy` must be either a `boolean` or an instance of [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php).

- If it is `false`, a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) composed of the [`NoPathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/NoPathFindingStrategy.php) will be built.
- If it is `true`, a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) with the [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php) is built. Note that in our context of *manual mapping*, `$pathFindingStrategy = true` does not make sense since the map would just ignore the routes you previously defined.
- If it is an instance of [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php), a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) composed of this instance is built.

**Returns**

An instance of [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php).

### Filtering

A *filter* allows filtering the *source point* value before it gets assigned to the *target point* by the mapper.

A basic example of how to use a *filter*:

```php
class User
{
    private $age;

    public function __construct(int $age)
    {
        $this->age = $age;
    }

    public function getAge(): int
    {
        return $this->age;
    }
}

class UserDto
{
    public $age;
}

$user    = new User(29);
$userDto = new UserDto();
```

#### Solution 1: implement a callable

```php
$map = $mapBuilder
    ->addFilterOnRoute(
        function ($route, $context, $objectMapper) {
            return $route->getSourcePoint()->getValue($context->getSource()) + 1;
        },
        'User.getAge()',
        'UserDto.$age'
    )
    ->buildMap($pathFindingStrategy = true)
;

$objectMapper->map($user, $userDto, $map); // The `$context` you get in the `Filter` above
echo $userDto->age; // '30'
```

Build a map adding a `callable` filter on a specific route with the method described below:

```php
MapBuilderInterface::addFilterOnRoute(callable $callable, string $sourcePointFqn, string $targetPointFqn): MapBuilderInterface
```

**Parameters**

`$callable` must be a `callable` containing the filtering logic. This `callable` returns a `mixed` value which will be assigned to the target point by the mapper. This `callable` takes as arguments:

- `$route` is an instance of [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) the filter is on.
- `$context` is an instance of [`Context`](https://github.com/opportus/object-mapper/blob/master/src/Context.php) which contain the arguments passed to the `ObjectMapper::map(object $source, $target, ?Map $map = null): ?object` method and offer contextual helper methods manipulating these arguments.
- `$objectMapper` is an instance of [`ObjectMapperInterface`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapperInterface.php) which is useful for recursion.

`$sourcePointFqn` must be a `string` representing the Fully Qualified Name of a *source point* which can be:

- A public, protected or private property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)) represented by its FQN having for syntax `'My\Class.$property'`.
- A public, protected or private method requiring no argument ([`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php)) represented by its FQN having for syntax `'My\Class.method()'`.

`$targetPointFqn` must be a `string` representing the Fully Qualified Name of a *target point* which can be:

- A public, protected or private property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)) represented by its FQN having for syntax `'My\Class.$property'`.
- A parameter of a public, protected or private method ([`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php)) represented by its FQN having for syntax `'My\Class.method().$parameter'`.

**Returns**

A **new** instance of [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php).

#### Solution 2: implement FilterInterface

```php
class Filter implements FilterInterface
{
    /** {@inheritdoc} */
    public function supportRoute(Route $route): bool
    {
        return $route->getFqn() === 'User.getAge():UserDto.$age';
    }

    /** {@inheritdoc} */
    public function getValue(Route $route, Context $context, ObjectMapperInterface $objectMapper)
    {
        return $route->getSourcePoint()->getValue($context->getSource()) + 1;
    }
}

$map = $mapBuilder
    ->addFilter(new Filter())
    ->buildMap($pathFindingStrategy = true)
;

$objectMapper->map($user, $userDto, $map); // The `$context` you get in the `Filter` above
echo $userDto->age; // '30'
```

Build a map adding to it a filter implemeting [`FilterInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Filter/FilterInterface.php) with the method described below:

```php
MapBuilderInterface::AddFilter(FilterInterface $filter): MapBuilderInterface
```

**Parameters**

`$filter` must be an instance of [`FilterInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Filter/FilterInterface.php).

**Returns**

A **new** instance of [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php).

---

Then, the `ObjectMapper::map(object $source, $target, ?Map $map =null): ?object` method gets the filtered value to assign to the *target point* by calling on your [`FilterInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Filter/FilterInterface.php) implementation instance the method described below:

```php
FilterInterface::getValue(Route $route, Context $context, ObjectMapperInterface $objectMapper)
```

**Parameters**

`$route` is an instance of the [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) the `ObjectMapper::map(object $source, $target, ?Map $map =null): ?object` method is currently working on.

`$context` is an instance of [`Context`](https://github.com/opportus/object-mapper/blob/master/src/Context.php) containing the arguments passed to the `ObjectMapper::map(object $source, $target, ?Map $map =null): ?object` method.

`$objectMapper` is the instance of [`ObjectMapperInterface`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapperInterface.php) which can be useful for [recursion](#recursion).

**Returns**

A `mixed` value.

---

Keep in mind that a *filter* *supports* instance(s) of [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php).

You have to define the supported routes by implementing the method described below:

```php
FilterInterface::supportRoute(Route $route): bool
```

**Parameters**

`$route` is an instance of the [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) the `ObjectMapper::map(object $source, $target, ?Map $map =null): ?object` method is currently working on.

**Returns**

A `bool` defining whether the passed [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) is supported by this *filter*.

### Recursion

Although a recursion dedicated feature may come later, you can use the *filtering* feature [introduced previously](#filtering) to recursively map a *source point* to a *target point*. For example:

- If you map an instance of `A` (that *has* `C`) to `B` (that *has* `D`) and that you want in the same time to map `C` to `D`, AKA *simple recursion*.
- If you map an instance of `A` (that *has many* `C`) to `B` (that *has many* `D`) and that you want in the same time to map many `C` to many `D`, AKA *in-width recursion* or *iterable recursion*.
- If you map an instance of `A` (that *has* `C` which *has* `E`) to `B` (that *has* `D` which *has* `F`) and that you want in the same time to map `C` and `E` to `D` and `F`, AKA *in-depth recursion*.

## Mapping preloading

In the code examples above, we *define the map* (adding to it routes and filters) *on the go*, via the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) API. There is another way to *define the map*, preloading its definition.

While this library is definitely designed with *mapping preloading* in mind, it does not *provide* a way to implement preloadable definitions and effectively preload them.

Indeed, this solution is designed as a core for higher level systems to integrate it as an *object mapper* subsystem. Therefore it does not ship any DIC and configuration system necessary for achieving *mapping preloading* which is better implemented into your specific context.

So this chapter is an attempt to help you to implement *mapping preloading*, making use of your own DIC and configuration systems in order for this solution to be integrated seamlessly as a subsystem into a wider system such as a framework, serializer, form handler, ORM, DIC or whatever.

For instance, the Symfony [ObjectMapperBundle](https://github.com/opportus/ObjectMapperBundle) does implement to some extend such *mapping preloading*. For example, this bundle instantiates all services tagged `object_mapper.filter` and inject them into the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) during its initial instantiation. Then the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) injects these tagged filters into the maps that it builds. This way tagged filters are *added automatically* to the [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php). So you do not need to do:

```php
// Define the map *on the go*

// Instantiate the implmented filters
$filterA = new FilterA($route);
$filterB = new FilterB($route);

// Add the filters to the map
$map = $mapBuilder
    ->addFilter($filterA)
    ->addFilter($filterB)
    ->buildMap($pathFindingStrategy = true)
;

// Map source to the target
$objectMapper->map($source, $target, $map);
```

Instead you can simply do:

```php
// The map is PREloaded, the implemented filters are added automatically to the map

// Map the source to the target
$target = $objectMapper->map($source, $target);
```

For its next release, the [ObjectMapperBundle](https://github.com/opportus/ObjectMapperBundle) project may implement more *mapping preloading* features such as configuration files and annotations preloading, making use of *Injection* and *Configuration* Symfony's components and Doctrine's *Annotations*. So that you do not need to do:

```php

// Define *on the go* the route
$map = $mapBuilder
    ->addRoute('User.getUserame()', 'User.$username')
    ->addRoute('User.getAge()', 'User.$age')
    ->buildMap($pathFindingStrategy = false)
;

// Map the source to the target
$objectMapper->map($source, $target, $map);
```

Instead you can do:

```php
UserDto
{
    /**
     * @HighLevelSystemRoute(sources={"User.getUsername()"})
     */
    public $username;

    /**
     * @HighLevelSystemRoute(sources={"User.getAge()"})
     */
    public $age;
}

$objectMapper->map($source, $target);
```

Or for example with a global map configuration file instead:

```yaml
# user-dto-map.yaml

-
source: User.getUsername()
target: UserDto.$username
-
source: User.getAge()
target: UserDto.$age
```
```php
$objectMapper->map($source, $target);
```

With the help of this library's [`RouteBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/RouteBuilder.php) service, the [ObjectMapperBundle](https://github.com/opportus/ObjectMapperBundle) can then build from this preloaded mapping configuration routes to inject into the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) during its initial instantiation in order for it to build aware maps.
