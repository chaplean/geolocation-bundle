Getting Started With ChapleanGeolocationBundle
==============================================

# Prerequisites

This version of the bundle requires Symfony 2.8+.

# Configuration

Import config in `app/config/config.yml`:
```yaml
imports:
    - { resource: @ChapleanGeolocationBundle/Resources/config/config.yml }
```

Add parameter `chaplean_geolocation.api_key` in `app/config/parameters.yml`

###### Note

Default api key for chaplean: `AIzaSyA4iQOHlCF5nqaEDKk-9IsYAMapdOvIATc`

For more information in 