<?php

namespace Attend\Database\Map;

use Attend\Database\Attendance;
use Attend\Database\AttendanceQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'attendance' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class AttendanceTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = '.Map.AttendanceTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'attend';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'attendance';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Attend\\Database\\Attendance';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Attendance';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 4;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 4;

    /**
     * the column name for the id field
     */
    const COL_ID = 'attendance.id';

    /**
     * the column name for the student_id field
     */
    const COL_STUDENT_ID = 'attendance.student_id';

    /**
     * the column name for the check_in field
     */
    const COL_CHECK_IN = 'attendance.check_in';

    /**
     * the column name for the check_out field
     */
    const COL_CHECK_OUT = 'attendance.check_out';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array(
        self::TYPE_PHPNAME   => array('Id', 'StudentId', 'CheckIn', 'CheckOut',),
        self::TYPE_CAMELNAME => array('id', 'studentId', 'checkIn', 'checkOut',),
        self::TYPE_COLNAME   => array(
            AttendanceTableMap::COL_ID,
            AttendanceTableMap::COL_STUDENT_ID,
            AttendanceTableMap::COL_CHECK_IN,
            AttendanceTableMap::COL_CHECK_OUT,
        ),
        self::TYPE_FIELDNAME => array('id', 'student_id', 'check_in', 'check_out',),
        self::TYPE_NUM       => array(0, 1, 2, 3,)
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array(
        self::TYPE_PHPNAME   => array('Id' => 0, 'StudentId' => 1, 'CheckIn' => 2, 'CheckOut' => 3,),
        self::TYPE_CAMELNAME => array('id' => 0, 'studentId' => 1, 'checkIn' => 2, 'checkOut' => 3,),
        self::TYPE_COLNAME   => array(
            AttendanceTableMap::COL_ID         => 0,
            AttendanceTableMap::COL_STUDENT_ID => 1,
            AttendanceTableMap::COL_CHECK_IN   => 2,
            AttendanceTableMap::COL_CHECK_OUT  => 3,
        ),
        self::TYPE_FIELDNAME => array('id' => 0, 'student_id' => 1, 'check_in' => 2, 'check_out' => 3,),
        self::TYPE_NUM       => array(0, 1, 2, 3,)
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('attendance');
        $this->setPhpName('Attendance');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\Attend\\Database\\Attendance');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('student_id', 'StudentId', 'INTEGER', 'students', 'id', true, 10, null);
        $this->addColumn('check_in', 'CheckIn', 'INTEGER', false, null, null);
        $this->addColumn('check_out', 'CheckOut', 'INTEGER', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Student', '\\Attend\\Database\\Student', RelationMap::MANY_TO_ONE, array(
            0 =>
                array(
                    0 => ':student_id',
                    1 => ':id',
                ),
        ), 'CASCADE', null, null, false);
    } // buildRelations()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array $row resultset row.
     * @param int $offset The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[ TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id',
                TableMap::TYPE_PHPNAME, $indexType) ] === null
        ) {
            return null;
        }

        return null === $row[ TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id',
            TableMap::TYPE_PHPNAME,
            $indexType) ] || is_scalar($row[ TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id',
            TableMap::TYPE_PHPNAME, $indexType) ]) || is_callable([
            $row[ TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id',
                TableMap::TYPE_PHPNAME, $indexType) ],
            '__toString'
        ]) ? (string)$row[ TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id',
            TableMap::TYPE_PHPNAME,
            $indexType) ] : $row[ TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id',
            TableMap::TYPE_PHPNAME, $indexType) ];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array $row resultset row.
     * @param int $offset The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int)$row[ $indexType == TableMap::TYPE_NUM
            ? 0 + $offset
            : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType) ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? AttendanceTableMap::CLASS_DEFAULT : AttendanceTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array $row row returned by DataFetcher->fetch().
     * @param int $offset The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
     * One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (Attendance object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = AttendanceTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = AttendanceTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + AttendanceTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = AttendanceTableMap::OM_CLASS;
            /** @var Attendance $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            AttendanceTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = AttendanceTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = AttendanceTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Attendance $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                AttendanceTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string $alias optional table alias
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(AttendanceTableMap::COL_ID);
            $criteria->addSelectColumn(AttendanceTableMap::COL_STUDENT_ID);
            $criteria->addSelectColumn(AttendanceTableMap::COL_CHECK_IN);
            $criteria->addSelectColumn(AttendanceTableMap::COL_CHECK_OUT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.student_id');
            $criteria->addSelectColumn($alias . '.check_in');
            $criteria->addSelectColumn($alias . '.check_out');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(AttendanceTableMap::DATABASE_NAME)->getTable(AttendanceTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(AttendanceTableMap::DATABASE_NAME);
        if ( ! $dbMap->hasTable(AttendanceTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new AttendanceTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a Attendance or Criteria object OR a primary key value.
     *
     * @param mixed $values Criteria or Attendance object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     *
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doDelete($values, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AttendanceTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Attend\Database\Attendance) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(AttendanceTableMap::DATABASE_NAME);
            $criteria->add(AttendanceTableMap::COL_ID, (array)$values, Criteria::IN);
        }

        $query = AttendanceQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            AttendanceTableMap::clearInstancePool();
        } elseif ( ! is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array)$values as $singleval) {
                AttendanceTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the attendance table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return AttendanceQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Attendance or Criteria object.
     *
     * @param mixed $criteria Criteria or Attendance object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     *
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AttendanceTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Attendance object
        }

        if ($criteria->containsKey(AttendanceTableMap::COL_ID) && $criteria->keyContainsValue(AttendanceTableMap::COL_ID)) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . AttendanceTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = AttendanceQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // AttendanceTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
AttendanceTableMap::buildTableMap();
