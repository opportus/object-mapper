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
  - [Automatic mapping](#automatic-mapping)
  - [Manual mapping](#manual-mapping)
  - [Filtering](#filtering)
  - [Recursion](#recursion)
- [Mapping autoloading](#mapping-autoloading)

## Use cases

Use this solution for copying state of objects to differently typed objects using extensible controls and mapping strategies.

Simple, flexible, extensible, this tool can be used in many use cases such as:

- Mapping state of objects from/to DTOs.
- Mapping state of domain model from/to persistance model.
- Mapping state of domain model to view model.
- Mapping state of objects to differently typed objects to get operated on, so that these operations don't mess with the state of the source objects.
- As an *object mapping* subsystem to be integrated by systems such as frameworks, serializers, form handlers, ORMs, etc...
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

The rational behind this design is:

In any use case context, there is **already** a dedicated solution for handling **properly** instantiation of services **because** that is not a problem an *object mapper* is meant to solve. This problem is also know as *#dependency-injection*, *#lazy-loading*, *#inversion-of-control*, and so on... So that delegating the instantiation of its services to its client, this solution becomes more **simple**, **flexible**, **extensible**, **performant**, and **integrable**.

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

## Mapping

Mapping object to object is done via the main [`ObjectMapper`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapper.php) service's method:

```php
ObjectMapper::map(object $source, $target, ?Map $map = null): ?object
```

**Parameters**

`$source` must be an `object` to map data from.

`$target` must be an `object` (or a `string` being the Fully Qualified Name of a class to istantiate and) to map data to.

`$map` must be a `null` or an instance of [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php). If it is `null`, the method builds and uses a map composed of the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php).

**Returns**

  - `null` if the map has no route connecting source points with target points.
  - `object` which is the (instantiated and) updated target.

### How it works

The [`ObjectMapper`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapper.php) method presented above iterates through each [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) that it gets from the [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php). Doing so, the method assigns the value of the current [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php)'s *source point* to its *target point*, optionally applying your implemented [`FilterInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Filter/FilterInterface.php) during this value assignment.

A [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) is defined by and composed of its *source point* and its *target point*.

A *source point* can be either:

  - A [`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)
  - A [`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php)

A *target point* can be either:

  - A [`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)
  - A [`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php)

Routes can be defined either [automatically](#automatic-mapping) (default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php)) or [manually](#manual-mapping) ([`NoPathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/NoPathFindingStrategy.php)).

### Automatic mapping

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

The *automatic mapping* allows to map seamlessly source object's state to the target object.

Calling the `ObjectMapper::map(object $source, $target, ?Map $map = null): ?object` method presented earlier, with its `$map` parameter set on `null`, makes the method build then use a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) composed of the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php).

The default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php) behavior consists of guessing what is the appropriate point of the source class to connect to each point of the target class. The connected *source point* and *target point* compose then a [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) which is followed by the `ObjectMapper::map(object $source, $target, ?Map $map = null): ?object` method.

For the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php), a *target point* can be:

  - A public property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php))
  - A parameter of a public method ([`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php))

The corresponding *source point* can be:

  - A public property having for name the same as the target point ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php))
  - A public getter having for name `'get'.ucfirst($targetPointName)` and requiring no argument ([`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php))

#### Custom automatic mapping

The default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php) presented above is based on one **particular convention** of naming source and target points. However, you might have to map objects which are not complying to this **particular convention**. Therefore, in order to *automatically* map these objects, the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php) is not usable anymore.

One solution is to implement a custom [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php) specific to these objects. In order to make your implemention define an appropriate [`RouteCollection`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/RouteCollection.php) to return, reverse-engineer the source and target classes to map.

For concrete example of how to implement a [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php), refer to the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php).

Below is described the single method of the [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php):

```php
PathFindingStrategyInterface::getRoutes(Context $context): RouteCollection;
```

**Parameters**

