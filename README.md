# Webtech Symfony

This repository contains the code for the project required as part of the
Webtech course BA-INF 162 Web Technologies at the University of Bonn.

## Installation

While not required, I would recommend installing the Symfony CLI tool to help
with the development process. You can find the installation instructions on the
[Symfony website](https://symfony.com/download).

After installing the Symfony CLI, follow the instructions in the Symfony setup
article, more specifically the section about
[technical requirements](https://symfony.com/doc/current/setup.html#technical-requirements).

Clone this repository and run the following commands to set up the project:

```bash
git clone https://github.com/lucaschultz/webtech-symfony
cd webtech-symfony/
composer install
pnpm install
```

if you are using VSCode, install the recommended extensions by opening the
`.vscode/extensions.json` file and clicking on the "Install" button.

## Running the Application

To run the application, you can use the Symfony CLI to start a local server:

```bash
symfony server:start --no-tls
```

This will serve the application at `http://localhost:8000` and run a TailwindCSS
worker to compile the styles. You can also run the TailwindCSS worker separately
by using:

```bash
php bin/console tailwind:build --watch
```
