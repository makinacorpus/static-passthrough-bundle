# Static Passthrough Bundle
## Easily serve a tree of folders and statics html files through Symfony

This bundle has been developped for a simple use case : serve a built documentation through a symfony application.

### Getting started

First get sources with composer:

```
composer require makinacorpus/static-passthrough-bundle
```

Then create a `statics` folder in the root directory of your application. Put a simple `test.html` file in it.

Configure the bundle in a `config/package/static_passthrough.yaml` file:

```
# config/package/static_passthrough.yaml
static_passthrough:
  definitions:
    docs:
      root_folder: 'statics' # Where to find files to passthrough (relative to %kernel.project_dir%)
      path_prefix: 'static/' # Root path to reach files through
```

Clear the cache:

```
bin/console c:c
```

Visit `example.com/dev/static/test.html`, you should see your HTML file appears.