`$context` An instance of [`Context`](https://github.com/opportus/object-mapper/blob/master/src/Context.php) which contains the arguments passed to the `ObjectMapper::map(object $source, $target, ?Map $map = null): ?object` method plus their meta information.

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
$map = $objectMapper->getMapBuilder()->buildMap(new MyPathFindingStrategy());

echo $map->getPathFindingStrategyFqn(); // 'MyPathFindingStrategy'

// Use the map
$user = $objectMapper->map($userDto, User::class, $map);
```

### Manual mapping

If the objects to map do not comply to the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php) convention, instead than implementing a [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php) for *automatic mapping*, you can use *manual mapping*.

There are two ways of achieving *manual mapping*. One way is [Mapping autoloading](#mapping-autoloading) and the second way is to define routes *on the go* via the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) API.

The [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) is an immutable service which implement a fluent interface.

A basic example of how to manually map `User`'s state to `ContributorDto` and vice-versa:

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

Add routes to the map with the method described below:

```php
MapBuilder::addRoute(string $sourcePointFqn, string $targetPointFqn): MapBuilderInterface
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

Finally, build the previously defined [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) with the method below:

```php
MapBuilder::buildMap($pathFindingStrategy = false): Map
```

**Parameters**

`$pathFindingStrategy` must be either a `boolean` or an instance of [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php).

  - If it is `false`, a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) composed of the [`NoPathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/NoPathFindingStrategy.php) will be built.
  - If it is `true`, a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) with the [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php) is built. Note that in our context of *manual mapping* that wouldn't make sense, since the map would ignore the routes you previously defined.
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

$user    = new User(30);
$userDto = new UserDto();

/********************************************************************************************************
    Solution 1: build a map adding a `callable` filter on a specific route
*********************************************************************************************************/

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

/********************************************************************************************************
    Solution 2: build a map adding a filter implemeting `FilterInterface`
*********************************************************************************************************/

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

/*********************************************************************************************
   Same result for either of the solutions
*********************************************************************************************/

$userDto = $objectMapper->map($user, $userDto, $map); // The `$context` you get in the `Filter` above

echo $userDto->age; // '31'
```

The `ObjectMapper::map(object $source, $target, ?Map $map =null): ?object` method gets the filtered value by calling on your [`FilterInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Filter/FilterInterface.php) implementation instance the method described below:

```php
FilterInterface::getValue(Route $route, Context $context, ObjectMapperInterface $objectMapper)
```

**Parameters**

`$route` is an instance of the [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) the `ObjectMapper::map(object $source, $target, ?Map $map =null): ?object` method is currently working on.

`$context` is an instance of [`Context`](https://github.com/opportus/object-mapper/blob/master/src/Context.php) containing the arguments passed to the `ObjectMapper::map(object $source, $target, ?Map $map =null): ?object` method.

`$objectMapper` is the instance of [`ObjectMapperInterface`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapperInterface.php) which can be useful for [recursion](#recursion).

**Returns**

A `mixed` value.

Keep in mind that a *filter* *supports* instance(s) of [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php).

You have to define the supported routes by implementing the method described below:

```php
FilterInterface::supportRoute(Route $route): bool
```

**Returns**

A `bool` defining whether the passed [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) is supported by this *filter*.

### Recursion

Although a recursion dedicated feature may come later, you can use the *filter* to recursively map a *source point* to a *target point*. For example:

  - If you map an instance of `A` (that *has* `C`) to `B` (that *has* `D`) and that you want in the same time to map `C` to `D`, AKA *simple recursion*
  - If you map an instance of `A` (that *has many* `C`) to `B` (that *has many* `D`) and that you want in the same time to map many `C` to many `D`, AKA *iterable recursion* or *in-width recursion*

You can achieve *in-depth recursion* by adding a *filter* to a route of the child type, the grandchild type and so on...

## Mapping autoloading

In the code examples above, we *define the map* (adding to it routes and filters) *on the go*, via the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) API. There is another way to *define the map*, *mapping autoloading*.

While this library is designed with *mapping autoloading* in mind, it does not *implement* it.

Indeed, this solution is designed to be simple, flexible and extensible, as a core. Therefore it does not ship any DIC and configuration system necessary for achieving *mapping autoloading* which is better implemented into your specific context.

So this chapter is an attempt to help you implementing *mapping autoloading* making use of your own DIC and configuration systems in order for this solution to be integrated seamlessly as a subsystem into a wider system such as a framework, serializer, form handler, ORM or whatever.

For instance, [ObjectMapperBundle](https://github.com/opportus/ObjectMapperBundle) does implement to some extend such *mapping autoloading*. For example, this bundle instantiates all services tagged `object_mapper.filter` and inject them into the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) during its initial instantiation. Then the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) injects these tagged filters into the maps that it builds. This way tagged filters are *added automatically* to the [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php). So you do not need to do:

```php
// Define the map *on the go*

$filterA = new FilterA($route);
$filterB = new FilterB($route);

$map = $mapBuilder
    ->addFilter($filterA)
    ->addFilter($filterB)
    ->buildMap($pathFindingStrategy = true)
;

$target = $objectMapper->map($source, $target, $map);
```

Instead you can simply do:

```php
// The map is PREloaded, filters are added automatically to the map

$target = $objectMapper->map($source, $target);
```

For its next release, the [ObjectMapperBundle](https://github.com/opportus/ObjectMapperBundle) project may implement configuration (config file, annotation) autoloading, leveraging *Injection* and *Configuration* Symfony's components and Doctrine's *Annotations*. So that you do not need to do:

```php
// Mapping state of `User` object to `UserDto` object

$map = $mapBuilder
    ->addRoute('User.getUserame()', 'User.$username')
    ->addRoute('User.getAge()', 'User.$age')
    ->buildMap($pathFindingStrategy = false)
;

$objectMapper->map($source, $target, $map);
```

Instead you can do:

```php
UserDto
{
    /**
     * @ObjectMapperRoute(sources="User.getUsername()")
     */
    public $username;

    /**
     * @ObjectMapperRoute(sources="User.getAge()")
     */
    public $age;
}
```

Or:

```yaml
# user-dto-map.yaml

-
source: User.getUsername()
target: UserDto.$username
-
source: User.getAge()
target: UserDto.$age
```

The [ObjectMapperBundle](https://github.com/opportus/ObjectMapperBundle) can then build from this autoloaded mapping configuration routes and filters to inject into the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) during its initial instantiation in order for it to build aware maps. So that the user can then map seamlessly state of the typed objects such as defined into the mapping configuration above:

```php
// Mapping state of `User` object to `UserDto` object
$objectMapper->map($source, $target);
```
