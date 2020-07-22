# Static Passthrough Bundle: easily serve a tree of folders and statics html files through Symfony

This bundle has been developped for a simple use case : serve a built documentation through a symfony application.

## Getting started

### 1/ Get sources with Composer:

```sh
composer require makinacorpus/static-passthrough-bundle
```

### 2/ Register the bundle

```php
# config/bundles.php
return [

    // ...

    MakinaCorpus\StaticPassthroughBundle\StaticPassthroughBundle::class => ['all' => true],

    // ...
];
```

### 3/ Add Static Passthrough Routes definition

```yaml
# config/routes.yaml

# ...

static_passthrough:
    resource: "@StaticPassthroughBundle/Resources/config/routes.php"

# ...

```

### 4/ Configure Static Passthrough

Let's assume we have a `statics` folder in the root directory of our application, with a simple `test.html` file in it:
that's the static file we want to serve through our Symfony app.

To do so, configure the bundle like this:

```yaml
# config/package/static_passthrough.yaml
static_passthrough:
  definitions:
    docs:
      root_folder: 'statics' # Where to find files to passthrough (relative to %kernel.project_dir%)
      path_prefix: 'statics/' # Root path to reach files in root_folder
```

Don't forget to clear the cache:

```sh
bin/console c:c
```

### 5/ Reach the file with your browser :

Visit `[app_basepath]/statics/test.html`, you should see your HTML file appears.
