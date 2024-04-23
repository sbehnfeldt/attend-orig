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
use flapjack\attend\database\Student;
use flapjack\attend\database\StudentQuery;


/**
 * This class defines the structure of the 'students' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 */
class StudentTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    public const CLASS_NAME = '.Map.StudentTableMap';

    /**
     * The default database name for this class
     */
    public const DATABASE_NAME = 'attend';

    /**
     * The table name for this class
     */
    public const TABLE_NAME = 'students';

    /**
     * The PHP name of this class (PascalCase)
     */
    public const TABLE_PHP_NAME = 'Student';

    /**
     * The related Propel class for this table
     */
    public const OM_CLASS = '\\flapjack\\attend\\database\\Student';

    /**
     * A class that can be returned by this tableMap
     */
    public const CLASS_DEFAULT = 'Student';

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
    public const COL_ID = 'students.id';

    /**
     * the column name for the family_name field
     */
    public const COL_FAMILY_NAME = 'students.family_name';

    /**
     * the column name for the first_name field
     */
    public const COL_FIRST_NAME = 'students.first_name';

    /**
     * the column name for the enrolled field
     */
    public const COL_ENROLLED = 'students.enrolled';

    /**
     * the column name for the classroom_id field
     */
    public const COL_CLASSROOM_ID = 'students.classroom_id';

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
        self::TYPE_PHPNAME       => ['Id', 'FamilyName', 'FirstName', 'Enrolled', 'ClassroomId', ],
        self::TYPE_CAMELNAME     => ['id', 'familyName', 'firstName', 'enrolled', 'classroomId', ],
        self::TYPE_COLNAME       => [StudentTableMap::COL_ID, StudentTableMap::COL_FAMILY_NAME, StudentTableMap::COL_FIRST_NAME, StudentTableMap::COL_ENROLLED, StudentTableMap::COL_CLASSROOM_ID, ],
        self::TYPE_FIELDNAME     => ['id', 'family_name', 'first_name', 'enrolled', 'classroom_id', ],
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
        self::TYPE_PHPNAME       => ['Id' => 0, 'FamilyName' => 1, 'FirstName' => 2, 'Enrolled' => 3, 'ClassroomId' => 4, ],
        self::TYPE_CAMELNAME     => ['id' => 0, 'familyName' => 1, 'firstName' => 2, 'enrolled' => 3, 'classroomId' => 4, ],
        self::TYPE_COLNAME       => [StudentTableMap::COL_ID => 0, StudentTableMap::COL_FAMILY_NAME => 1, StudentTableMap::COL_FIRST_NAME => 2, StudentTableMap::COL_ENROLLED => 3, StudentTableMap::COL_CLASSROOM_ID => 4, ],
        self::TYPE_FIELDNAME     => ['id' => 0, 'family_name' => 1, 'first_name' => 2, 'enrolled' => 3, 'classroom_id' => 4, ],
        self::TYPE_NUM           => [0, 1, 2, 3, 4, ]
    ];

    /**
     * Holds a list of column names and their normalized version.
     *
     * @var array<string>
     */
    protected $normalizedColumnNameMap = [
        'Id' => 'ID',
        'Student.Id' => 'ID',
        'id' => 'ID',
        'student.id' => 'ID',
        'StudentTableMap::COL_ID' => 'ID',
        'COL_ID' => 'ID',
        'students.id' => 'ID',
        'FamilyName' => 'FAMILY_NAME',
        'Student.FamilyName' => 'FAMILY_NAME',
        'familyName' => 'FAMILY_NAME',
        'student.familyName' => 'FAMILY_NAME',
        'StudentTableMap::COL_FAMILY_NAME' => 'FAMILY_NAME',
        'COL_FAMILY_NAME' => 'FAMILY_NAME',
        'family_name' => 'FAMILY_NAME',
        'students.family_name' => 'FAMILY_NAME',
        'FirstName' => 'FIRST_NAME',
        'Student.FirstName' => 'FIRST_NAME',
        'firstName' => 'FIRST_NAME',
        'student.firstName' => 'FIRST_NAME',
        'StudentTableMap::COL_FIRST_NAME' => 'FIRST_NAME',
        'COL_FIRST_NAME' => 'FIRST_NAME',
        'first_name' => 'FIRST_NAME',
        'students.first_name' => 'FIRST_NAME',
        'Enrolled' => 'ENROLLED',
        'Student.Enrolled' => 'ENROLLED',
        'enrolled' => 'ENROLLED',
        'student.enrolled' => 'ENROLLED',
        'StudentTableMap::COL_ENROLLED' => 'ENROLLED',
        'COL_ENROLLED' => 'ENROLLED',
        'students.enrolled' => 'ENROLLED',
        'ClassroomId' => 'CLASSROOM_ID',
        'Student.ClassroomId' => 'CLASSROOM_ID',
        'classroomId' => 'CLASSROOM_ID',
        'student.classroomId' => 'CLASSROOM_ID',
        'StudentTableMap::COL_CLASSROOM_ID' => 'CLASSROOM_ID',
        'COL_CLASSROOM_ID' => 'CLASSROOM_ID',
        'classroom_id' => 'CLASSROOM_ID',
        'students.classroom_id' => 'CLASSROOM_ID',
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
        $this->setName('students');
        $this->setPhpName('Student');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\flapjack\\attend\\database\\Student');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('family_name', 'FamilyName', 'VARCHAR', true, 45, null);
        $this->addColumn('first_name', 'FirstName', 'VARCHAR', true, 45, null);
        $this->addColumn('enrolled', 'Enrolled', 'BOOLEAN', true, 1, null);
        $this->addForeignKey('classroom_id', 'ClassroomId', 'INTEGER', 'classrooms', 'id', false, null, null);
    }

    /**
     * Build the RelationMap objects for this table relationships
     *
     * @return void
     */
    public function buildRelations(): void
    {
        $this->addRelation('Classroom', '\\flapjack\\attend\\database\\Classroom', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':classroom_id',
    1 => ':id',
  ),
), 'RESTRICT', null, null, false);
        $this->addRelation('Schedule', '\\flapjack\\attend\\database\\Schedule', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':student_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'Schedules', false);
    }

    /**
     * Method to invalidate the instance pool of all tables related to students     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool(): void
    {
        // Invalidate objects in related instance pools,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        ScheduleTableMap::clearInstancePool();
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
        return $withPrefix ? StudentTableMap::CLASS_DEFAULT : StudentTableMap::OM_CLASS;
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
     * @return array (Student object, last column rank)
     */
    public static function populateObject(array $row, int $offset = 0, string $indexType = TableMap::TYPE_NUM): array
    {
        $key = StudentTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = StudentTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + StudentTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = StudentTableMap::OM_CLASS;
            /** @var Student $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            StudentTableMap::addInstanceToPool($obj, $key);
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
            $key = StudentTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = StudentTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Student $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                StudentTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(StudentTableMap::COL_ID);
            $criteria->addSelectColumn(StudentTableMap::COL_FAMILY_NAME);
            $criteria->addSelectColumn(StudentTableMap::COL_FIRST_NAME);
            $criteria->addSelectColumn(StudentTableMap::COL_ENROLLED);
            $criteria->addSelectColumn(StudentTableMap::COL_CLASSROOM_ID);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.family_name');
            $criteria->addSelectColumn($alias . '.first_name');
            $criteria->addSelectColumn($alias . '.enrolled');
            $criteria->addSelectColumn($alias . '.classroom_id');
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
            $criteria->removeSelectColumn(StudentTableMap::COL_ID);
            $criteria->removeSelectColumn(StudentTableMap::COL_FAMILY_NAME);
            $criteria->removeSelectColumn(StudentTableMap::COL_FIRST_NAME);
            $criteria->removeSelectColumn(StudentTableMap::COL_ENROLLED);
            $criteria->removeSelectColumn(StudentTableMap::COL_CLASSROOM_ID);
        } else {
            $criteria->removeSelectColumn($alias . '.id');
            $criteria->removeSelectColumn($alias . '.family_name');
            $criteria->removeSelectColumn($alias . '.first_name');
            $criteria->removeSelectColumn($alias . '.enrolled');
            $criteria->removeSelectColumn($alias . '.classroom_id');
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
        return Propel::getServiceContainer()->getDatabaseMap(StudentTableMap::DATABASE_NAME)->getTable(StudentTableMap::TABLE_NAME);
    }

    /**
     * Performs a DELETE on the database, given a Student or Criteria object OR a primary key value.
     *
     * @param mixed $values Criteria or Student object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(StudentTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \flapjack\attend\database\Student) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(StudentTableMap::DATABASE_NAME);
            $criteria->add(StudentTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = StudentQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            StudentTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                StudentTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the students table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(?ConnectionInterface $con = null): int
    {
        return StudentQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Student or Criteria object.
     *
     * @param mixed $criteria Criteria or Student object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed The new primary key.
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ?ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StudentTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Student object
        }

        if ($criteria->containsKey(StudentTableMap::COL_ID) && $criteria->keyContainsValue(StudentTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.StudentTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = StudentQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

}
