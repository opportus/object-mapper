# Object Mapper

Provides a powerful object mapping system.

Contributions are welcome.

## Index

- [Integrations](#integrations)
- [Installation](#installation)
- [Mapping](#mapping)
    - [Routing](#routing)
        - [Automatic routing](#automatic-routing)
        - [Manual routing](#manual-routing)
    - [Filtering](#filtering)
        - [Recursion](#recursion)
- [Mapping predefinition](#mapping-predefinition)
    - [Automatic instantiation and injection of filters](#automatic-instantiation-and-injection-of-filters)
    - [Configuration](#configuration)

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

This library contains 4 services. 3 of them require to be instantiated with their respective dependencies which are other lower level services among those 4.

```php
namespace Opportus\ObjectMapper;

/** @var Map\Route\Point\PointFactoryInterface */
$pointFactory = new Map\Route\Point\PointFactory();

/** @var Map\Route\RouteBuilderInterface */
$routeBuilder = new Map\Route\RouteBuilder($pointFactory);

/** @var Map\MapBuilderInterface */
$mapBuilder = new Map\MapBuilder($routeBuilder);

/** @var ObjectMapperInterface */
$objectMapper = new ObjectMapper($mapBuilder);
```

Services instantiation is handled by you. You may want to achieve that with a DI Container or manually (such as above) in your composition root.

## Mapping

Mapping object to object is done via the main [`ObjectMapper`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapper.php) service's method:

```php
ObjectMapper::map($source, $target, ?MapInterface $map = null): ?object
```

**Parameters**

`$source` must be an `object` to map data from.

`$target` must be an `object` (or a `string` being the Fully Qualified Name of a class to istantiate and) to map data to.

`$map` must be a `null` or an instance of [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php). If it is `null`, the method builds and uses a map composed of the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php).

**Returns**

- `null` if the map has no route connecting source points with target points.
- `object` which is the (instantiated and) updated target.

### Routing

A [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) is defined by and composed of its *source point* and its *target point*.

A *source point* can be either:

- A [`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)
- A [`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php)

A *target point* can be either:

- A [`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)
- A [`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php)

The [`ObjectMapper`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapper.php) method presented above iterates through each [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) that it gets from the [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php). Doing so, the method assigns the value of the current [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php)'s *source point* to its *target point*.

#### Automatic routing

A basic example about how to *automatically route* one `User` to one `UserDto` and vice-versa:

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

// Map the `User` instance to the `UserDto` instance
$objectMapper->map($user, $userDto);

echo $userDto->username; // 'Toto'

// Map the `UserDto` instance to a new `User`
$user = $objectMapper->map($userDto, User::class);

echo $user->getUsername(); // 'Toto'
```

The automatic mapping allows to map seemlessly objects to objects.

Calling the `ObjectMapper::map(object $source, $target, ?Map $map): ?object` method presented earlier, with its `$map` parameter set on `null` makes the method build then use a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) composed of the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php).

The default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php) behavior consists of guessing what is the appropriate point of the source class to connect to each point of the target class. The connected *source point* and *target point* compose then a [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) which is followed by the `ObjectMapper::map(object $source, $target, ?Map $map): ?object` method.

For the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php), a *target point* can be:

- A public property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php))
- A parameter of a public method ([`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php))

The corresponding *source point* can be:

- A public property having for name the same as the target point ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php))
- A public getter having for name `'get'.ucfirst($targetPointName)` and requiring no argument ([`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php))

Sometime, the default [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php) may not be the most appropriate behavior anymore. In this case, you can implement your own [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php), and in order to make it guess the appropriate [`RouteCollection`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/RouteCollection.php), reverse-engineer the classes to map:

```php
PathFindingStrategyInterface::getRoutes(Context $context): RouteCollection;
```

**Parameters**

`$context` An instance of [`Context`](https://github.com/opportus/object-mapper/blob/master/src/Context.php) which contains the arguments passed to the `ObjectMapper::map(object $source, $target, ?Map $map =null): ?object` method plus meta information.

**Returns**

[`RouteCollection`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/RouteCollection.php) connecting the *source points* with the *tagets points*.

**Example**

```php
class MyPathFindingStrategy implements PathFindingStrategyInterface
{
    // ...

    public function getRoutes(Context $context): RouteCollection
    {
        // Custom algorithm
    }

    // ...
}

// Pass to the map builder the strategy you want it to compose the map of
$map = $objectMapper->getMapBuilder()->buildMap(new MyPathFindingStrategy());

echo $map->getPathFindingStrategyFqn(); // 'MyPathFindingStrategy'

// Use the map
$user = $objectMapper->map($userDto, User::class, $map);

// ...
```

#### Manual routing

A basic example about how to *manually route* one `User` to one `ContributorDto` and vice-versa:

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

// Add manually the route to the map
$map = $objectMapper->getMapBuilder()
    ->addRoute('User::getUsername()', 'ContributorDto::$name')
    ->buildMap()
;

// Map the `User` instance to the `ContributorDto` instance
$objectMapper->map($user, $contributorDto, $map);

echo $contributorDto->name; // 'Toto'

// Add manually the route to the map
$map = $objectMapper->getMapBuilder()
    ->addRoute('ContributorDto::$name', 'User::__construct()::$username')
    ->buildMap()
;

// Map the `ContributorDto` instance to a new `User`
$user = $objectMapper->map($contributorDto, User::class, $map);

echo $user->getUsername(); // 'Toto'
```

The *manual routing* requires more work but gives you unlimited control over which *source point* to map to which *target point*.

One way to register routes manually is to use the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) API. The [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) is an immutable service which implement a fluent interface:

```php
MapBuilder::addRoute(string $sourcePointFqn, string $targetPointFqn, $filter = null): MapBuilderInterface
```

**Parameters**

`$sourcePointFqn` must be a `string` representing the Fully Qualified Name of a *source point* which can be:

- A public, protected or private property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)) represented by its FQN having for syntax `'My\Class::$property'`
- A public, protected or private method requiring no argument ([`MethodPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/MethodPoint.php)) represented by its FQN having for syntax `'My\Class::method()'`

`$targetPointFqn` must be a `string` representing the Fully Qualified Name of a *target point* which can be:

- A public, protected or private property ([`PropertyPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/PropertyPoint.php)) represented by its FQN having for syntax `'My\Class::$property'`
- A parameter of a public, protected or private method ([`ParameterPoint`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Point/ParameterPoint.php)) represented by its FQN having for syntax `'My\Class::method()::$parameter'`

`$filter` parameter must be either a `null`, a `callable` or an instance of [`FilterInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Filter/FilterInterface.php).

**Returns**

A **new** instance of [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php).

```php
MapBuilder::buildMap($pathFindingStrategy = false): Map
```

**Parameters**

`$pathFindingStrategy` must be either a `boolean` or an instance of [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php).

- If it is `false`, a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) composed of the [`NoPathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/NoPathFindingStrategy.php) will be built
- If it is `true`, a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) with the [`PathFindingStrategy`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategy.php) is built
- If it is an instance of [`PathFindingStrategyInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Strategy/PathFindingStrategyInterface.php), a [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php) composed of this instance is built

**Returns**

An instance of [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php).

### Filtering

A *filter* allows you to modify the *source point* value before it gets assigned to the *target point* by the mapper.

In order to make good use of the *filter* feature, you have to understand what a [`Context`](https://github.com/opportus/object-mapper/blob/master/src/Context.php) is. [`Context`](https://github.com/opportus/object-mapper/blob/master/src/Context.php) holds the arguments that we inject into the `ObjectMapper::map(object $source, $target, ?Map $map =null): ?object` method, plus their meta information. Furthermore, this [`Context`](https://github.com/opportus/object-mapper/blob/master/src/Context.php) validates itself, so you can fully rely on it, working with it and passing it around (*#primitive obsession*).

2 basic examples about how to *filter*:

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

$mapBuilder = $objectMapper->getMapBuilder();
```

Build a map adding a `callable` filter on a specific route:

```php
$map = $mapBuilder
    ->addRoute('User::getAge()', 'UserDto::$age', function ($route, $context, $objectMapper) {
        return $route->getSourcePoint()->getValue($context->getSource() + 1;
    })
    ->buildMap($automaticRouting = $pathFindingStrategy = true)
;

$userDto = $objectMapper->map($user, $userDto, $map);// The `$context` you get in the `callable` filter above

echo $userDto->age; // '31'
```

Build a map adding a filter implemeting `FilterInterface` on a specific route:

```php
class Filter implements FilterInterface
{
    private $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /** {@inheritdoc} */
    public function getRouteFqn(): string
    {
        return $this->route->getFqn();
    }

    /** {@inheritdoc} */
    public function getValue(Context $context, ObjectMapperInterface $objectMapper)
    {
        return $this->route->getSourcePoint()->getValue($context->getSource()) + 1;
    }
}

$route = $routeBuilder->buildRoute('User::getAge()', 'UserDto::$age');

$filter = new Filter($route);

$map = $mapBuilder
    ->addFilter($filter)
    ->buildMap($automaticRouting = $pathFindingStrategy = true)
;

$userDto = $objectMapper->map($user, $userDto, $map); // The `$context` you get in the `Filter` above

echo $userDto->age; // '31'
```

The `ObjectMapper::map(object $source, $target, ?Map $map =null): ?object` method gets the filtered value by calling on your [`FilterInterface`](https://github.com/opportus/object-mapper/blob/master/src/Map/Filter/FilterInterface.php) implementation the method described below:

```php
FilterInterface::getValue(Context $context, ObjectMapperInterface $objectMapper)
```

**Parameters**

`$context` is an instance of [`Context`](https://github.com/opportus/object-mapper/blob/master/src/Context.php).

`$objectMapper` is the instance of [`ObjectMapperInterface`](https://github.com/opportus/object-mapper/blob/master/src/ObjectMapperInterface.php).

**Returns**

A `mixed` value.

**Throws**

A [`NotSupportedContextException`](https://github.com/opportus/object-mapper/blob/master/src/Exception/NotSupportedContextException.php) signaling to the mapper to assign the ***original*** value of the *source point* to the *target point*.

Keep in mind that a *filter* is *attached to* a specific [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php).

You define the [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) to which to *attach* your *filter* in the method described below:

```php
FilterInterface::getRouteFqn(): string
```

**Returns**

A `string` being the **Fully Qualified Name** of the [`Route`](https://github.com/opportus/object-mapper/blob/master/src/Map/Route/Route.php) this *filter* is on.

#### Recursion

You can use the *filter* to recursively map a *source point* to a *target point*. For example:

- If you map an instance of `A` (that *has* `C`) to `B` (that *has* `D`) and that you want in the same time to map `C` to `D`, AKA *simple recursion*
- IF you map an instance of `A` (that *has many* `C`) to `B` (that *has many* `D`) and that you want in the same time to map many `C` to many `D`, AKA *iterable recursion* or *in-width recursion*

You can achieve *in-depth recursion* naturally, by adding a *filter* to a route of the child type, the grandchild type and so on...

##### Simple recursion

A basic example about how to map `A` (with its `C`) to `B` (and its `D`):

```php
class User
{
    private $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }
}

class UserDto
{
    /** @var CompanyDto $company */
    public $conpany; // We want an instance of `CompanyDto` here, NOT an instance of `Company`
}

class Company
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

class CompanyDto
{
    public $name;
}

$user    = new User(new Company('SensioLabs'));
$userDto = new UserDto();

class SimpleRecursionFilter implements FilterInterface
{
    private $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /** {@inheritdoc} */
    public function getRouteFqn(): string
    {
        return $this->route->getFqn();
    }

    /** {@inheritdoc} */
    public function getValue(Context $context, ObjectMapperInterface $objectMapper)
    {
        echo $this->route->getFqn(); // 'User::getCompany()=>UserDto::$company'
        echo $this->route->getSourcePoint()->getFqn(); // 'User::getCompany()'
        echo $this->route->getTargetPoint()->getFqn(); // 'UserDto::$company'

        // The `User` instance
        $parentSource = $user = $context->getSource();

        // The `UserDto` instance
        $parentTarget = $userDto = $context->getTarget();
        
        // The `Company` instance
        $childSource = $company = $this->route->getSourcePoint()->getValue($user);
        $childSource = $company = $user->getCompany();

        // The `CompanyDto` class name
        $childTarget = $companyDto = CompanyDto::class;

        // The `Map` instance
        $childMap = $context->getMap();

        return $objecMapper->map($childSource, $childTarget, $childMap);
    }
}

$route = $routeBuilder->buildRoute('User::getCompany()', 'UserDto::$company');

$map = $mapBuilder
    ->addFilter(new SimpleRecursionFilter($route))
    ->buildMap($automaticRouting = $pathFindingStrategy = true)
;

$userDto = $objectMapper->map($user, $userDto, $map); // The `$context` you get in the `SimpleRecursionFilter` above

echo get_class($userDto->company); // 'CompanyDto'
echo $userDto->company->name; // 'SensioLabs'
```

##### Iterable recursion

Use this approach to map objects of an iterable (such as array or collection) of the source to an iterable of the target.

A basic example about how to map `A` (with its many `C`) to `B` (and its many `D`):

```php
class User
{
    /** @var User[] $friends */
    private $friends;

    public function __construct(array $friends = [])
    {
        $this->friends = $friends;
    }

    public function getFriends(): array
    {
        return $this->friends;
    }
}

class UserDto
{
    /** @var UserDto[] $friends */
    public $friends; // We want instances of `UserDto` here, NOT instances of `User`
}

$user    = new User(new User(), new User());
$userDto = new UserDto();

class IterableRecursionFilter implements FilterInterface
{
    private $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /** {@inheritdoc} */
    public function getRouteFqn(): string
    {
        return $this->route->getFqn();
    }

    /** {@inheritdoc} */
    public function getValue(Context $context, ObjectMapperInterface $objectMapper)
    {
        $friends = $this->route->getSourcePoint()->getValue($context->getSource());

        $friendDtoCollection = new FriendDtoCollection();
        foreach ($friends as $friend) {
            $friendDtoCollection->addFriend(
                $objectMapper->map($friend, UserDto::class, $context->getMap())
            );
        }

        return $friendDtoCollection;
    }
}

$route = $routeBuilder->buildRoute('User::getFriends()', 'UserDto::$friends');

$map = $mapBuilder
    ->addFilter(new IterableRecursionFilter($route))
    ->buildMap($automaticRouting = $pathFindingStrategy = true)
;

$userDto = $objectMapper->map($user, $userDto, $map); // The `$context` you get in the `IterableRecursionFilter` above

echo get_class($userDto->friends) // 'FriendDtoCollection'

foreach ($userDto->friends as $friend) {
    echo get_class($friend); // 'UserDto'
}
```

## Mapping predefinition

In the code examples above, we *define the map* (adding to it routes and filters) *on the go*, via the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) API. There is another way to *define the map*, called *mapping PREdefinition*. While this library is designed with *mapping predefinition* in mind, it does not *implement* it.

This library aims to remain dependency-less and as much simple as possible, therefore it does not ship any configuration and DIC system necessary for achieving *mapping predefinition*. So this chapter is an attempt to help you implementing *mapping predefinition* making use of your own configuration and DIC systems.

### Automatic instantiation and injection of filters

For instance, [ObjectMapperBundle](https://github.com/opportus/ObjectMapperBundle) does implement such a *mapping predefinition*. For example, this bundle instantiates all services tagged `object_mapper_filter` and inject them into the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) during its initial instantiation. Then the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) injects these aliased filters into the maps that it builds. This way tagged filters are *added automatically* to the [`Map`](https://github.com/opportus/object-mapper/blob/master/src/Map/Map.php). So you do not need to write:

```php
// Define the map *on the go*

$filterA = new FilterA($route);
$filterB = new FilterB($route);

$map = $mapBuilder
    ->addFilter($filterA)
    ->addFilter($filterB)
    ->buildMap($automaticRouting = $pathFindingStrategy = true)
;

$target = $objectMapper->map($source, $target, $map);
```

Instead you can simply write:

```php
// The map is PREdefined, filters are added automatically to the map

$target = $objectMapper->map($source, $target);
```

### Configuration

You can predefine a map statically, via a configuration file:

```php
// object-mapping.php

return [
    [
        'source' => 'User::getUsername()',
        'target' => 'UserDto::$username',
    ],
    [
        'source' => 'User::getAge()',
        'target' => 'UserDto::$age',
        'filter' => $filter,
    ],
]
```

Then, such as the [ObjectMapperBundle](https://github.com/opportus/ObjectMapperBundle) does with filters, inject these loaded and parsed configurations into the [`MapBuilder`](https://github.com/opportus/object-mapper/blob/master/src/Map/MapBuilder.php) in order for it to build aware maps.