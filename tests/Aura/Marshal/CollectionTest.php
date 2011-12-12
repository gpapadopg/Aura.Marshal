<?php
namespace Aura\Marshal;
use Aura\Marshal\Collection\GenericCollection;
use Aura\Marshal\Type\GenericType;
use Aura\Marshal\Record\Builder as RecordBuilder;
use Aura\Marshal\Collection\Builder as CollectionBuilder;

/**
 * Test class for Collection.
 * Generated by PHPUnit on 2011-11-26 at 16:38:42.
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    protected $collection;
    
    protected $empty_collection;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $type = new GenericType;
        $type->setIdentityField('id');
        
        $ids = array(1, 2, 3, 5, 7, 11, 13);
        $names = array('foo', 'bar', 'baz', 'dib', 'zim', 'gir', 'irk');
        $data = array();
        foreach ($names as $key => $name) {
            $data[] = (object) array(
                'id' => $ids[$key],
                'name' => $name
            );
        }
        
        $this->collection = new GenericCollection($data, $type);
        $this->empty_collection = new GenericCollection(array(), $type);
    }
    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testGetIdentityValues()
    {
        $expect = array(1, 2, 3, 5, 7, 11, 13);
        $actual = $this->collection->getIdentityValues();
        $this->assertSame($expect, $actual);
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->empty_collection->isEmpty());
        $this->assertFalse($this->collection->isEmpty());
    }
    
    public function testObjectsInCollectionAreInIdentityMap()
    {
        $type = new GenericType;
        $type->setIdentityField('id');
        $type->setRecordClass('Aura\Marshal\Record\GenericRecord');
        $type->setRecordBuilder(new RecordBuilder);
        $type->setCollectionBuilder(new CollectionBuilder);
        
        $ids = array(1, 2, 3, 5, 7, 11, 13);
        $names = array('foo', 'bar', 'baz', 'dib', 'zim', 'gir', 'irk');
        $data = array();
        foreach ($names as $key => $name) {
            $data[] = array(
                'id' => $ids[$key],
                'name' => $name
            );
        }
        
        $type->load($data);
        
        // get a collection of all the IDs from the type *before* creating
        // any record objects.
        $collection = $type->getCollection($ids);
        
        // get a record by ID from the type and change it.
        // note that getRecord() is by identity value, not offset.
        $expect = $type->getRecord(1);
        $expect->name = 'changed';
        
        // now get what should be the same record from the collection.
        // it should be changed as well.
        // note that collection is by offset, not identity value.
        $actual = $collection[0];
        $this->assertSame($expect->name, $actual->name);
    }
}