[![PR CI](https://github.com/SO-LEAN/cleanprospecter/actions/workflows/on-pr.yml/badge.svg)](https://github.com/SO-LEAN/cleanprospecter/actions/workflows/on-pr.yml)
![maintanibility](https://api.codeclimate.com/v1/badges/b61cae7437cba2d564fb/maintainability)
![coverage](https://api.codeclimate.com/v1/badges/b61cae7437cba2d564fb/test_coverage)
# Cleanprospecter

**Cleanprospecter** is a php 7.2 business prospect application designed according to Robert C. Martin [recommendations for clean architecture](https://8thlight.com/blog/uncle-bob/2012/08/13/the-clean-architecture.html).

Add cleanprospecter in your project with [composer](https://getcomposer.org).

```console
 $ composer require so-lean/cleanprospecter
```

A **symfony 4.1** implementation can be found on github [here](https://github.com/SO-LEAN/prospecterapp)
## Progress

Consider that scope as the **minimal viable product**.
 
- [x] As anonymous, I want to login
- [x] As main app, I want to refresh user
- [x] As prospector, I want to create organization
- [x] As prospector, I want to find my own organizations
- [x] As prospector, I want to get organization
- [x] As prospector, I want to update organization
- [x] As prospector, I want to create organization
- [x] As user, I want to get my account information
- [x] As user, I want to update my account information
- [x] As user, I want to remove my organization logo
- [ ] As prospector, I want to create prospect
- [ ] As prospector, I want to find my own prospects
- [ ] As prospector, I want to create phone call event
- [ ] As prospector, I want to create appointment event
- [ ] As prospector, I want to create email event
- [ ] As prospector, I want to create sms event
- [ ] As prospector, I want to find my own prospects

## In the future
* tags
* auto import events from email box, short message service etc...
* email marketing campaign
 
## Clean architecture -_Business rules as a simple composer package._-

<p align="center">
  <img src="https://8thlight.com/blog/assets/posts/2012-08-13-the-clean-architecture/CleanArchitecture-8d1fe066e8f7fa9c7d8e84c1a6b0e2b74b2c670ff8052828f4a7e73fcbbc698c.jpg" alt="The Clean Architecture">
</p>

A good explanation is available in this Uncle Bob [talk here](https://www.youtube.com/watch?v=Nsjsiz2A9mg)

> A GOOD ARCHITECTURE MAXIMIZES THE NUMBER OF DECISIONS NOT MADE
> - UNCLE BOB

## Terminological differences

In order to clarify some uncle bob concepts

* Interactors becomes use cases and are locatated in src/UseCase/**UseCaseName** and take its name from it : _ex_ FindMyOwnOrganizations
* Request an response are data transfer object and are located at the same place : _ex_ FindMyOwnOrganizations**Request**, FindMyOwnOrganizations**Response**
* Presenter interface (Dependency inversion) too : _ex_ FindMyOwnOrganizations**Presenter**
* Gateways is not only database abstraction, entity gateway are located in src/Gateway/Entity


## How to implement cleanprospecter

Clean architecture use dependency injection to build uses cases.

1 You need to implement all Gateways in your main application
* Build use cases in the IOC
* Register it in the facade.

```php
    // in IOC
    
    //OrganizationGatewayImpl implements OrganizationGateway interface
    $organizationGateway = new OrganizationGatewayImpl();
 
    $useCase = new GetOrganizationImpl($organizationGateway);
    
    //Create facade and register use case
    $facade = new UseCasesFacade();
    $facade->addUseCase($useCase);
```

```php
    // in controller (or somewhere else)
    $request = new GetOrganizationRequest(7);
    
    //presenter implements GetOrganizationPresenter
    $presenter = new GetOrganizationPresenterImpl();
    
    //all use case is accessible by their name 
    $facade->getOrganization($request, $presenter);
```

A use case can say what it does

```php
   //...
   
   $useCase = new GetOrganizationImpl($organizationGateway);
   
   echo $useCase;
   
   //Display : "As prospector, I want to get organization"
```

## Developer tools

### prerequisites

* docker
* docker-compose

All common command lines are accessible by the Makefile. 
Make create a docker image based on official php alpine docker image (php 7.2.3) with xdebug and composer installed globally.

```console
    $ make
```

### Commands

make **command**

| Command         | comments                                                   | 
| ----------------|------------------------------------------------------------|
| build-env       | Build the docker env file tagged _prospecter-run_          |
| composer        | install vendors                                            |
| composer-update | update vendors                                             |
| test            | execute tests suite                                        |
| testdox         | execute tests and write agile documentation in text format |                     
| test-coverage   | execute tests and generate report in html format           |
| cs              | code sniffer                                               |
| cs-fix          | fix automatically code sniffer errors                      |
