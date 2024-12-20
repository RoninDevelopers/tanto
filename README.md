# Tanto CLI

Tanto is a PHP CLI tool designed to streamline the management of project scripts defined in `composer.json` and `package.json`. It scans your project, extracts script commands, and lets you run them easily from the CLI.

---

_A tantō a traditionally made Japanese knife that were worn by the samurai class of feudal Japan. The tantō dates to the Heian period, when it was mainly used as a weapon but evolved in design over the years to become more ornate. Tantō were used in traditional martial arts (tantojutsu). The term has seen a resurgence in modern times, with the rise of martial arts in Japan and the West._

---

## Features

- Automatically detects scripts in `composer.json` and `package.json`.
- Generates a `tanto.yml` configuration file with all detected commands.
- Provides an interactive menu to list and run available commands.
- Allows direct execution of a command by its name.

---

## Installation

### Step 1: Install the Package via Composer

To install Tanto, add it to your project using Composer:

```bash
composer require --dev ronindevelopers/tanto
```

Alternatively, you can install it globally:

```bash
composer global require ronindevelopers/tanto
```

### Step 2: Set Up the CLI

If installed locally, you can use the `bin/tanto` script:

```bash
./vendor/bin/tanto
```

For global installations, the `tanto` command will be available directly:

```bash
tanto
```

---

## Usage

### 1. Initialize the Configuration

Run the `tanto:init` command to scan your project and create the `tanto.yml` file:

```bash
./vendor/bin/tanto init
```

This will:

- Look for `composer.json` and `package.json` files.
- Extract all scripts and commands.
- Save them in a `tanto.yml` file in your project root.

Example `tanto.yml`:

```yaml
commands:
  - name: test
    command: npm test
    source: package.json
    description: ''
  - name: build
    command: composer build
    source: composer.json
    description: ''
```

### 2. List and Run Commands

To list all available commands and run them interactively, use the `tanto:run` command:

```bash
./vendor/bin/tanto run
```

This will display a numbered list of commands. Select one to execute it.

Example:

```bash
Available Commands:
[0] test (package.json)
[1] build (composer.json)

Select a command to run:
```

### 3. Run a Command Directly

You can run a command directly by passing its name as an argument to `tanto:run`:

```bash
./vendor/bin/tanto run test
```

If the command name matches one in `tanto.yml`, it will execute immediately.


### 4. Update the Configuration

Run the update `tanto:update` command to refresh `tanto.yml` with the latest scripts:

```bash
./vendor/bin/tanto update
```

This will scan `package.json` and `composer.json` for new commands.
Existing commands' descriptions will be preserved.
New commands will have a '' description by default, which you can manually edit later in `tanto.yml`.

---

### Contributing

We welcome contributions! Please fork the repository, make your changes, and submit a pull request.

---

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
