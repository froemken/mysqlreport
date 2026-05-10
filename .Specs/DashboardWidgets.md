# Dashboard Widgets

This document specifies the integration of the `mysqlreport` extension with the TYPO3 Dashboard. It covers the conditional registration of widgets, the data provision, and the creation of a user-friendly preset.

## Conditional Registration (`DashboardPass`)

To ensure the extension works even if `typo3/cms-dashboard` is not installed, a custom compiler pass is used.

-   **Location**: `Configuration/Services.php`
-   **Class**: `StefanFroemken\Mysqlreport\DependencyInjection\Compiler\DashboardPass`
-   **Logic**: This pass checks if the `Dashboard` class exists. If not, it iterates through all service definitions and removes any that are tagged with `dashboard.widget`. This prevents the Dependency Injection container from failing due to missing dependencies.
-   **Import**: The main `Services.yaml` only imports `Backend/DashboardWidgets.yaml` if the `dashboard` extension is loaded, providing a first layer of separation.

## Widget Definitions (`DashboardWidgets.yaml`)

All widgets are defined in `Configuration/Backend/DashboardWidgets.yaml`. They share common traits:
-   They are instances of standard TYPO3 Chart Widgets (`DoughnutChartWidget`, `BarChartWidget`).
-   Each widget receives a dedicated Data Provider via constructor injection.
-   All widgets are assigned to the `mysqlreport` group via the `groupNames` tag attribute.

### Widgets and Data Providers:

1.  **Max Used Connections** (`mysql-max-used-connections`)
    -   **Provider**: `MaxUsedConnectionsDataProvider`
    -   **Type**: Doughnut Chart
2.  **InnoDB Buffer** (`mysql-inno-db-buffer`)
    -   **Provider**: `InnoDbBufferDataProvider`
    -   **Type**: Doughnut Chart
3.  **Query Types** (`mysql-query-types`)
    -   **Provider**: `QueryTypesDataProvider`
    -   **Type**: Bar Chart
4.  **Created Temp Tables** (`mysql-created-temp-tables`)
    -   **Provider**: `CreatedTempTablesDataProvider`
    -   **Type**: Doughnut Chart
5.  **Statistic: FTS** (`mysql-handler-read-next`)
    -   **Provider**: `HandlerReadNextDataProvider`
    -   **Type**: Doughnut Chart

## Data Providers (`Classes/Dashboard/Provider/`)

All data providers are `readonly` classes that implement `ChartDataProviderInterface`. They follow a consistent pattern:

-   **Dependencies**: They inject `StatusRepository` and/or `VariablesRepository`.
-   **Data Fetching**: The `findAll()` methods of the repositories are called in the constructor to fetch the required data once.
-   **`getChartData()`**: This method transforms the raw status and variable data into the specific array structure required by the chart widgets (labels, datasets, colors). All calculations and data mapping happen here.

## Grouping and Presets

To simplify user experience, the widgets are bundled.

1.  **Widget Group (`DashboardWidgetGroups.php`)**:
    -   A group with the identifier `mysqlreport` is registered.
    -   This allows all related widgets to be grouped together in the "Add Widget" wizard.

2.  **Dashboard Preset (`DashboardPresets.php`)**:
    -   A preset with the identifier `mysqlreport` is registered.
    -   It includes the identifiers of all 5 widgets in its `defaultWidgets` list.
    -   By setting `showInWizard` to `true`, users can add all `mysqlreport` widgets to their dashboard with a single click by selecting the "MySQL Report" preset.
