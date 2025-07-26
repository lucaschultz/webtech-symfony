# Webtech Symfony

This repository contains the code for the project required as part of the
Webtech course BA-INF 162 Web Technologies at the University of Bonn. Check out
the tasks in the [TASKS.md](TASKS.md) file to see what we are working on.

## Installation

First make sure you have the following prerequisites installed on your system:

- **Symfony CLI** to run the development server and manage the project. Head
  over to their site for the
  [installation instructions](https://symfony.com/download).
- **Docker** to run containers for the database. Head over to their site for the
  [installation instructions](https://docs.docker.com/engine/install/)
- **Node.js** to run the JavaScript build tools like Prettier for formatting and
  CSpell for spellchecking. I would recommend installing Node.js via the Fast
  Node Manager (`fnm`) for easier version management. Head over to their GitHub
  page for the
  [installation instructions](https://github.com/Schniz/fnm?tab=readme-ov-file#installation).
- **pnpm** as the package manager for JavaScript. Head over to their site for
  the [installation instructions](https://pnpm.io/installation).

To check if your system meets the requirements for Symfony, you can run:

```bash
symfony check:requirements
```

If that command returns errors, check ot the
[installation instructions for Symfony](https://symfony.com/doc/current/setup.html).
Otherwise clone this repository and run the following commands to set up the
project:

```bash
git clone https://github.com/lucaschultz/webtech-symfony
cd webtech-symfony/
composer install
pnpm install
```

If you are using VSCode, install the recommended extensions by opening the
`.vscode/extensions.json` file and clicking on the "Install" button.

## Running the Application

To run the application, make sure Docker is running and start the docker
composition:

```bash
pnpm run dc:up
```

Now you can use the Symfony CLI to start a development server:

```bash
pnpm run dev
```

This will serve the application at `http://localhost:8000` and run a TailwindCSS
worker to compile the styles. You can also run the TailwindCSS worker separately
by using:

```bash
php bin/console tailwind:build --watch
```

## Commit Guidelines

Make sure to run the formatting and spellcheck commands before committing your
changes:

```bash
pnpm run format
pnpm run spellcheck
```

Commit messages should follow the conventional commit format that is described
in the
[Conventional Commits specification](https://www.conventionalcommits.org/en/v1.0.0/).

### Configuration Word Spelling Errors

You might encounter spelling errors in the configuration files after installing
a new package. If the **only** errors are technical terms such as `javascripts`,
`phpstan`, or `healthcheck`, you can bulk add them to the `project-words.txt`
file by running:

```bash
pnpm run spellcheck:baseline
```

## Links

- [Database Schema](https://dbdiagram.io/d/StudiBonnTask-6883889bcca18e685cc0e682)
- [TailwindUI](https://tailwindcss.com/plus/ui-blocks/marketing)
- [Symfony User Authentication](https://symfony.com/doc/current/security.html#authenticating-users)
- [Symfony Notifications](https://symfony.com/doc/current/session.html#installation)
- [Symfony Relations](https://symfony.com/doc/current/doctrine/associations.html)
