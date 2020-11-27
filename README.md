# instalation
- run in console
```
composer require kematjaya/import-bundle
```
# config
- add to bundles.php
```
// config/bundles.php
....
Kematjaya\ImportBundle\ImportBundle::class => ['all' => true]
....
```
# using service
- on controller
```
...
use Kematjaya\ImportBundle\Manager\ImportManagerInterface;
...

public function import(ImportManagerInterface $importManager)
{
  $source = new YourDataSource();
  $transformer = new YourTransformer();
  $manager->->process($source, $transformer);
}
