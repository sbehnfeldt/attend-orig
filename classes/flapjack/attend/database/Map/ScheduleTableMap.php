<?php

namespace flapjack\attend\database\Map;

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;
use flapjack\attend\database\Schedule;
use flapjack\attend\database\ScheduleQuery;


/**
 * This class defines the structure of the 'schedules' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 */
class ScheduleTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    public const CLASS_NAME = '.Map.ScheduleTableMap';

    /**
     * The default database name for this class
     */
    public const DATABASE_NAME = 'attend';

    /**
     * The table name for this class
     */
    public const TABLE_NAME = 'schedules';

    /**
     * The PHP name of this class (PascalCase)
     */
    public const TABLE_PHP_NAME = 'Schedule';

    /**
     * The related Propel class for this table
     */
    public const OM_CLASS = '\\flapjack\\attend\\database\\Schedule';

    /**
     * A class that can be returned by this tableMap
     */
    public const CLASS_DEFAULT = 'Schedule';

    /**
     * The total number of columns
     */
    public const NUM_COLUMNS = 5;

    /**
     * The number of lazy-loaded columns
     */
    public const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    public const NUM_HYDRATE_COLUMNS = 5;

    /**
     * the column name for the id field
     */
    public const COL_ID = 'schedules.id';

    /**
     * the column name for the student_id field
     */
    public const COL_STUDENT_ID = 'schedules.student_id';

    /**
     * the column name for the schedule field
     */
    public const COL_SCHEDULE = 'schedules.schedule';

    /**
     * the column name for the start_date field
     */
    public const COL_START_DATE = 'schedules.start_date';

    /**
     * the column name for the entered_at field
     */
    public const COL_ENTERED_AT = 'schedules.entered_at';

    /**
     * The default string format for model objects of the related table
     */
    public const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     *
     * @var array<string, mixed>
     */
    protected static $fieldNames = [
        self::TYPE_PHPNAME       => ['Id', 'StudentId', 'Schedule', 'StartDate', 'EnteredAt', ],
        self::TYPE_CAMELNAME     => ['id', 'studentId', 'schedule', 'startDate', 'enteredAt', ],
        self::TYPE_COLNAME       => [ScheduleTableMap::COL_ID, ScheduleTableMap::COL_STUDENT_ID, ScheduleTableMap::COL_SCHEDULE, ScheduleTableMap::COL_START_DATE, ScheduleTableMap::COL_ENTERED_AT, ],
        self::TYPE_FIELDNAME     => ['id', 'student_id', 'schedule', 'start_date', 'entered_at', ],
        self::TYPE_NUM           => [0, 1, 2, 3, 4, ]
    ];

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     *
     * @var array<string, mixed>
     */
    protected static $fieldKeys = [
        self::TYPE_PHPNAME       => ['Id' => 0, 'StudentId' => 1, 'Schedule' => 2, 'StartDate' => 3, 'EnteredAt' => 4, ],
        self::TYPE_CAMELNAME     => ['id' => 0, 'studentId' => 1, 'schedule' => 2, 'startDate' => 3, 'enteredAt' => 4, ],
        self::TYPE_COLNAME       => [ScheduleTableMap::COL_ID => 0, ScheduleTableMap::COL_STUDENT_ID => 1, ScheduleTableMap::COL_SCHEDULE => 2, ScheduleTableMap::COL_START_DATE => 3, ScheduleTableMap::COL_ENTERED_AT => 4, ],
        self::TYPE_FIELDNAME     => ['id' => 0, 'student_id' => 1, 'schedule' => 2, 'start_date' => 3, 'entered_at' => 4, ],
        self::TYPE_NUM           => [0, 1, 2, 3, 4, ]
    ];

    /**
     * Holds a list of column names and their normalized version.
     *
     * @var array<string>
     */
    protected $normalizedColumnNameMap = [
        'Id' => 'ID',
        'Schedule.Id' => 'ID',
        'id' => 'ID',
        'schedule.id' => 'ID',
        'ScheduleTableMap::COL_ID' => 'ID',
        'COL_ID' => 'ID',
        'schedules.id' => 'ID',
        'StudentId' => 'STUDENT_ID',
        'Schedule.StudentId' => 'STUDENT_ID',
        'studentId' => 'STUDENT_ID',
        'schedule.studentId' => 'STUDENT_ID',
        'ScheduleTableMap::COL_STUDENT_ID' => 'STUDENT_ID',
        'COL_STUDENT_ID' => 'STUDENT_ID',
        'student_id' => 'STUDENT_ID',
        'schedules.student_id' => 'STUDENT_ID',
        'Schedule' => 'SCHEDULE',
        'Schedule.Schedule' => 'SCHEDULE',
        'schedule' => 'SCHEDULE',
        'schedule.schedule' => 'SCHEDULE',
        'ScheduleTableMap::COL_SCHEDULE' => 'SCHEDULE',
        'COL_SCHEDULE' => 'SCHEDULE',
        'schedules.schedule' => 'SCHEDULE',
        'StartDate' => 'START_DATE',
        'Schedule.StartDate' => 'START_DATE',
        'startDate' => 'START_DATE',
        'schedule.startDate' => 'START_DATE',
        'ScheduleTableMap::COL_START_DATE' => 'START_DATE',
        'COL_START_DATE' => 'START_DATE',
        'start_date' => 'START_DATE',
        'schedules.start_date' => 'START_DATE',
        'EnteredAt' => 'ENTERED_AT',
        'Schedule.EnteredAt' => 'ENTERED_AT',
        'enteredAt' => 'ENTERED_AT',
        'schedule.enteredAt' => 'ENTERED_AT',
        'ScheduleTableMap::COL_ENTERED_AT' => 'ENTERED_AT',
        'COL_ENTERED_AT' => 'ENTERED_AT',
        'entered_at' => 'ENTERED_AT',
        'schedules.entered_at' => 'ENTERED_AT',
    ];

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function initialize(): void
    {
        // attributes
        $this->setName('schedules');
        $this->setPhpName('Schedule');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\flapjack\\attend\\database\\Schedule');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('student_id', 'StudentId', 'INTEGER', 'students', 'id', true, null, null);
        $this->addColumn('schedule', 'Schedule', 'INTEGER', true, null, 0);
        $this->addColumn('start_date', 'StartDate', 'DATE', true, null, null);
        $this->addColumn('entered_at', 'EnteredAt', 'INTEGER', true, null, 0);
    }

    /**
     * Build the RelationMap objects for this table relationships
     *
     * @return void
     */
    public function buildRelations(): void
    {
        $this->addRelation('Student', '\\flapjack\\attend\\database\\Student', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':student_id',
    1 => ':id',
  ),
), null, null, null, false);
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array $row Resultset row.
     * @param int $offset The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string|null The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow(array $row, int $offset = 0, string $indexType = TableMap::TYPE_NUM): ?string
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return null === $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array $row Resultset row.
     * @param int $offset The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow(array $row, int $offset = 0, string $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param bool $withPrefix Whether to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass(bool $withPrefix = true): string
    {
        return $withPrefix ? ScheduleTableMap::CLASS_DEFAULT : ScheduleTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array $row Row returned by DataFetcher->fetch().
     * @param int $offset The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array (Schedule object, last column rank)
     */
    public static function populateObject(array $row, int $offset = 0, string $indexType = TableMap::TYPE_NUM): array
    {
        $key = ScheduleTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ScheduleTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ScheduleTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ScheduleTableMap::OM_CLASS;
            /** @var Schedule $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ScheduleTableMap::addInstanceToPool($obj, $key);
        }

        return [$obj, $col];
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array<object>
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher): array
    {
        $results = [];

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = ScheduleTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ScheduleTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Schedule $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ScheduleTableMap::addInstanceToPool($obj, $key);
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
     * @param Criteria $criteria Object containing the columns to add.
     * @param string|null $alias Optional table alias
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return void
     */
    public static function addSelectColumns(Criteria $criteria, ?string $alias = null): void
    {
        if (null === $alias) {
            $criteria->addSelectColumn(ScheduleTableMap::COL_ID);
            $criteria->addSelectColumn(ScheduleTableMap::COL_STUDENT_ID);
            $criteria->addSelectColumn(ScheduleTableMap::COL_SCHEDULE);
            $criteria->addSelectColumn(ScheduleTableMap::COL_START_DATE);
            $criteria->addSelectColumn(ScheduleTableMap::COL_ENTERED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.student_id');
            $criteria->addSelectColumn($alias . '.schedule');
            $criteria->addSelectColumn($alias . '.start_date');
            $criteria->addSelectColumn($alias . '.entered_at');
        }
    }

    /**
     * Remove all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be removed as they are only loaded on demand.
     *
     * @param Criteria $criteria Object containing the columns to remove.
     * @param string|null $alias Optional table alias
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return void
     */
    public static function removeSelectColumns(Criteria $criteria, ?string $alias = null): void
    {
        if (null === $alias) {
            $criteria->removeSelectColumn(ScheduleTableMap::COL_ID);
            $criteria->removeSelectColumn(ScheduleTableMap::COL_STUDENT_ID);
            $criteria->removeSelectColumn(ScheduleTableMap::COL_SCHEDULE);
            $criteria->removeSelectColumn(ScheduleTableMap::COL_START_DATE);
            $criteria->removeSelectColumn(ScheduleTableMap::COL_ENTERED_AT);
        } else {
            $criteria->removeSelectColumn($alias . '.id');
            $criteria->removeSelectColumn($alias . '.student_id');
            $criteria->removeSelectColumn($alias . '.schedule');
            $criteria->removeSelectColumn($alias . '.start_date');
            $criteria->removeSelectColumn($alias . '.entered_at');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap(): TableMap
    {
        return Propel::getServiceContainer()->getDatabaseMap(ScheduleTableMap::DATABASE_NAME)->getTable(ScheduleTableMap::TABLE_NAME);
    }

    /**
     * Performs a DELETE on the database, given a Schedule or Criteria object OR a primary key value.
     *
     * @param mixed $values Criteria or Schedule object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ?ConnectionInterface $con = null): int
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ScheduleTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \flapjack\attend\database\Schedule) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ScheduleTableMap::DATABASE_NAME);
            $criteria->add(ScheduleTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = ScheduleQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            ScheduleTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                ScheduleTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the schedules table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(?ConnectionInterface $con = null): int
    {
        return ScheduleQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Schedule or Criteria object.
     *
     * @param mixed $criteria Criteria or Schedule object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed The new primary key.
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ?ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ScheduleTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Schedule object
        }

        if ($criteria->containsKey(ScheduleTableMap::COL_ID) && $criteria->keyContainsValue(ScheduleTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ScheduleTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = ScheduleQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

}
