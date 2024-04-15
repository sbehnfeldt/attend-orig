<?php

namespace Attend\Database\Base;

use \Exception;
use \PDO;
use Attend\Database\Student as ChildStudent;
use Attend\Database\StudentQuery as ChildStudentQuery;
use Attend\Database\Map\StudentTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'students' table.
 *
 *
 *
 * @method     ChildStudentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildStudentQuery orderByFamilyName($order = Criteria::ASC) Order by the family_name column
 * @method     ChildStudentQuery orderByFirstName($order = Criteria::ASC) Order by the first_name column
 * @method     ChildStudentQuery orderByEnrolled($order = Criteria::ASC) Order by the enrolled column
 * @method     ChildStudentQuery orderByClassroomId($order = Criteria::ASC) Order by the classroom_id column
 *
 * @method     ChildStudentQuery groupById() Group by the id column
 * @method     ChildStudentQuery groupByFamilyName() Group by the family_name column
 * @method     ChildStudentQuery groupByFirstName() Group by the first_name column
 * @method     ChildStudentQuery groupByEnrolled() Group by the enrolled column
 * @method     ChildStudentQuery groupByClassroomId() Group by the classroom_id column
 *
 * @method     ChildStudentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildStudentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildStudentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildStudentQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildStudentQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildStudentQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildStudentQuery leftJoinClassroom($relationAlias = null) Adds a LEFT JOIN clause to the query using the Classroom relation
 * @method     ChildStudentQuery rightJoinClassroom($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Classroom relation
 * @method     ChildStudentQuery innerJoinClassroom($relationAlias = null) Adds a INNER JOIN clause to the query using the Classroom relation
 *
 * @method     ChildStudentQuery joinWithClassroom($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Classroom relation
 *
 * @method     ChildStudentQuery leftJoinWithClassroom() Adds a LEFT JOIN clause and with to the query using the Classroom relation
 * @method     ChildStudentQuery rightJoinWithClassroom() Adds a RIGHT JOIN clause and with to the query using the Classroom relation
 * @method     ChildStudentQuery innerJoinWithClassroom() Adds a INNER JOIN clause and with to the query using the Classroom relation
 *
 * @method     ChildStudentQuery leftJoinAttendance($relationAlias = null) Adds a LEFT JOIN clause to the query using the Attendance relation
 * @method     ChildStudentQuery rightJoinAttendance($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Attendance relation
 * @method     ChildStudentQuery innerJoinAttendance($relationAlias = null) Adds a INNER JOIN clause to the query using the Attendance relation
 *
 * @method     ChildStudentQuery joinWithAttendance($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Attendance relation
 *
 * @method     ChildStudentQuery leftJoinWithAttendance() Adds a LEFT JOIN clause and with to the query using the Attendance relation
 * @method     ChildStudentQuery rightJoinWithAttendance() Adds a RIGHT JOIN clause and with to the query using the Attendance relation
 * @method     ChildStudentQuery innerJoinWithAttendance() Adds a INNER JOIN clause and with to the query using the Attendance relation
 *
 * @method     ChildStudentQuery leftJoinSchedule($relationAlias = null) Adds a LEFT JOIN clause to the query using the Schedule relation
 * @method     ChildStudentQuery rightJoinSchedule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Schedule relation
 * @method     ChildStudentQuery innerJoinSchedule($relationAlias = null) Adds a INNER JOIN clause to the query using the Schedule relation
 *
 * @method     ChildStudentQuery joinWithSchedule($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Schedule relation
 *
 * @method     ChildStudentQuery leftJoinWithSchedule() Adds a LEFT JOIN clause and with to the query using the Schedule relation
 * @method     ChildStudentQuery rightJoinWithSchedule() Adds a RIGHT JOIN clause and with to the query using the Schedule relation
 * @method     ChildStudentQuery innerJoinWithSchedule() Adds a INNER JOIN clause and with to the query using the Schedule relation
 *
 * @method     \Attend\Database\ClassroomQuery|\Attend\Database\AttendanceQuery|\Attend\Database\ScheduleQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildStudent findOne(ConnectionInterface $con = null) Return the first ChildStudent matching the query
 * @method     ChildStudent findOneOrCreate(ConnectionInterface $con = null) Return the first ChildStudent matching the query, or a new ChildStudent object populated from the query conditions when no match is found
 *
 * @method     ChildStudent findOneById(int $id) Return the first ChildStudent filtered by the id column
 * @method     ChildStudent findOneByFamilyName(string $family_name) Return the first ChildStudent filtered by the family_name column
 * @method     ChildStudent findOneByFirstName(string $first_name) Return the first ChildStudent filtered by the first_name column
 * @method     ChildStudent findOneByEnrolled(int $enrolled) Return the first ChildStudent filtered by the enrolled column
 * @method     ChildStudent findOneByClassroomId(int $classroom_id) Return the first ChildStudent filtered by the classroom_id column *
 * @method     ChildStudent requirePk($key, ConnectionInterface $con = null) Return the ChildStudent by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudent requireOne(ConnectionInterface $con = null) Return the first ChildStudent matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildStudent requireOneById(int $id) Return the first ChildStudent filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudent requireOneByFamilyName(string $family_name) Return the first ChildStudent filtered by the family_name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudent requireOneByFirstName(string $first_name) Return the first ChildStudent filtered by the first_name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudent requireOneByEnrolled(int $enrolled) Return the first ChildStudent filtered by the enrolled column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudent requireOneByClassroomId(int $classroom_id) Return the first ChildStudent filtered by the classroom_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildStudent[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildStudent objects based on current ModelCriteria
 * @method     ChildStudent[]|ObjectCollection findById(int $id) Return ChildStudent objects filtered by the id column
 * @method     ChildStudent[]|ObjectCollection findByFamilyName(string $family_name) Return ChildStudent objects filtered by the family_name column
 * @method     ChildStudent[]|ObjectCollection findByFirstName(string $first_name) Return ChildStudent objects filtered by the first_name column
 * @method     ChildStudent[]|ObjectCollection findByEnrolled(int $enrolled) Return ChildStudent objects filtered by the enrolled column
 * @method     ChildStudent[]|ObjectCollection findByClassroomId(int $classroom_id) Return ChildStudent objects filtered by the classroom_id column
 * @method     ChildStudent[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class StudentQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Attend\Database\Base\StudentQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'attend', $modelName = '\\Attend\\Database\\Student', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildStudentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildStudentQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildStudentQuery) {
            return $criteria;
        }
        $query = new ChildStudentQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildStudent|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(StudentTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = StudentTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([
                $key,
                '__toString'
            ]) ? (string)$key : $key)))
        ) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildStudent A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, family_name, first_name, enrolled, classroom_id FROM students WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildStudent $obj */
            $obj = new ChildStudent();
            $obj->hydrate($row);
            StudentTableMap::addInstanceToPool($obj,
                null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string)$key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildStudent|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria    = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria    = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildStudentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(StudentTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildStudentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(StudentTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStudentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id[ 'min' ])) {
                $this->addUsingAlias(StudentTableMap::COL_ID, $id[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id[ 'max' ])) {
                $this->addUsingAlias(StudentTableMap::COL_ID, $id[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the family_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFamilyName('fooValue');   // WHERE family_name = 'fooValue'
     * $query->filterByFamilyName('%fooValue%', Criteria::LIKE); // WHERE family_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $familyName The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStudentQuery The current query, for fluid interface
     */
    public function filterByFamilyName($familyName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($familyName)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentTableMap::COL_FAMILY_NAME, $familyName, $comparison);
    }

    /**
     * Filter the query on the first_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFirstName('fooValue');   // WHERE first_name = 'fooValue'
     * $query->filterByFirstName('%fooValue%', Criteria::LIKE); // WHERE first_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $firstName The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStudentQuery The current query, for fluid interface
     */
    public function filterByFirstName($firstName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($firstName)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentTableMap::COL_FIRST_NAME, $firstName, $comparison);
    }

    /**
     * Filter the query on the enrolled column
     *
     * Example usage:
     * <code>
     * $query->filterByEnrolled(1234); // WHERE enrolled = 1234
     * $query->filterByEnrolled(array(12, 34)); // WHERE enrolled IN (12, 34)
     * $query->filterByEnrolled(array('min' => 12)); // WHERE enrolled > 12
     * </code>
     *
     * @param     mixed $enrolled The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStudentQuery The current query, for fluid interface
     */
    public function filterByEnrolled($enrolled = null, $comparison = null)
    {
        if (is_array($enrolled)) {
            $useMinMax = false;
            if (isset($enrolled[ 'min' ])) {
                $this->addUsingAlias(StudentTableMap::COL_ENROLLED, $enrolled[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($enrolled[ 'max' ])) {
                $this->addUsingAlias(StudentTableMap::COL_ENROLLED, $enrolled[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentTableMap::COL_ENROLLED, $enrolled, $comparison);
    }

    /**
     * Filter the query on the classroom_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClassroomId(1234); // WHERE classroom_id = 1234
     * $query->filterByClassroomId(array(12, 34)); // WHERE classroom_id IN (12, 34)
     * $query->filterByClassroomId(array('min' => 12)); // WHERE classroom_id > 12
     * </code>
     *
     * @see       filterByClassroom()
     *
     * @param     mixed $classroomId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStudentQuery The current query, for fluid interface
     */
    public function filterByClassroomId($classroomId = null, $comparison = null)
    {
        if (is_array($classroomId)) {
            $useMinMax = false;
            if (isset($classroomId[ 'min' ])) {
                $this->addUsingAlias(StudentTableMap::COL_CLASSROOM_ID, $classroomId[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($classroomId[ 'max' ])) {
                $this->addUsingAlias(StudentTableMap::COL_CLASSROOM_ID, $classroomId[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentTableMap::COL_CLASSROOM_ID, $classroomId, $comparison);
    }

    /**
     * Filter the query by a related \Attend\Database\Classroom object
     *
     * @param \Attend\Database\Classroom|ObjectCollection $classroom The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildStudentQuery The current query, for fluid interface
     */
    public function filterByClassroom($classroom, $comparison = null)
    {
        if ($classroom instanceof \Attend\Database\Classroom) {
            return $this
                ->addUsingAlias(StudentTableMap::COL_CLASSROOM_ID, $classroom->getId(), $comparison);
        } elseif ($classroom instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StudentTableMap::COL_CLASSROOM_ID, $classroom->toKeyValue('PrimaryKey', 'Id'),
                    $comparison);
        } else {
            throw new PropelException('filterByClassroom() only accepts arguments of type \Attend\Database\Classroom or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Classroom relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildStudentQuery The current query, for fluid interface
     */
    public function joinClassroom($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Classroom');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Classroom');
        }

        return $this;
    }

    /**
     * Use the Classroom relation Classroom object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Attend\Database\ClassroomQuery A secondary query class using the current class as primary query
     */
    public function useClassroomQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinClassroom($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Classroom', '\Attend\Database\ClassroomQuery');
    }

    /**
     * Filter the query by a related \Attend\Database\Attendance object
     *
     * @param \Attend\Database\Attendance|ObjectCollection $attendance the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildStudentQuery The current query, for fluid interface
     */
    public function filterByAttendance($attendance, $comparison = null)
    {
        if ($attendance instanceof \Attend\Database\Attendance) {
            return $this
                ->addUsingAlias(StudentTableMap::COL_ID, $attendance->getStudentId(), $comparison);
        } elseif ($attendance instanceof ObjectCollection) {
            return $this
                ->useAttendanceQuery()
                ->filterByPrimaryKeys($attendance->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAttendance() only accepts arguments of type \Attend\Database\Attendance or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Attendance relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildStudentQuery The current query, for fluid interface
     */
    public function joinAttendance($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Attendance');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Attendance');
        }

        return $this;
    }

    /**
     * Use the Attendance relation Attendance object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Attend\Database\AttendanceQuery A secondary query class using the current class as primary query
     */
    public function useAttendanceQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAttendance($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Attendance', '\Attend\Database\AttendanceQuery');
    }

    /**
     * Filter the query by a related \Attend\Database\Schedule object
     *
     * @param \Attend\Database\Schedule|ObjectCollection $schedule the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildStudentQuery The current query, for fluid interface
     */
    public function filterBySchedule($schedule, $comparison = null)
    {
        if ($schedule instanceof \Attend\Database\Schedule) {
            return $this
                ->addUsingAlias(StudentTableMap::COL_ID, $schedule->getStudentId(), $comparison);
        } elseif ($schedule instanceof ObjectCollection) {
            return $this
                ->useScheduleQuery()
                ->filterByPrimaryKeys($schedule->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySchedule() only accepts arguments of type \Attend\Database\Schedule or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Schedule relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildStudentQuery The current query, for fluid interface
     */
    public function joinSchedule($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Schedule');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Schedule');
        }

        return $this;
    }

    /**
     * Use the Schedule relation Schedule object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Attend\Database\ScheduleQuery A secondary query class using the current class as primary query
     */
    public function useScheduleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSchedule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Schedule', '\Attend\Database\ScheduleQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildStudent $student Object to remove from the list of results
     *
     * @return $this|ChildStudentQuery The current query, for fluid interface
     */
    public function prune($student = null)
    {
        if ($student) {
            $this->addUsingAlias(StudentTableMap::COL_ID, $student->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the students table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StudentTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            StudentTableMap::clearInstancePool();
            StudentTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StudentTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(StudentTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            StudentTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            StudentTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // StudentQuery
