# Static Passthrough Bundle: easily serve a tree of statics files through Symfony

This bundle has been developed for a simple use case : serve static files (e.g. generated documentation) through a Symfony application.

## Getting started

### 1/ Install with Composer:

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

Let's assume we have a `docs` folder in the root directory of our application, with a simple `test.html` file in it:
that's the static file we want to serve through our Symfony app.

To do so, configure the bundle like this:

```yaml
# config/package/static_passthrough.yaml
static_passthrough:
  definitions:
    docs: # Route name will be 'static_passthrough_docs'
      root_folder: 'docs' # Where to find files to passthrough (this path has to be relative to %kernel.project_dir%)
      path_prefix: 'docs/' # Path to reach files in root_folder
```

Don't forget to clear the cache:

```sh
bin/console c:c
```

### 5/ Reach the file with your browser :

Visit `[app_basepath]/docs/test.html`, you should see your HTML file appear.

Note that you could also view it visiting `[app_basepath]/docs/test`,
in fact when you try to visit `[app_basepath]/docs/test`, the bundle will look for a file in these different paths, in that order:

* `[app_basepath]/docs/test`
* `[app_basepath]/docs/test.html`
* `[app_basepath]/docs/test/index.html`

### 6/ Generate URL

Here is an examples to create an URL to reach 'test.html' file described above:

From a controller:

```php
$this->generateUrl(
    'static_passthrough_docs',
    ['path' => 'test.html]
);
```

From twig:

```twig
{{ path('static_passthrough_docs', {'path': 'test.html'}) }}
```
