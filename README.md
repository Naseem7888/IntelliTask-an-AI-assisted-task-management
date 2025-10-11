# IntelliTask

IntelliTask is a Laravel 10 project designed to streamline task management and enhance productivity. This application provides a robust framework for managing tasks, users, and various functionalities that support collaborative work.

## Features

- User authentication and management
- Task creation, updating, and deletion
- API endpoints for task management
- Frontend integration with modern JavaScript and CSS

## Installation

To set up the project, follow these steps:

1. Clone the repository:
   ```
   git clone <repository-url>
   ```

2. Navigate to the project directory:
   ```
   cd IntelliTask
   ```

3. Install the dependencies:
   ```
   composer install
   npm install
   ```

4. Set up your environment file:
   ```
   cp .env.example .env
   ```

5. Generate the application key:
   ```
   php artisan key:generate
   ```

6. Run the migrations:
   ```
   php artisan migrate
   ```

7. Seed the database (optional):
   ```
   php artisan db:seed
   ```

8. Start the development server:
   ```
   php artisan serve
   ```

## Email verification (dev vs prod)

This project ships with routes that use the `verified` middleware (for example, `GET /dashboard` is protected by `['auth', 'verified']`). The behavior depends on whether the `User` model implements the `MustVerifyEmail` interface and how mail is configured.

- Local development (recommended):
  - `app/Models/User.php` does NOT implement `MustVerifyEmail`, so the `verified` middleware is effectively bypassed for local dev.
  - `.env` uses the log mail driver: `MAIL_MAILER=log` to avoid needing an SMTP server.
  - Result: You can register and access the app immediately without sending any emails.

- Production (recommended):
  1) Re-enable email verification in the user model:
     - In `app/Models/User.php` add `use Illuminate\Contracts\Auth\MustVerifyEmail;` at the top
     - Change the class signature to `class User extends Authenticatable implements MustVerifyEmail`
  2) Configure a working mail transport in the production `.env`:
     - `MAIL_MAILER=smtp`
     - Set `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`
  3) Apply the changes:
     - `php artisan optimize:clear`
  4) Result: Users must verify their email before accessing routes with `verified` middleware (e.g., the dashboard).

Notes:

- If you want to test verification flows without an SMTP account, you can still keep `MustVerifyEmail` and set `MAIL_MAILER=log` so emails are written to `storage/logs/laravel.log`.
- Popular providers: Mailtrap (testing), SendGrid, Mailgun, AWS SES, or your SMTP host.

## Usage

Once the application is running, you can access it at `http://localhost:8000`. You can register a new user, log in, and start managing your tasks.

## Contributing

Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License

This project is licensed under the MIT License. See the LICENSE file for more details.
