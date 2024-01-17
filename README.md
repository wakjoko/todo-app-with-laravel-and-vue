<h1 align="center">Todo App</h1>

## Introduction
Just like [todoist](https://todoist.com), this app is meant to help you prioritize your tasks.\
This app is assuming you gonna have 4 categories of tasks:
- Todo
- Completed
- Archived
- Deleted (not recoverable, thus not shown at all)

In any of above categories except **Deleted** obviously, you may filter the tasks by priority, title and due date which accept a range of dates.\
You may also sort the tasks by latest/oldest creation date, due date and tasks's priority.\
The task editor resembles todoist a lot which you can write the task details and add any photos or file attachments along.\
Forgot to mention that you may register an account only with a real email address.

## Frontend
It's based on Vue3. The entry point is at `resources/views/vue.blade.php` and to dig deeper, check out the js files at `resources/js/app.js`.

## Self hosting
Assuming you already install a database, proceed with below steps:
- Open up your terminal
- Clone this repository `git clone https://github.com/wakjoko/todo-app-with-laravel-and-vue.git`
- Change directory `cd todo-app-with-laravel-and-vue`
- Copy environment file `cp .env.example .env`
- Set the database connection in `.env`
- Initialize the app
    - `composer install`
    - `php artisan key:generate`
    - `php artisan storage:link`
    - `php artisan migrate --force`
    - `php artisan db:seed --force` (optional if you wanna have sample data)
    - `npm install`
    - `npm run build` or `npm run dev`
- Start the app service with `php artisan serve` or install a webserver for better performance

## Under the hood
- [**Laravel 10**](https://laravel.com/docs/10.x)
- [**Vue 3**](https://devdocs.io/vue~3)
- [**Bootstrap 5**](https://getbootstrap.com/docs/5.3/getting-started/introduction)

## License
This application is licensed under the [MIT license](http://opensource.org/licenses/MIT).
