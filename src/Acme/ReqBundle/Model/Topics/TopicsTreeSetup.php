<?php

namespace Acme\ReqBundle\Model\Topics;

use Acme\ReqBundle\Model\Topics\TopicsGetterWrapper;
use Exception;

/**
 * @author Andrea Fiori
 * @since  18 October 2014
 */
class TopicsTreeSetup
{
    private $topicsGetterWrapper;
    
    private $records;
    private $recordsIndex = array();
    private $recordsTree = array();
        
    /**
     * @param TopicsGetterWrapper $topicsGetterWrapper
     */
    public function setTopicsGetterWrapper(TopicsGetterWrapper $topicsGetterWrapper)
    {
        $this->topicsGetterWrapper = $topicsGetterWrapper;
    }
    
    public function getTopicsGetterWrapper()
    {
        return $this->topicsGetterWrapper;
    }
    
    /**
     * @throws Exception
     */
    private function assertTopicsGetterWrapper()
    {
        if (!$this->topicsGetterWrapper) {
            throw new Exception("TopicsGetterWrapper is not set");
        }
    }

    /**
     * Set topics records
     * 
     * @param array $input
     * @return TopicsGetterWrapper
     */
    public function setupTopicsFromDb($input = array())
    {
        $this->assertTopicsGetterWrapper();
        
        $this->topicsGetterWrapper->setInput($input);
        $this->topicsGetterWrapper->setupQueryBuilder();

        $this->records = $this->topicsGetterWrapper->getRecords();

        return $this->records;
    }
    
    /**
     * Set record indexes and tree array with all data
     * 
     * @return array $this->recordsTree
     */
    public function setupRecordsTree()
    {
        $records = $this->getRecords();
        if ($records) {
            foreach($records as $row) {
                
                $id = isset($row['id']) ? $row['id'] : '';
                $parent_id = $row['parentId'] == 0 ? "NULL" : $row['parentId'];
                
                $this->recordsTree[$id] = $row;
                
                $this->recordsIndex[$parent_id][] = $id;
            }
        }
        
        return $this->recordsTree;
    }
    
    /**
     * @param array $records
     * @return array $this->records
     */
    public function setRecords(array $records)
    {
        $this->records = $records;
        
        return $this->records;
    }
    
    /**
     * @return array|null
     */
    public function getRecords()
    {
        return $this->records;
    }
    
    /**
     * @return array|null
     */
    public function getRecordsIndex()
    {
        return $this->recordsIndex;
    }
    
    /**
     * @return array|null
     */
    public function getRecordsTree()
    {
        return $this->recordsTree;
    }
}