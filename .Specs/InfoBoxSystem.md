# InfoBox System

This document specifies the internal "InfoBox" rendering system. It is a reusable pattern used across multiple backend sub-modules to display status-like information in a consistent format.

## Overview

The system is designed to decouple data fetching and rendering from the controllers. Instead of a controller fetching all data and passing it to a monolithic Fluid template, the responsibility is distributed among several smaller, specialized `InfoBox` classes.

The core components of this system are:

1.  **`Page` Service**: A DTO that acts as a container for a collection of InfoBoxes for a specific topic (e.g., "InnoDB").
2.  **`AbstractInfoBox`**: A base class that defines the contract for all InfoBox objects.
3.  **Concrete InfoBox Implementations**: Specific classes (e.g., `UptimeInfoBox`) that extend `AbstractInfoBox` and contain the logic to fetch and prepare data for a single panel.
4.  **Service Tagging**: The Symfony Dependency Injection container uses tags to collect all relevant InfoBox implementations and inject them into the correct `Page` service.

## Component Analysis

### 1. `Page` Service (`Classes/Menu/Page.php`)

-   A `readonly` DTO that receives an iterable of `AbstractInfoBox` objects via its constructor.
-   The `getRenderedInfoBoxes()` method iterates through all its InfoBoxes.
-   For each InfoBox, it calls the `updateView()` method, which returns a configured `ViewInterface` object.
-   It then calls `render()` on that view and concatenates the resulting HTML strings.
-   The final, combined HTML of all InfoBoxes is returned to the controller.

### 2. `AbstractInfoBox` (`Classes/InfoBox/AbstractInfoBox.php`)

-   Defines the basic structure of an InfoBox, including methods for rendering a title, body, and footer.
-   Crucially, it contains the `updateView()` method, where the concrete class assigns its fetched data to the view.
-   Many InfoBoxes use the `GetStatusValuesAndVariablesTrait` to easily access the `StatusRepository` and `VariablesRepository`.

### 3. Service Configuration (`Configuration/Services.yaml`)

This is where the system is wired together.

-   **InfoBox Tagging**: Concrete InfoBox classes are automatically tagged using the `#[AutoconfigureTag]` PHP attribute. For example, an InfoBox for the "Status" page will have `#[AutoconfigureTag('mysqlreport.infobox.status')]`.
-   **`Page` Service Definition**: For each sub-module that uses this pattern, a dedicated `Page` service is defined.
-   **`!tagged_iterator`**: The `arguments` of the `Page` service use the `!tagged_iterator` directive to collect all services with a specific tag. For example, the `mysqlreport.page.information` service receives all InfoBoxes tagged with `mysqlreport.infobox.status`.
-   **Controller Injection**: The corresponding controller (e.g., `StatusController`) then gets the correctly configured `Page` service injected.

## Workflow Example (`StatusController`)

1.  `StatusController` has `@mysqlreport.page.information` injected as `$this->page`.
2.  The `indexAction` calls `$this->page->getRenderedInfoBoxes()`.
3.  The `Page` object iterates through all InfoBoxes tagged with `mysqlreport.infobox.status` (e.g., `UptimeInfoBox`, `ConnectionInfoBox`).
4.  Each InfoBox fetches its data (e.g., from `StatusRepository`), prepares it, and renders its own small HTML partial (`Resources/Private/Templates/InfoBox/Default.html`).
5.  The controller receives a single string of pre-rendered HTML and assigns it to the main module template (`Resources/Private/Templates/Status/Index.html`).
