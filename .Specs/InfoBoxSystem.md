# InfoBox System

This document specifies the internal "InfoBox" rendering system. It is a reusable pattern used across multiple backend sub-modules to display status-like information in a consistent format.

## Overview

The system is designed to decouple database data fetching and rendering from the controllers. Instead of a controller fetching all data and passing it to a monolithic Fluid template, the responsibility is distributed among several smaller, specialized `InfoBox` classes.

The core components of this system are:

1.  **`InfoBoxInterface`**: The interface (`Classes/InfoBox/InfoBoxInterface.php`) defining the contract for all InfoBoxes. It requires a `TITLE` constant and a `getBody(): string` method.
2.  **Concrete InfoBox Implementations**: Specific `final readonly` classes (e.g., `UptimeInfoBox`) that implement `InfoBoxInterface` (and optional interfaces like `InfoBoxUnorderedListInterface` or `InfoBoxStateInterface`) and contain the logic to calculate and format data.
3.  **`RenderInfoBoxFactory`**: A stateless rendering service (`Classes/InfoBox/RenderInfoBoxFactory.php`) that maps the structured InfoBox properties to a Fluid view and generates the final HTML output.
4.  **Service Tagging**: InfoBox implementations are tagged with their respective topic tags (e.g., `mysqlreport.infobox.status`) using the PHP attribute `#[AutoconfigureTag]`.
5.  **Constructor Autowiring**: The DI container instantiates the models `StatusValues` and `Variables` using repositories and caches them. They are autowired directly to the InfoBox constructors via the global `bind` configuration in `Services.yaml`.

## Component Analysis

### 1. `InfoBoxInterface` (`Classes/InfoBox/InfoBoxInterface.php`)

-   Defines the contract:
    -   `public const TITLE = '';`
    -   `public function getBody(): string;`

### 2. `RenderInfoBoxFactory` (`Classes/InfoBox/RenderInfoBoxFactory.php`)

-   Takes the `ViewFactoryInterface` in the constructor.
-   The `render(iterable $infoBoxes): string` method iterates over the InfoBoxes, extracts their structured data, assigns them to a Fluid view, and compiles the final HTML.
-   It keeps the InfoBox classes completely independent of the view layer (fully headless-ready).

### 3. Service Configuration (`Configuration/Services.yaml`)

This is where the system is wired together.

-   **Factories**: `StatusValues` and `Variables` are registered as factory services calling the `findAll` repository methods.
-   **Autowiring Bindings**: The types are globally bound to the factory services under `_defaults.bind`.
-   **Direct Controller Injection**: The controller (e.g., `StatusController`) is configured to receive the tagged iterator of InfoBoxes directly via `!tagged_iterator`.

## Workflow Example (`StatusController`)

1.  `StatusController` has `iterable $infoBoxes` (tagged with `mysqlreport.infobox.status`) and the `RenderInfoBoxFactory` injected via constructor autowiring.
2.  The `indexAction` calls `$this->renderInfoBoxFactory->render($this->infoBoxes)`.
3.  Inside the factory, each InfoBox's `TITLE` constant and `getBody()` content are extracted and assigned to `Resources/Private/Templates/InfoBox/Default.html`.
4.  The controller receives a single string of pre-rendered HTML and assigns it to the main module template (`Resources/Private/Templates/Status/Index.html`).
