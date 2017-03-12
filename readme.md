# Welcome to the Eventus Server
**the Back End API for the Eventus cluster of applications**

### Extensive Documentation can be found in the [Eventus Server Wiki](https://github.com/kennyhong/Eventus-Server/wiki)  

_**This project uses Laravel Framework**_  
We're using Laravel (PHP) to build out the API. All structure is discussed and defined by the framework developers. All content in this repository is required for the functionality (or future functionality) of the server application.

**Here is the general structure for the parts of the architecture that we have implemented:**
```
- app
  - Exceptions
    - EventusException: A generic parent Exception for our server application.
    - Handler(modified): Added a hook in render() that sends a JSON response instead of a rendered HTML page.
  - Http
    - Controllers
      - EventController: Handles interaction between the Event objects, the persistence layer, and children.
      - ServiceController: Handles interaction between the Service objects, the persistence layer, and children.
      - ServiceTagController: Handles interaction between the ServiceTag objects and the persistence layer.
    - Middleware
      - CorsHeaders: Adds the headers necessary to allow Cross-Origin Resource Sharing.
      - EventusJsonResponseFormat: Modifies the outgoing response in-transit to meet our Eventus JSON spec.
  - Event: Class definition for an Eventus Event object.
  - Service: Class definition for an Eventus Service object.
  - ServiceTag: Class definition for an Eventus ServiceTag object.
```

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb combination of simplicity, elegance, and innovation give you tools you need to build any application with which you are tasked.

## Learning Laravel

Laravel has the most extensive and thorough documentation and video tutorial library of any modern web application framework. The [Laravel documentation](https://laravel.com/docs) is thorough, complete, and makes it a breeze to get started learning the framework.

If you're not in the mood to read, [Laracasts](https://laracasts.com) contains over 900 video tutorials on a range of topics including Laravel, modern PHP, unit testing, JavaScript, and more. Boost the skill level of yourself and your entire team by digging into our comprehensive video library.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
