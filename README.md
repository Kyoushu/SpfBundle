# Kyoushu/SpfBundle

A bundle for building pages utilising [SPF](http://youtube.github.io/spfjs/)

## Usage Example

Extend your controller with \Kyoushu\SpfBundle\Controller\SpfController

```php
/**
 * @Route("/", name="homepage")
 */
public function indexAction()
{
    return $this->renderSpf(
        '::index.html.twig',
        array('foo' => 'bar'),
        array(
            Fragment::create('title', 'title'), // Sets title to HTML from 'title' Twig block
            Fragment::create('content', 'body'), // Adds HTML from 'content' Twig block into body object
            Fragment::create('javascripts', 'foot'), // Adds HTML from 'foot' Twig block into foot string
            Fragment::create('foo', 'attr', 'bar') // Sets attribute 'foo' to static value 'bar'
        )
    );
}
```

### Response

When the controller method is invoked with a request containing spf=naviagte, spf=navigate-back or spf=navigate-forward in the query string, a JSON response containing HTML generated by the blocks "title", "content" and "javascripts" is returned. The attribute "foo" is defined statically.

```json
{
    "title": "Title from Twig template",
    "body": {
        "content": "HTML from content block in Twig template"
    },
    "attr": {
        "foo": "bar"
    },
    "foot": "<script src=\"script/from/javascripts/block/in/twig.js\"></script>"
}
```

## Config

```
kyoushu_spf:
    default_fragments: # Fragments defined here will always be added to navigate responses (if related Twig blocks are defined)
        - { name: "content", type: "body" }
        - { name: "title", type: "title" }
        - { name: "javascripts", type: "foot" }
        - { name: "stylesheets": type: "head" }
        - { name: "foo", type: "attr", value: "bar" }
```

## TO-DO

* Unit tests
* Add to Packagist when stable
* Install instructions
