<p align="center">
<a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" height="200" alt="Laravel Logo"></a>
<img src="https://www.vectorlogo.zone/logos/postgresql/postgresql-icon.svg" height="200" alt="PostgresSQL Logo">
</p>

## About This Learning Project

This project is a simple CRUD application that uses Laravel 10 RESTful API and PostgresSQL. It is a learning project that I created to learn more about Laravel RESTful API and PostgresSQL.

## Endpoint

### User

- `POST /api/user/create` - Register User
- `POST /api/user/login` - Login User
- `GET /api/user/profile` - Get User Profile
- `PATCH /api/user/profile` - Update User Profile
- `DELETE /api/user/logout` - Logout User

### Contact

- `POST /api/contact` - Create Contact
- `GET /api/contact/{idContact}` - Get Contact
- `PUT /api/contact/{idContact}` - Update Contact
- `DELETE /api/contact/{idContact}` - Delete Contact
- `GET /api/contacts?name=&email=&phone=&size=&page=` - Get All Contacts with Query and Pagination

### Address

- `POST /api/contact/{idContact}/address/{idAddress}` - Create Address
- `GET /api/contact/{idContact}/address/{idAddress}` - Get Address
- `PUT /api/contact/{idContact}/address/{idAddress}` - Update Address
- `DELETE /api/contact/{idContact}/address/{idAddress}` - Delete Address
- `GET /api/contact/{idContact}/addresses` - Get List Addresses from Contact

## Installation

1. Clone this repository
2. Run `composer install`
3. Create a new database in PostgresSQL
4. Copy `.env.example` to `.env` and configure the database
5. Run `php artisan key:generate`
6. Run `php artisan migrate`
7. Run `php artisan serve`

## Source

Youtube Channel: [Programmer Zaman Now](https://www.youtube.com/@ProgrammerZamanNow)

