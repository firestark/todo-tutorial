<img src="./logo.svg" width="250" align="center" vertical-align="top">


Firestark is a **non MVC PHP7 framework** which separates business logic from implementation logic. Firestark achieves this separation by giving you a special architecture that completely rids the business logic from outside dependencies. Instead the implementation logic is responsible for dependencies and speaks with the businesses logic to make a working application. This way the business logic is a very simple and readable layer to work in.

- A simple todo application example can be found [here](https://github.com/firestark/todo)
- An example project can be found [here](https://github.com/firestark/goalstark)

```php
<?php

use function compact as with;

when ( 'i want to add a todo', then ( apply ( a ( 
    
function ( todo $todo, todo\manager $manager )
{
    if ( $manager->has ( $todo ) )
        return [ 2000, with ( 'todo' ) ];

    $manager->add ( $todo );
    return [ 1000, with ( 'todo' ) ];
} ) ) ) );
```
> Firestark's business logic code example


## Firestark's propositions

### Business driven architecture

Firestark puts business logic first. At the highest level you will immediately see what the application is meant to do. Technical implementations can be found at lower levels.

### You ain't gonna need it

Firestark is built with [YAGNI](https://martinfowler.com/bliki/Yagni.html) in mind. Adding more code adds more complexity, often in vain. Firestark provides just the functionality you need and not a line of code more.

### Flexibility

Firestark is very small by default and uses the following components:

- IOC container
- small HTTP layer
- HTTP router

With these components firestark provides you a basic architecture to built well structured, business driven applications. The architecture is built in such a way that you can easily extend it with your own favorite components.

### Fast

Because firestark does not include any unnecessary code building fast and robust applications with firestark is easy.


## Getting started

### Server Requirements

- PHP >= 7.1.3
- Host pointing to / (for example: virtual host)

### Installation

1. `composer create-project firestark/project`
2. Make sure the application can write inside the `client/storage` directory.

## Directory structure

| Directory        | Description                  |
| ---------------- | ---------------------------- |
| /app             | Business logic               |
| /app/procedures  | Business logic procedures    |
| /app/agreements  | Business logic entities      |
| /client          | Technical layer              |
| /client/services | Implementation of agreements |
| /client/bindings | App implementations bindings |
| /client/routes   | Http routes                  |
| /client/statuses | Business status matchers     |
| /client/facades  | Technical facades            |

## Inspirations

### Years of lost architecture

[Good software architecture explanation](https://www.youtube.com/watch?v=WpkDN78P884)

#### key takeaways
> A good Architecture immediately shows it's intent

> A good architecture allows for major decisions to be defered

> The database is a detail

By Robert C. Martin


## The general idea

The general idea is that we split up the entire application into 2 layers:
- The business logic
- The implementation logic

### The business logic

The business logic is the part of your application where business rules are enforced. This layer is split up into two different section:
- Agreements
- Procedures

#### Procedures

Procedures apply rules decided by the business to the application. The following are examples of such rules:
- A todo with given description may only occur once.
- A person with a bronze account has 10% price reduction on his total buyings.
- Booking a flight in holiday seasons adds an additional 15% cost on the base price.

Next to applying these rules the procedure usually calls some methods to create, read, update or delete some entities in the system. In the end a procedure returns a status, based on the result of the applied rules with optionally some data relevant to that status.

#### Agreements

Agreements are plain php objects. An agreement can be an entity which describes all the properties that belong to that entity or a business service that interacts with entities.

##### Entity

An example of an entity is a: todo. That todo entity describes all the data that belongs to a todo. A todo could for example exist of a description, a flag to see whether it’s completed and a due date.


##### Business service

An example of a service is a todo manager. The todo manager is an access point to the todos, like a repository. The todo manager describes all the things that can be done with todo’s. For example a todo can be retrieved, added, updated or deleted. The important part here is that this business service **must not depend** on concrete implementations. This means this business service does not know about the underlying used persistence mechanism (eg. database, flatfile). This business service simply describes what can and may be done with a todo.


### The implementation logic

The implementation logic is responsible to implement all the things the business logic needs to create a working application. The following things reside in this layer:
- Services: Implementations of business services
- Container bindings
- Status matchers: To respond on statuses returned by the business logic
- HTTP Routing
- Views
- Facades


#### Services

A service is an implementation of a business service. The business service states some functionality that the service needs to implement. For example: A service that implements the todomanager could be a flatfiletodomanager. The flatfiletodomanager implements all functionality the todomanager in the business logic states and stores the results in a flat file.


#### Bindings

A binding binds a service to a business service in the application. This way we can choose what implementation to use in our application. For example if i want to use a flatfiletodomanager as the used todomanager in my application i would use a binding to do that.


#### Status matchers

A status matcher matches a particular status returned by the business logic. In this status matcher we can do some final calculations before sending back a response to the client.

## More information

More information can be found [here](https://github.com/firestark/project/tree/master/docs).

## Contributions

Contributions and feedback are very appreciated, feel free to [place an issue](https://github.com/firestark/project/issues).
