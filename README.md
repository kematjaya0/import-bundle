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
```
# data source
```
...
use Kematjaya\ImportBundle\DataSource\RemoteDataSource;
use Kematjaya\ImportBundle\DataSource\SpreadSheetDataSource;
...
...
$remoteSource = new RemoteDataSource('https://jsonplaceholder.typicode.com/posts'); // from remote source
$excelSource = new SpreadSheetDataSource('D://test.xlsx'); // from excel file
...
```
# data transformer
- create data transformer class
```
// src/DataTransformer/PostDataTransformer
...
use App\Entity\Post;
use Kematjaya\ImportBundle\DataTransformer\AbstractDataTransformer;
...

class PostDataTrasnformer extends AbstractDataTransformer
{
  public function fromArray(array $data) 
    {
        $datas = $this->checkConstraints($data);
        $entity = (new Post())
                ->setId($datas['id'])
                ->setUserId($datas['userId'])
                ->setTitle($datas['title'])
                ->setBody($datas['body']);
        
        return $entity;
    }

    protected function getColumns(): array 
    {
        return [
            [
                self::KEY_FIELD => 'id', // key in TargetObject
                self::KEY_INDEX => 0,    // key in array source
                self::KEY_CONSTRAINT => [
                    self::CONSTRAINT_REQUIRED => true // for required data
                    self::CONSTRAINT_REFERENCE_CLASS => User::class // relation class
                    self::CONSTRAINT_REFERENCE_FIELD => 'code' // field in reference class
                ]
            ]
        ];
    }
}
```
