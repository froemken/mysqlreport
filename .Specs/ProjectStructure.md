# Project Structure

This document outlines the directory and file structure for the `mysqlreport` TYPO3 extension. The AI assistant must adhere to these conventions.

## Root Directory

The root directory of the extension contains the following main folders and files:

- `Classes/`: Contains all PHP classes (Domain, Controller, Repositories, etc.).
- `Configuration/`: Holds all configuration files, including TCA, TypoScript, and services.
- `Documentation/`: Official extension documentation in reStructuredText format for the TYPO3 TER.
- `Resources/`: Contains all non-PHP files like Fluid templates, language files, and public assets.
- `Tests/`: Contains all PHPUnit tests.
- `.Specs/`: Contains specifications and documentation for the AI assistant (in Markdown format).
- `composer.json`: Composer manifest.
- `ext_emconf.php`: TYPO3 extension configuration file.

## Detailed Structure

### `Classes/`

All classes must follow PSR-12 and use the namespace `StefanFroemken\Mysqlreport`.

- `Classes/Controller/`: Extbase controllers.
- `Classes/Domain/Model/`: Extbase domain models.
- `Classes/Domain/Repository/`: Extbase repositories.
- `Classes/Utility/`: Utility classes.
- `Classes/Command/`: Symfony commands.

### `Configuration/`

- `Configuration/TCA/`: Contains Table Configuration Array (TCA) definitions.
    - `Configuration/TCA/tx_mysqlreport_domain_model_...`: TCA for a specific model.
    - `Configuration/TCA/Overrides/`: For extending existing TCA of other tables (e.g., `pages`, `tt_content`).
- `Configuration/TypoScript/`: TypoScript files.
    - `Configuration/TypoScript/constants.typoscript`
    - `Configuration/TypoScript/setup.typoscript`
- `Configuration/Extbase/`: Extbase framework configuration (e.g., `Persistence/Classes.php`).

### `Resources/`

- `Resources/Private/Language/`: Language files (`locallang.xlf`).
- `Resources/Private/Templates/`: Fluid templates for controllers.
- `Resources/Private/Partials/`: Fluid partials.
- `Resources/Private/Layouts/`: Fluid layouts.
- `Resources/Public/Icons/`: Publicly accessible icons.
- `Resources/Public/JavaScript/`: Publicly accessible JavaScript files.
- `Resources/Public/Css/`: Publicly accessible CSS files.
