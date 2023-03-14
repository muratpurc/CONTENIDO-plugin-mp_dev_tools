# CONTENIDO plugin "Mp Dev Tools"

## Description

Contains some helpful features for the development of CONTENIDO modules and plugins.

## Requirements

- CONTENIDO >= 4.10
- PHP >= 7.0

## Features

- Basic functionality to access request variables ($_GET, $_POST, $_REQUEST, etc.).
- Basic functionality for modules, both for module inputs and for module outputs.
- Basic functionality for plugins.
- Interface to some backend and frontend (client) information.
- Simplified generation of tables in the module configuration (module input).
- Simplified generation of expandable and collapsible tables in the module configuration (module input), as known from the "Article List Reloaded" and "Appointment List v3" (Terminliste v3) modules.
- Easy handling of CMS tokens (CMS_VAR and CMS_VALUE) in module inputs and outputs.
- Fully compatible with CONTENIDO >= 4.10.*.
- Fully compatible with PHP >= 7.0, also with PHP up to 8.2.
- In summary, an easier way to program modules and plugins for CONTENIDO CMS.

## Usage

1. Download release archive (zip)

2. Install it using the PIM (Plugin Manager) in CONTENIDO backend

3. Use it in your modules or plugins

## Module example

See also "_examples" folder in GitHub for a sample implementation of a module based on "Mp Dev Tools" features.
Install the module in your CONTENIDO project, create an article with a template where the module is configured and see some details in module input and module output.

## Plugin example

See the code of the plugin "Mp Dev Tools", it uses its own features.